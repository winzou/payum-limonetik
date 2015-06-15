<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\PayumLimonetik;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\PaymentFactory as CorePaymentFactory;
use Payum\Core\PaymentFactoryInterface;
use winzou\Limonetik\APIClient;
use winzou\PayumLimonetik\Action\Api\AuthorizeTokenAction;
use winzou\PayumLimonetik\Action\Api\ChargeTokenAction;
use winzou\PayumLimonetik\Action\CaptureAction;
use winzou\PayumLimonetik\Action\FillOrderDetailsAction;
use winzou\PayumLimonetik\Action\PaymentDetailsSyncAction;
use winzou\PayumLimonetik\Action\StatusAction;

class LimonetikPaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $corePaymentFactory;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param array $defaultConfig
     * @param PaymentFactoryInterface $corePaymentFactory
     */
    public function __construct(array $defaultConfig = array(), PaymentFactoryInterface $corePaymentFactory = null)
    {
        $this->corePaymentFactory = $corePaymentFactory ?: new CorePaymentFactory();
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config = array())
    {
        return $this->corePaymentFactory->create($this->createConfig($config));
    }

    /**
     * {@inheritDoc}
     */
    public function createConfig(array $config = array())
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->corePaymentFactory->createConfig());

        $config->defaults(array(
            'payum.factory_name' => 'limonetik',
            'payum.factory_title' => 'Limonetik',

            'payum.action.capture'            => new CaptureAction(),
            'payum.action.fill_order_details' => new FillOrderDetailsAction(),
            'payum.action.status'             => new StatusAction(),
            'payum.action.sync'               => new PaymentDetailsSyncAction(),
            'payum.action.authorize'          => new AuthorizeTokenAction(),
            'payum.action.charge'             => new ChargeTokenAction(),
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'merchantId' => null,
                'key'        => null,
                'sandbox'   => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array('merchant_id', 'key', 'sandbox');

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validatedKeysSet($config['payum.required_options']);

                $apiConfig = array(
                    'merchantId' => $config['merchant_id'],
                    'key'        => $config['key'],
                    'sandbox'    => $config['sandbox'],
                );

                return new APIClient($apiConfig, $config['buzz.client']);
            };
        }

        return (array) $config;
    }
}
