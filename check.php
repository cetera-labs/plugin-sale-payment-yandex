<?php
namespace SalePaymentYandex;

$application->connectDb();
$application->initSession();
$application->initPlugins();

$yaMoneyCommonHttpProtocol = new YaMoneyCommonHttpProtocol("checkOrder");
$yaMoneyCommonHttpProtocol->processRequest($_REQUEST);
exit;
?>
