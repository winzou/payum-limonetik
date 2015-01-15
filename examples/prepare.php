<?php

require '../vendor/autoload.php';
require 'config.php';

$paymentName = 'limonetik';

$storage = $payum->getStorage($orderClass);

$order = $storage->create();
$order->setNumber(uniqid());
$order->setCurrencyCode('EUR');
$order->setTotalAmount(90); // 1.23 EUR
$order->setDescription('A description');
$order->setClientId('anId');
$order->setClientEmail('foo@example.com');
$order->setDetails(array('PaymentPageId' => 'sacarte'));

$storage->update($order);

$captureToken = $tokenFactory->createCaptureToken($paymentName, $order, 'payum-limonetik/examples/done.php');

var_dump($captureToken->getTargetUrl());
