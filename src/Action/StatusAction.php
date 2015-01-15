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

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use winzou\Limonetik\APIClient;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = new ArrayObject($request->getModel());

        switch ($model['PaymentOrder']['Status']) {
            case null:
            case APIClient::STATUS_CREATED:
            case APIClient::STATUS_PROGRESS:
                $request->markNew();
                break;
            case APIClient::STATUS_ABORTED:
            case APIClient::STATUS_CANCELLED:
                $request->markCanceled();
                break;
            case APIClient::STATUS_REFUSED:
                $request->markFailed();
                break;
            case APIClient::STATUS_AUTHORIZED:
                $request->markAuthorized();
                break;
            case APIClient::STATUS_AUTHORIZING:
                $request->markPending();
                break;
            case APIClient::STATUS_CHARGED:
                $request->markCaptured();
                break;
            case APIClient::STATUS_REFUNDED:
                $request->markRefunded();
                break;
            case APIClient::STATUS_ERROR:
                $request->markUnknown();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
