<?php
namespace SalePaymentYandex;

$application->connectDb();
$application->initSession();
$application->initPlugins();

$yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("paymentAviso");
$yaMoneyCommonHttpProtocol->processRequest($_REQUEST);
exit;
?>
