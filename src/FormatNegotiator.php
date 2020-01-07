<?php

namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Detect any of the supported preferred formats from a HTTP request
 */
class FormatNegotiator
{
    /**
     * @var array Available format handlers
     */
    private static $formats = [
        Formats\Html::class,
        Formats\Json::class,
        Formats\PlainText::class,
        Formats\Xml::class,
    ];

    /**
     * Returns the preferred format based on the Accept header
     *
     * @param ServerRequestInterface $request
     * @return Formats\Format
     */
    public static function negotiate(ServerRequestInterface $request): Formats\Format
    {
        $acceptTypes = $request->getHeader('accept');

        if (count($acceptTypes) > 0) {
            $acceptType = $acceptTypes[0];

            // As many formats may match for a given Accept header, let's try to find the one that fits the best
            $counters = [];
            foreach (self::$formats as $format) {
                foreach ($format::MIMES as $value) {
                    $counters[$format] = isset($counters[$format]) ? $counters[$format] : 0;
                    $counters[$format] += intval(strpos($acceptType, $value) !== false);
                }
            }

            // Sort the array to retrieve the format that best matches the Accept header
            asort($counters);
            end($counters);

            if (current($counters) == 0) {
                return new Formats\PlainText;
            } else {
                $class = key($counters);
                return new $class;
            }
        }

        return new Formats\Html;
    }
}
