<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\PayumLimonetik\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Sync;
use Payum\Core\Exception\RequestNotSupportedException;
use winzou\PayumLimonetik\Api;

class PaymentDetailsSyncAction extends AbstractApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request Sync */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['PaymentOrderId']) {
            return;
        }

        $model->replace($this->api->PaymentOrderDetail($model['PaymentOrderId']));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Sync
            && $request->getModel() instanceof \ArrayAccess
        ;
    }
}
