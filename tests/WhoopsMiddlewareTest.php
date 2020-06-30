<?php

namespace Franzl\Middleware\Whoops\Test;

use Exception;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use Middlewares\Utils\Factory;
use Middlewares\Utils\FactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WhoopsMiddlewareTest extends TestCase
{
    public function setUpForError($expectedContentType)
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())->method('withBody')->willReturnSelf();
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', $expectedContentType)
            ->willReturnSelf();
        $responseFactoryMock = $this->createMock(ResponseFactoryInterface::class);
        $responseFactoryMock->expects($this->once())->method('createResponse')->with(500)->willReturn($responseMock);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamFactoryMock = $this->createMock(StreamFactoryInterface::class);
        $streamFactoryMock->expects($this->once())->method('createStream')->willReturn($streamMock);

        $factoryMock = $this->createMock(FactoryInterface::class);
        $factoryMock->expects($this->once())->method('getResponseFactory')->willReturn($responseFactoryMock);
        $factoryMock->expects($this->once())->method('getStreamFactory')->willReturn($streamFactoryMock);

        /** @var FactoryInterface $factoryMock */
        Factory::setFactory($factoryMock);
    }

    public function test_successful_request_is_left_untouched()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        /**
         * @var ServerRequestInterface $requestMock
         * @var ResponseInterface $responseMock
         */
        $response = (new WhoopsMiddleware)->process(
            $requestMock,
            $this->handlerThatReturns($responseMock)
        );

        $this->assertSame($responseMock, $response);
    }

    public function test_exception_is_handled()
    {
        $this->setUpForError('text/html');
        (new WhoopsMiddleware)->process(
            $this->requestWithAccept('text/html'),
            $this->handlerThatThrowsException()
        );
    }

    /**
     * @dataProvider knownTypes
     */
    public function test_known_mime_types_will_return_preferred_content_type($mime, $expectedContentType)
    {
        $this->setUpForError($expectedContentType);
        (new WhoopsMiddleware)->process(
            $this->requestWithAccept($mime),
            $this->handlerThatThrowsException()
        );
    }

    public function knownTypes()
    {
        yield ['text/html', 'text/html'];
        yield ['application/xhtml+xml', 'text/html'];
        yield ['application/json', 'application/json'];
        yield ['text/json', 'application/json'];
        yield ['application/x-json', 'application/json'];
        yield ['text/xml', 'text/xml'];
        yield ['application/xml', 'text/xml'];
        yield ['application/x-xml', 'text/xml'];
        yield ['text/plain', 'text/plain'];
    }

    public function test_multiple_mime_types_will_prefer_the_first_match()
    {
        $this->setUpForError('text/xml');
        (new WhoopsMiddleware)->process(
            $this->requestWithAccept('application/xml, application/json'),
            $this->handlerThatThrowsException()
        );
    }

    public function test_multiple_mime_types_will_prefer_the_first_match_reversed()
    {
        // Test vice versa to avoid false positives
        $this->setUpForError('application/json');
        (new WhoopsMiddleware)->process(
            $this->requestWithAccept('application/json, application/xml'),
            $this->handlerThatThrowsException()
        );
    }

    public function test_unknown_mime_types_will_fall_back_to_plain_text()
    {
        $this->setUpForError('text/plain');

        (new WhoopsMiddleware)->process(
            $this->requestWithAccept('foo/bar, x/custom'),
            $this->handlerThatThrowsException()
        );
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

    /**
     * @return ServerRequestInterface
     */
    private function requestWithAccept($acceptHeader)
    {
        $headers = explode(',', $acceptHeader);
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('getHeader')->with('accept')->willReturn($headers);
        return $requestMock;
    }
}
