<?php

namespace Franzl\Middleware\Whoops\Test;

use Franzl\Middleware\Whoops\Middleware;
use Franzl\Middleware\Whoops\WhoopsRunFactoryInterface;
use Franzl\Middleware\Whoops\WhoopsRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ResponseInterface */
    private $response;
    /** @var \PHPUnit_Framework_MockObject_MockObject|WhoopsRunFactoryInterface */
    private $whoopsRunFactory;
    /** @var \PHPUnit_Framework_MockObject_MockObject|WhoopsRunner */
    private $whoopsRunner;
    /** @var Middleware */
    private $middleware;

    public function testNewInstance()
    {
        $this->assertInstanceOf(Middleware::class, Middleware::newInstance());
    }

    public function testInvoke()
    {
        // configure mocks
        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getBody')->willReturn('Success!');

        // actual test
        $response = $this->middleware->__invoke(
            $this->serverRequest,
            $this->response,
            function (ServerRequestInterface $request, ResponseInterface $response) {
                return $response;
            }
        );
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success!', $response->getBody());
    }

    public function testInvokeException()
    {
        $exception = new \Exception();

        // configure mocks
        $this->response->method('getStatusCode')->willReturn(500);
        $this->response->method('getBody')->willReturn('Exception!');
        $this->whoopsRunner->method('handle')
            ->with($this->equalTo($exception), $this->equalTo($this->serverRequest))
            ->willReturn($this->response);

        // actual test
        $response = $this->middleware->__invoke(
            $this->serverRequest,
            $this->response,
            function (ServerRequestInterface $request, ResponseInterface $response) use ($exception) {
                throw $exception;
            }
        );
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Exception!', $response->getBody());
    }

    protected function setUp()
    {
        $this->serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->whoopsRunFactory = $this->getMockBuilder(WhoopsRunFactoryInterface::class)->getMock();
        $this->whoopsRunFactory->method('getWhoopsInstance');
        $this->whoopsRunner = $this->getMockBuilder(WhoopsRunner::class)
            ->setConstructorArgs([$this->whoopsRunFactory])
            ->getMock();
        $this->middleware = new Middleware($this->whoopsRunner);
    }
}
