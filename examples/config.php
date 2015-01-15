<?php

use Payum\Core\Bridge\Buzz\ClientFactory;
use Payum\Core\Registry\SimpleRegistry;
use Payum\Core\Security\PlainHttpRequestVerifier;
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
    'merchantId'  => 'your-id',
    'key'         => 'your-key',
    'sandbox'     => true,
    'buzz.client' => $curl
));

$payments = array();
$payments['limonetik'] = $paymentFactory->create();

$payum = new SimpleRegistry($payments, $storages);

$tokenStorage = new FilesystemStorage('./token', 'Payum\Core\Model\Token', 'hash');

$requestVerifier = new PlainHttpRequestVerifier($tokenStorage);

$tokenFactory = new GenericTokenFactory(
    $tokenStorage,
    $payum,
    'https://localhost',
    'payum-limonetik/examples/capture.php',
    'payum-limonetik/examples/notify.php',
    'payum-limonetik/examples/authorize.php',
    'payum-limonetik/examples/refund.php'
);
