<?php

namespace Franzl\Middleware\Whoops;

use Franzl\Middleware\Whoops\Insides\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ErrorMiddleware class for use with Zend's Stratigility middleware pipe
 */
class ErrorMiddleware extends AbstractMiddleware
{

    /**
     * @param $error
     * @param Request $request
     * @param Response $response
     * @param callable $out
     * @return \Zend\Diactoros\Response\HtmlResponse
     */
    public function __invoke($error, Request $request, Response $response, callable $out)
    {
        return $this->whoopsRun->handle($error, $request);
    }

}
