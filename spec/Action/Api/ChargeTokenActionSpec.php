<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\winzou\PayumLimonetik\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Payment;
use Payum\Core\Request\Sync;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use winzou\Limonetik\APIClient;
use winzou\PayumLimonetik\Request\ChargeToken;

class ChargeTokenActionSpec extends ObjectBehavior
{
    function let(APIClient $api, Payment $payment)
    {
        $this->setApi($api);
        $this->setPayment($payment);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('winzou\PayumLimonetik\Action\Api\ChargeTokenAction');
    }

    function it_should_charge(ChargeToken $request, APIClient $api, Payment $payment)
    {
        $model = new ArrayObject(array(
            'PaymentOrder' => array('Status' => APIClient::STATUS_AUTHORIZED),
            'PaymentOrderId' => 456,
            'Amount' => 123,
            'Currency' => 'EUR'
        ));
        $request->getModel()->willReturn($model);

        $api->PaymentOrderCharge(456, 123, 'EUR')->shouldBeCalled();
        $payment->execute(new Sync($model))->shouldBeCalled();

        $this->execute($request);
    }

    function it_should_not_charge_if_token_is_not_authorized(ChargeToken $request, APIClient $api, Payment $payment)
    {
        $model = new ArrayObject(array(
            'PaymentOrder' => array('Status' => APIClient::STATUS_PROGRESS),
            'PaymentOrderId' => 456,
            'Amount' => 123,
            'Currency' => 'EUR'
        ));
        $request->getModel()->willReturn($model);

        $api->PaymentOrderCharge(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $payment->execute(Argument::any())->shouldNotBeCalled();

        $this->execute($request);
    }

    function it_should_not_accept_invalid_request(APIClient $api)
    {
        $api->PaymentOrderCreate(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow('Payum\Core\Exception\RequestNotSupportedException')->during('execute', array($api));
    }
}
