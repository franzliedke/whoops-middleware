<?php

namespace Franzl\Middleware\Whoops\Test;

use Exception;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;

class WhoopsMiddlewareTest extends TestCase
{
    public function testProcess()
    {
        $response = (new WhoopsMiddleware)->process(
            new ServerRequest,
            $this->handlerThatReturns(new TextResponse('Success!'))
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success!', $response->getBody());
    }

    public function testProcessWithException()
    {
        $response = (new WhoopsMiddleware)->process(
            new ServerRequest,
            $this->handlerThatThrowsException()
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('text/html', $response->getHeaderLine('content-type'));
    }

    public function testProcessWithExceptionWhenRequestWantsJson()
    {
        $response = (new WhoopsMiddleware)->process(
            (new ServerRequest)->withHeader('Accept', 'application/json'),
            $this->handlerThatThrowsException()
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }

    public function testProcessWithExceptionWhenRequestWantsPlainText()
    {
        $response = (new WhoopsMiddleware)->process(
            (new ServerRequest)->withHeader('Accept', 'text/plain'),
            $this->handlerThatThrowsException()
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->getHeaderLine('content-type'));
    }

    public function testProcessWithExceptionWhenRequestWantsXml()
    {
        $response = (new WhoopsMiddleware)->process(
            (new ServerRequest)->withHeader('Accept', 'application/xml'),
            $this->handlerThatThrowsException()
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('text/xml', $response->getHeaderLine('content-type'));
    }

    private function handlerThatReturns(ResponseInterface $response)
    {
        return new class($response) implements RequestHandlerInterface {
            public function __construct($response) {
                $this->response = $response;
            }
            public function handle(ServerRequestInterface $request): ResponseInterface {
                return $this->response;
            }
        };
    }

    private function handlerThatThrowsException()
    {
        return new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface {
                throw new Exception;
            }
        };
    }
}
