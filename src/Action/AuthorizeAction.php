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
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Exception\RequestNotSupportedException;
use winzou\PayumLimonetik\Request\AuthorizeToken;

class AuthorizeAction extends AbstractApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @throws HttpRedirect
     */
    public function execute($request)
    {
        /** @var $request AuthorizeToken */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model['PaymentPageUrl']) {
            $model->replace($this->api->PaymentOrderCreate($model->toUnsafeArray()));
        }

        throw new HttpRedirect($model['PaymentPageUrl']);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof AuthorizeToken &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
