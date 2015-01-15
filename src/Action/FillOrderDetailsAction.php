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
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\FillOrderDetails;

class FillOrderDetailsAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param FillOrderDetails $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $order = $request->getOrder();

        $details = $order->getDetails();

        $details['Amount'] = $order->getTotalAmount();
        $details['Currency'] = $order->getCurrencyCode();

        $details['MerchantOrder']['TotalAmount'] = $order->getTotalAmount();
        $details['MerchantOrder']['Currency'] = $order->getCurrencyCode();

        $details['MerchantOrder']['Customer']['Id'] = $order->getClientId();
        $details['MerchantOrder']['Customer']['Email'] = $order->getClientEmail();

        $order->setDetails($details);
    }
    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof FillOrderDetails;
    }
}
