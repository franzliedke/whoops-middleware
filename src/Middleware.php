<?php

namespace Franzl\Middleware\Whoops;

use Franzl\Middleware\Whoops\Insides\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Middleware class for "typical" PSR-7 middleware
 */
class Middleware extends AbstractMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable|null $next
     * @return \Zend\Diactoros\Response\HtmlResponse
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        try {
            return $next($request, $response);
        } catch (\Exception $e) {
            return $this->whoopsRun->handle($e, $request);
        }
    }

}
