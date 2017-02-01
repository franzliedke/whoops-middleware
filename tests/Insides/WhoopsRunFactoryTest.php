<?php

namespace Franzl\Middleware\Whoops\Test;

use Franzl\Middleware\Whoops\Insides\CliDetector;
use Franzl\Middleware\Whoops\Insides\FormatNegotiator;
use Franzl\Middleware\Whoops\Insides\WhoopsRunFactory;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\XmlResponseHandler;
use Whoops\Run;

class WhoopsRunFactoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PHPUnit_Framework_MockObject_MockObject|CliDetector */
    private $cliDetector;
    /** @var \PHPUnit_Framework_MockObject_MockObject|FormatNegotiator */
    private $formatNegotiator;
    /** @var \PHPUnit_Framework_MockObject_MockObject|ServerRequestInterface */
    private $serverRequest;

    protected function setUp()
    {
        $this->cliDetector = $this->getMockBuilder(CliDetector::class)->getMock();
        $this->formatNegotiator = $this->getMockBuilder(FormatNegotiator::class)->getMock();
        $this->serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
    }

    public function testNewInstance()
    {
        $this->assertInstanceOf(WhoopsRunFactory::class, WhoopsRunFactory::newInstance());
    }

    public function testGetWhoopsInstanceCli()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(true);

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PlainTextHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceJson()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn('json');

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(JsonResponseHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceHtml()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn('html');

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PrettyPageHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceXml()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn('xml');

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(XmlResponseHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceText()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn('txt');

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PlainTextHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceDefaultEmpty()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn(null);

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PrettyPageHandler::class, $run->getHandlers()[0]);
    }

    public function testGetWhoopsInstanceDefault()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(false);
        $this->formatNegotiator
            ->method('getPreferredFormat')
            ->with($this->equalTo($this->serverRequest))
            ->willReturn('default');

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PlainTextHandler::class, $run->getHandlers()[0]);
    }

}
