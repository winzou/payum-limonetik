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
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use winzou\Limonetik\APIClient;
use winzou\PayumLimonetik\Request\AuthorizeToken;

class AuthorizeActionSpec extends ObjectBehavior
{
    protected $apiReturn = array('PaymentPageUrl' => 'http://localhost/payment_page_url');

    function let(APIClient $api)
    {
        $this->setApi($api);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('winzou\PayumLimonetik\Action\AuthorizeAction');
    }

    function it_should_create_order(AuthorizeToken $request, APIClient $api)
    {
        $model = new ArrayObject(array());;
        $request->getModel()->willReturn($model);

        $api->PaymentOrderCreate($model->toUnsafeArray())->shouldBeCalled()->willReturn($this->apiReturn);

        $this->shouldThrow('Payum\Core\Reply\HttpRedirect')->during('execute', array($request));
    }

    function it_should_not_create_order_if_url_is_present(AuthorizeToken $request, APIClient $api)
    {
        $model = new ArrayObject(array('PaymentPageUrl' => 'http://my-url.com'));;
        $request->getModel()->willReturn($model);

        $api->PaymentOrderCreate($model->toUnsafeArray())->shouldNotBeCalled();

        $this->shouldThrow('Payum\Core\Reply\HttpRedirect')->during('execute', array($request));
    }

    function it_should_not_accept_invalid_request(APIClient $api)
    {
        $api->PaymentOrderCreate(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow('Payum\Core\Exception\RequestNotSupportedException')->during('execute', array($api));
    }
}
