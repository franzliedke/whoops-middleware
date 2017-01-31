<?php

namespace Franzl\Middleware\Whoops\Test;

use Franzl\Middleware\Whoops\Insides\CliDetector;

class CliDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCli()
    {
        $this->assertTrue((new CliDetector())->isCli());
    }
}
