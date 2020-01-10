<?php
namespace SalePaymentYandex;

use YandexCheckout\Client;

class Gateway extends \Sale\PaymentGateway\GatewayAbstract {
	
	public $SECURITY_TYPE = 'MD5';
		
	public static function getInfo()
	{
		$t = \Cetera\Application::getInstance()->getTranslator();
		
		return [
			'name'        => 'Yandex',
			'description' => '',
			'icon'        => '/plugins/sale_payment_yandex/yandex.png',
			'params' => [	
				[
					'name'       => 'shopId',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Идентификатор магазина *'),
					'allowBlank' => false,
				],	
				[
					'name'       => 'shopSecret',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Секретный ключ *'),
					'allowBlank' => false,
				],                
				[
					'name'       => 'paymentType',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Способ оплаты'),
					'xtype'      => 'combobox',
					'value'      => '',
					'store'      => [
						['',  $t->_('выбор на стороне Яндекс.Кассы')],
						['yandex_money',$t->_('оплата из кошелька в Яндекс.Деньгах')],
						['bank_card',$t->_('оплата с произвольной банковской карты')],
					],
				],
				[
					'name'       => 'orderBundle',
					'xtype'      => 'checkbox',
					'fieldLabel' => 'Передача корзины товаров (кассовый чек 54-ФЗ)',
				],
				[
					'name'       => 'tax_system_code',
					'xtype'      => 'combobox',
					'fieldLabel' => 'Система налогообложения',
					'value'      => 0,
					'store'      => [
						[1, 'общая СН'],
						[2, 'упрощенная СН (доходы)'],
						[3, 'упрощенная СН (доходы минус расходы)'],
						[4, 'единый налог на вмененный доход'],
						[5, 'единый сельскохозяйственный налог'],
						[6, 'патентная СН'],
					],
				], 
				[
					'name'       => 'vat_code',
					'xtype'      => 'combobox',
					'fieldLabel' => 'Ставка НДС для товаров',
					'value'      => 0,
					'store'      => [
						[1, 'без НДС'],
						[2, 'НДС по ставке 0%'],
						[3, 'НДС чека по ставке 10%'],
						[4, 'НДС чека по ставке 20%'],
						[5, 'НДС чека по расчетной ставке 10/110'],
                        [6, 'НДС чека по расчётной ставке 20/120'],
					],
				],                
			]			
		];
	}
	
	public function pay( $return = '' )
	{
        if (!$return) $return = \Cetera\Application::getInstance()->getServer()->getFullUrl();
        
        $paymentData = array(
            'amount' => array(
                'value' => $this->order->getTotal(),
                'currency' => 'RUB',
            ),              
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => $return,
            ),
            'capture' => true,
            'description' => 'Заказ №'.$this->order->id,            
        );
        
        if ($this->params['orderBundle']) {
			$items = [];
			
            $i = 1;
			foreach ($this->order->getProducts() as $p) {
				$items[] = [
                    'description' => $p['name'],
                    'quantity'    => intval($p['quantity']),
                    'itemCode' => $p['id'],
                    "amount" => array(
                        "value" => $p['price'],
                        "currency" => $this->order->getCurrency()->code
                    ),   
                    "vat_code" => $this->params['vat_code'],
				];
			}

            $paymentData['receipt'] = [
                "customer" => array(
                    "full_name" => $this->order->getName(),
                    "phone" => $this->order->getPhone(),
                    "email" => $this->order->getEmail(),
                ),
               "tax_system_code" => $this->params['tax_system_code'],
                "items" => $items
            ];
        }        

        if ($this->params['paymentType']) {
            $paymentData['payment_method_data'] = array(
                'type' => $this->params['paymentType'],
            );
        }
        
        print_r($paymentData);
        
        $client = new Client();
        $client->setAuth($this->params['shopId'], $this->params['shopSecret']);
        $payment = $client->createPayment(
            $paymentData,
            uniqid('', true)
        );  
        
        print_r($payment);
        
	}	
	
}