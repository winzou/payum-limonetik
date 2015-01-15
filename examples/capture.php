<?php

require '../vendor/autoload.php';
require 'config.php';

use Payum\Core\Request\Capture;
use Payum\Core\Reply\HttpRedirect;

$token = $requestVerifier->verify($_REQUEST);

$payment = $payum->getPayment($token->getPaymentName());

$capture = new Capture($token);

if ($reply = $payment->execute($capture, true)) {
    if ($reply instanceof HttpRedirect) {
        var_dump($reply->getUrl());
        die();
    }

    throw new \LogicException('Unsupported reply', null, $reply);
}

$requestVerifier->invalidate($token);

var_dump($token->getAfterUrl());
