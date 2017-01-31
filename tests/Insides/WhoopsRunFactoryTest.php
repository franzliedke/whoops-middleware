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

    public function testCreateNewInstance()
    {
        $this->assertInstanceOf(WhoopsRunFactory::class, WhoopsRunFactory::createNewInstance());
    }

    public function testCreateInstanceCli()
    {
        // configure mocks
        $this->cliDetector->method('isCli')->willReturn(true);

        // actual test
        $run = (new WhoopsRunFactory($this->cliDetector, $this->formatNegotiator))->getWhoopsInstance($this->serverRequest);
        $this->assertInstanceOf(Run::class, $run);
        $this->assertInstanceOf(PlainTextHandler::class, $run->getHandlers()[0]);
    }

    public function testCreateInstanceJson()
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

    public function testCreateInstanceHtml()
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

    public function testCreateInstanceXml()
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

    public function testCreateInstanceText()
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

    public function testCreateInstanceDefaultEmpty()
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

    public function testCreateInstanceDefault()
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

    protected function setUp()
    {
        $this->cliDetector = $this->getMockBuilder(CliDetector::class)->getMock();
        $this->formatNegotiator = $this->getMockBuilder(FormatNegotiator::class)->getMock();
        $this->serverRequest = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
    }

}
