<?php

namespace Franzl\Middleware\Whoops\Test;

use Exception;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WhoopsMiddlewareTest extends TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|RequestHandlerInterface */
    private $handler;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseInterface */
    private $response;
    /** @var WhoopsMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->middleware = new WhoopsMiddleware;
        $this->serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        $this->response = $this->getMockBuilder(ResponseInterface::class)->setMethods([
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
        $this->handler->method('handle')->willReturn($this->response);

        $response = $this->middleware->process($this->serverRequest, $this->handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success!', $response->getBody());
    }

    public function testProcessWithException()
    {
        $this->handler->method('handle')->willThrowException(new Exception());

        $response = $this->middleware->process($this->serverRequest, $this->handler);

        $this->assertEquals(500, $response->getStatusCode());
    }
}
