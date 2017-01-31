<?php

namespace Franzl\Middleware\Whoops\Insides;

interface MiddlewareInterface
{

    /**
     * @return MiddlewareInterface
     */
    public static function createNewInstance();
}