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
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\UnsupportedApiException;
use winzou\Limonetik\APICLient;

abstract class AbstractApiAwareAction extends PaymentAwareAction implements ApiAwareInterface
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
        if (false == $api instanceof APIClient) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }
}
