<?php
namespace Franzl\Middleware\Whoops\Insides;

use Franzl\Middleware\Whoops\WhoopsRunFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\XmlResponseHandler;
use Whoops\Run;

class WhoopsRunFactory implements WhoopsRunFactoryInterface
{
    /** @var CliDetector */
    private $cliDetector;
    /** @var FormatNegotiator */
    private $formatNegotiator;

    /**
     * @param CliDetector $cliDetector
     * @param FormatNegotiator $formatNegotiator
     */
    public function __construct(CliDetector $cliDetector, FormatNegotiator $formatNegotiator)
    {
        $this->cliDetector = $cliDetector;
        $this->formatNegotiator = $formatNegotiator;
    }

    /**
     * @return WhoopsRunFactory
     */
    public static function newInstance()
    {
        return new WhoopsRunFactory(new CliDetector(), new FormatNegotiator());
    }

    /**
     * @param ServerRequestInterface $request
     * @return Run
     */
    public function getWhoopsInstance(ServerRequestInterface $request)
    {
        $whoops = new Run();
        if ($this->cliDetector->isCli()) {
            $whoops->pushHandler(new PlainTextHandler);
            return $whoops;
        }

        $format = $this->formatNegotiator->getPreferredFormat($request);
        switch ($format) {
            case 'json':
                $handler = new JsonResponseHandler;
                $handler->addTraceToOutput(true);
                break;
            case 'html':
                $handler = new PrettyPageHandler;
                break;
            case 'txt':
                $handler = new PlainTextHandler;
                $handler->addTraceToOutput(true);
                break;
            case 'xml':
                $handler = new XmlResponseHandler;
                $handler->addTraceToOutput(true);
                break;
            default:
                if (empty($format)) {
                    $handler = new PrettyPageHandler;
                } else {
                    $handler = new PlainTextHandler;
                    $handler->addTraceToOutput(true);
                }
        }

        $whoops->pushHandler($handler);
        return $whoops;
    }
}
