<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\PayumLimonetik\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Sync;
use winzou\PayumLimonetik\Action\AbstractApiAwareAction;
use winzou\PayumLimonetik\Request\ChargeToken;

class ChargeTokenAction extends AbstractApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request ChargeToken */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ('Authorized' === $model['PaymentOrder']['Status']) {
            $this->api->PaymentOrderCharge($model['PaymentOrderId'], $model['Amount'], $model['Currency']);

            $this->payment->execute(new Sync($model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof ChargeToken &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
