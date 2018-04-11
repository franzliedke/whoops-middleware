<?php

namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Middleware class for PSR-15 middleware
 */
class PSR15Middleware implements Middleware
{
    /**
     * Process an incoming server request and return a response, optionally
     * delegating response creation to a handler.
     */
    public function process(Request $request, Handler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            return WhoopsRunner::handle($e, $request);
        }
    }
}
