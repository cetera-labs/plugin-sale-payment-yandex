<?php
namespace SalePaymentYandex;

class Gateway extends \Sale\PaymentGateway\GatewayAbstract {
	
	public $SECURITY_TYPE = 'MD5';
		
	public static function getInfo()
	{
		$t = \Cetera\Application::getInstance()->getTranslator();
		
		return array(
			'name'        => 'Yandex',
			'description' => '',
			'icon'        => '/plugins/sale_payment_yandex/yandex.png',
			'params' => array(
				array(
					'xtype'      => 'displayfield',
					'value'      => '<div style="text-align: right"><a href="/plugins/sale_payment_yandex/help/index.html" target="_blank">Справка</a></div>'
				),			
				array(
					'name'       => 'shopId',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Идентификатор магазина (shopId) *'),
					'allowBlank' => false,
				),	
				array(
					'name'       => 'scid',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Идентификатор витрины (scid) *'),
					'allowBlank' => false,
				),					
				array(
					'name'       => 'paymentType',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Способ оплаты'),
					'xtype'      => 'combobox',
					'value'      => '',
					'store'      => [
						['',  $t->_('выбор на стороне Яндекс.Кассы')],
						['PC',$t->_('оплата из кошелька в Яндекс.Деньгах')],
						['AC',$t->_('оплата с произвольной банковской карты')],
					],
				),
				array(
					'name'       => 'shop_password',
					'xtype'      => 'textfield',
					'fieldLabel' => $t->_('Пароль магазина'),
					'allowBlank' => true,
				),		
				array(
					'name'       => 'test',
					'xtype'      => 'checkbox',
					'fieldLabel'   => $t->_('Режим отладки'),
				),
				/*
				array(
					'xtype'      => 'displayfield',
					'fieldLabel' => $t->_('URL-адрес для IPN уведомлений'),
					'value'      => 'http://'.$_SERVER['HTTP_HOST'].'/plugins/sale_payment_paypal/ipn.php'
				),	
				*/				
				
			)			
		);
	}
	
	public function pay( $return = '' )
	{
		$payNowUrl = $this->params['test']?'https://demomoney.yandex.ru/eshop.xml':'https://money.yandex.ru/eshop.xml';
		$shopId = $this->params['shopId'];
		$scid = $this->params['scid'];
                $paymentType = $this->params['paymentType'];
		$sum = $this->order->getTotal();
		$customerNumber = $this->order->user->email;
		$orderNumber = $this->order->id;
		
		print <<<END
			<form action="$payNowUrl" method="post" name="pay">
				<input name="shopId" value="$shopId" type="hidden"/>
				<input name="scid" value="$scid" type="hidden"/>
				<input name="sum" value="$sum" type="hidden">
				<input name="paymentType" value="$paymentType" type="hidden">
				<input name="customerNumber" value="$customerNumber" type="hidden"/>
				<input name="orderNumber" value="$orderNumber" type="hidden"/>
			</form>	
			<script>pay.submit();</script>
END;
	}	
	
}