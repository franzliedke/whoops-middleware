<?php

namespace Franzl\Middleware\Whoops\Test;

use Franzl\Middleware\Whoops\WhoopsRunFactoryInterface;
use Franzl\Middleware\Whoops\WhoopsRunner;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\RunInterface;

class WhoopsRunnerTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateNewInstance()
    {
        $this->assertInstanceOf(WhoopsRunner::class, WhoopsRunner::newInstance());
    }

    public function testHandle()
    {
        $exception = new \Exception();

        // configure mocks
        $whoopsRun = $this->getMockBuilder(RunInterface::class)->getMock();
        $whoopsRun->method('allowQuit')->with($this->equalTo(false));
        $whoopsRun->method('handleException')->with($this->equalTo($exception));
        $whoopsRunFactory = $this->getMockBuilder(WhoopsRunFactoryInterface::class)->getMock();
        $whoopsRunFactory->method('getWhoopsInstance')->willReturn($whoopsRun);
        $serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        // actual tests
        $whoopsRunner = new WhoopsRunner($whoopsRunFactory);
        $response = $whoopsRunner->handle($exception, $serverRequest);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(null, $response->getBody()->getContents());
    }

}
