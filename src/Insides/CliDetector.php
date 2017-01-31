<?php

namespace Franzl\Middleware\Whoops\Insides;

class CliDetector
{
    /**
     * @return bool
     */
    public function isCli()
    {
        return php_sapi_name() === 'cli';
    }
}