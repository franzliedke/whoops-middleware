<?php

namespace Franzl\Middleware\Whoops\Test;

use Exception;
use Franzl\Middleware\Whoops\PSR15Middleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PSR15MiddlewareTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject|DelegateInterface */
    private $delegate;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseInterface */
    private $response;
    /** @var PSR15Middleware */
    private $middleware;

    protected function setUp()
    {
        $this->middleware = new PSR15Middleware();
        $this->serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->delegate = $this->getMockBuilder(DelegateInterface::class)->setMethods(['process'])->getMock();
        $this->response = $this->getMockBuilder(ResponseInterface::class)->setMethods(
            [
                'getStatusCode',
                'withStatus',
                'getReasonPhrase',
                'getProtocolVersion',
                'withProtocolVersion',
                'getHeaders',
                'hasHeader',
                'getHeader',
                'getHeaderLine',
                'withHeader',
                'withAddedHeader',
                'withoutHeader',
                'getBody',
                'withBody'
            ])->getMock();
    }

    public function testProcess()
    {
        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getBody')->willReturn('Success!');
        $this->delegate->method('process')->willReturn($this->response);

        $response = $this->middleware->process($this->serverRequest, $this->delegate);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success!', $response->getBody());
    }

    public function testProcessWithException()
    {
        $this->delegate->method('process')->willThrowException(new Exception());

        $response = $this->middleware->process($this->serverRequest, $this->delegate);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
