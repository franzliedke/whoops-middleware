<?php
namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ServerRequestInterface;
use Whoops\Run;

interface WhoopsRunFactoryInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return Run
     */
    public function getWhoopsInstance(ServerRequestInterface $request);

}