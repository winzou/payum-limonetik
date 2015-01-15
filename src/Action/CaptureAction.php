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

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Sync;
use winzou\Limonetik\APICLient;
use winzou\PayumLimonetik\Request\AuthorizeToken;
use winzou\PayumLimonetik\Request\ChargeToken;

class CaptureAction extends PaymentAwareAction implements ApiAwareInterface
{
    /**
     * @var APICLient
     */
    protected $api;

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof APICLient) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        /** @var $request Capture */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (empty($model['PaymentPageUrl'])) {
            $model['MerchantUrls'] = array(
                'ReturnUrl'  => $request->getToken()->getTargetUrl(),
                'AbortedUrl' => $request->getToken()->getTargetUrl(),
                'ErrorUrl'   => $request->getToken()->getTargetUrl()
            );

            $this->payment->execute(new AuthorizeToken($model));
        }

        $this->payment->execute(new Sync($model));

        $this->payment->execute(new ChargeToken($model));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
