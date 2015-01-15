<?php

/*
 * This file is part of the PayumLimonetik package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\winzou\PayumLimonetik\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetStatusInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use winzou\Limonetik\APIClient;

class StatusActionSpec extends ObjectBehavior
{
    protected $apiReturn = array('PaymentPageUrl' => 'http://localhost/payment_page_url');

    function it_is_initializable()
    {
        $this->shouldHaveType('winzou\PayumLimonetik\Action\StatusAction');
    }

    function it_should_mark_new_if_empty_model(GetStatusInterface $request)
    {
        $model = new ArrayObject(array());
        $request->getModel()->willReturn($model);

        $request->markNew()->shouldBeCalled();

        $this->execute($request);
    }

    function it_should_mark_captured(GetStatusInterface $request)
    {
        $model = new ArrayObject(array('PaymentOrder' => array('Status' => APIClient::STATUS_CHARGED)));
        $request->getModel()->willReturn($model);

        $request->markCaptured()->shouldBeCalled();

        $this->execute($request);
    }
}
