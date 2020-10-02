<?php
use YandexCheckout\Model\Notification\NotificationSucceeded;
use YandexCheckout\Model\Notification\NotificationRefundSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;
use YandexCheckout\Model\NotificationEventType;

$application->connectDb();
$application->initSession();
$application->initPlugins();

try {
    
    $source = file_get_contents('php://input');
	file_put_contents(__DIR__.'/log_source'.time().'.txt', $source);
	
    $requestBody = json_decode($source, true);
        
    if ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED) {
        
        // успешный платеж
        $notification = new NotificationSucceeded($requestBody);
        $payment = $notification->getObject();  

        if (!isset( $payment->metadata['order_id'] )) {
            throw new \Exception('order_id not provided in request body');
        }    
        
        $order = \Sale\Order::getById( $payment->metadata['order_id'] );
        $gateway = $order->getPaymentGateway();
        
        $oid = $gateway->getOrderByTransaction( $payment->id );
        if ($oid != $order->id) {
            throw new \Exception('Order check failed');
        } 

        if ($gateway->params['orderBundle'] && $gateway->params['receiptAfterPayment']) {
            
            $client = new \SalePaymentYandex\Client();
            $client->setAuth($gateway->params['shopId'], $gateway->params['shopSecret']);        
            
            $resp = $client->getReceipts([
                'payment_id' => $payment->id,
            ]);

            if (count($resp->getItems()) == 0) {
            
                $receipt = $gateway->getReciept();
                $receipt['payment_id'] = $payment->id;
                
                $resp = $client->createReceiptNew(
                    $receipt,
                    uniqid('', true)
                );
            
            }

            //file_put_contents(__DIR__.'/log_payment'.time().'.txt', var_export($receipt, true));
        }

        $order->paymentSuccess();
        
    }
    elseif ($requestBody['event'] === NotificationEventType::REFUND_SUCCEEDED) {
        
        // успешный возврат
        $notification = new NotificationRefundSucceeded($requestBody);
        $refund = $notification->getObject();
        
        $oid = $gateway->getOrderByTransaction( $refund->getPaymentId() );
        $order = \Sale\Order::getById( $oid );
        $gateway = $order->getPaymentGateway();
        
        if ($gateway->params['orderBundle'] && $gateway->params['receiptAfterPayment']) {
            
            $client = new \SalePaymentYandex\Client();
            $client->setAuth($gateway->params['shopId'], $gateway->params['shopSecret']);        
                        
            $receipt = $gateway->getReciept();
            $receipt['refund_id'] = $refund->id;
            $receipt['type'] = 'refund';
            
            $resp = $client->createReceiptNew(
                $receipt,
                uniqid('', true)
            );

            file_put_contents(__DIR__.'/log_refund'.time().'.txt', var_export($receipt, true));
        }        
        
        $order->setPaid(\Sale\Order::PAY_REFUND)->save();
        
    }
    else {
        throw new \Exception('Event not permitted');
    }  

    
	header("HTTP/1.1 200 OK");
	print 'OK';		    
    
}
catch (\Exception $e) {
	
	header("HTTP/1.1 500 ".$e->getMessage());
	print $e->getMessage();
	
	file_put_contents(__DIR__.'/log_error'.time().'.txt', $e->getMessage());
	
}