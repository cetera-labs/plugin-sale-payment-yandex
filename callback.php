<?php
use YandexCheckout\Model\Notification\NotificationSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;
use YandexCheckout\Model\NotificationEventType;

$application->connectDb();
$application->initSession();
$application->initPlugins();

try {
    
    $source = file_get_contents('php://input');
    $requestBody = json_decode($source, true);
        
    if ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED) {
        $notification = new NotificationSucceeded($requestBody);
        $payment = $notification->getObject();        
    }
    else {
        throw new \Exception('Event not permitted');
    }  

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
		$receipt = $gateway->getReciept();
		$receipt['payment_id'] = $payment->id;
		
		$client = new \SalePaymentYandex\Client();
		$client->setAuth($gateway->params['shopId'], $gateway->params['shopSecret']);
		$resp = $client->createReceiptNew(
			$receipt,
			uniqid('', true)
		);

		//file_put_contents(__DIR__.'/log'.time().'.txt', var_export($receipt, true));
	}
	
	$order->paymentSuccess();
    
	header("HTTP/1.1 200 OK");
	print 'OK';		    
    
}
catch (\Exception $e) {
	
	header("HTTP/1.1 500 ".$e->getMessage());
	print $e->getMessage();
	
}