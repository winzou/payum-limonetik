<?php
//config.php

use Payum\Core\Bridge\Buzz\ClientFactory;
use Payum\Core\Bridge\PlainPhp\Security\HttpRequestVerifier;
use Payum\Core\Bridge\PlainPhp\Security\TokenFactory;
use Payum\Core\Registry\SimpleRegistry;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Storage\FilesystemStorage;
use winzou\PayumLimonetik\LimonetikPaymentFactory;

$curl = ClientFactory::createCurl();
// Uncomment this if you're facing some SSL issues on Windows -- for development purpose only!
// $curl->setVerifyPeer(false);

$orderClass = 'Payum\Core\Model\Order';

$storages = array(
    $orderClass => new FilesystemStorage('./order', $orderClass, 'number')
);

$paymentFactory = new LimonetikPaymentFactory(array(
    'merchant_id' => 'your-id',
    'key'         => 'your-key',
    'sandbox'     => true,
    'buzz.client' => $curl
));

$payments = array();
$payments['limonetik'] = $paymentFactory->create();

$payum = new SimpleRegistry($payments, $storages);

$tokenStorage = new FilesystemStorage('./token', 'Payum\Core\Model\Token', 'hash');

$requestVerifier = new HttpRequestVerifier($tokenStorage);

$tokenFactory = new GenericTokenFactory(
    new TokenFactory($tokenStorage, $payum),
    array(
        'capture'   => 'payum-limonetik/examples/capture.php',
        'notify'    => 'payum-limonetik/examples/notify.php',
        'authorize' => 'payum-limonetik/examples/authorize.php',
        'refund'    =>'payum-limonetik/examples/refund.php'
    )
);
