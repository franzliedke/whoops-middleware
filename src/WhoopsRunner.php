<?php

namespace Franzl\Middleware\Whoops;

use Franzl\Middleware\Whoops\Insides\WhoopsRunFactory;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Run;
use Zend\Diactoros\Response\HtmlResponse;

class WhoopsRunner
{
    /** @var WhoopsRunFactoryInterface */
    private $whoopsRunFactory;

    /**
     * @param WhoopsRunFactoryInterface $whoopsRunFactory
     */
    public function __construct(WhoopsRunFactoryInterface $whoopsRunFactory)
    {
        $this->whoopsRunFactory = $whoopsRunFactory;
    }

    /**
     * @return WhoopsRunner
     */
    public static function newInstance()
    {
        return new WhoopsRunner(WhoopsRunFactory::newInstance());
    }

    /**
     * @param $error
     * @param ServerRequestInterface $request
     * @return HtmlResponse
     */
    public function handle($error, ServerRequestInterface $request)
    {
        $method = Run::EXCEPTION_HANDLER;

        $whoops = $this->whoopsRunFactory->getWhoopsInstance($request);

        // Output is managed by the middleware pipeline
        $whoops->allowQuit(false);

        ob_start();
        $whoops->$method($error);
        $response = ob_get_clean();

        return new HtmlResponse($response, 500);
    }

}
