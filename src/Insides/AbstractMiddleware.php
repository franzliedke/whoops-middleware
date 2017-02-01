<?php

namespace Franzl\Middleware\Whoops\Insides;

use Franzl\Middleware\Whoops\WhoopsRunner;

abstract class AbstractMiddleware
{

    /** @var  WhoopsRunner */
    protected $whoopsRun;

    /**
     * @param WhoopsRunner $whoopsRunner
     */
    public function __construct(WhoopsRunner $whoopsRunner)
    {
        $this->whoopsRun = $whoopsRunner;
    }

    /**
     * @return AbstractMiddleware
     */
    public static function createNewInstance()
    {
        return new static(WhoopsRunner::createNewInstance());
    }

}