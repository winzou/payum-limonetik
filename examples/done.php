<?php

require '../vendor/autoload.php';
require 'config.php';

use Payum\Core\Request\GetHumanStatus;

$token = $requestVerifier->verify($_REQUEST);

$payment = $payum->getPayment($token->getPaymentName());

// you can invalidate the token. The url could not be requested any more.
// $requestVerifier->invalidate($token);

// Once you have token you can get the model from the storage directly. 
//$identity = $token->getDetails();
//$order = $payum->getStorage($identity->getClass())->find($identity);

// or Payum can fetch the model for you while executing a request (Preferred).
$payment->execute($status = new GetHumanStatus($token));
$order = $status->getFirstModel();

header('Content-Type: application/json');
print_r(array(
    'status' => $status->getValue(),
    'order' => array(
        'total_amount' => $order->getTotalAmount(),
        'currency_code' => $order->getCurrencyCode(),
        'details' => $order->getDetails(),
    ),
));
