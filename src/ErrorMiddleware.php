<?php

namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Stratigility\ErrorMiddlewareInterface;

/**
 * ErrorMiddleware class for use with Zend's Stratigility middleware pipe
 */
class ErrorMiddleware implements ErrorMiddlewareInterface
{
    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        return WhoopsRunner::handle($error);
    }
}
