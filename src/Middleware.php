<?php

namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Middleware
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($error, Request $request, Response $response, callable $out = null)
    {
        return WhoopsRunner::handle($error);
    }
}
