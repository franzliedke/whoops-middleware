<?php

namespace Franzl\Middleware\Whoops;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware returns the client preferred format.
 */
class FormatNegotiator
{
    /**
     * @var array Available formats with the mime types
     */
    private static $formats = [
        'html' => ['text/html', 'application/xhtml+xml'],
        'json' => ['application/json', 'text/json', 'application/x-json'],
        'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
        'txt' => ['text/plain']
    ];

    /**
     * Returns the format.
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public static function getFormat(ServerRequestInterface $request)
    {
        $default = 'html';

        $acceptType = $request->getHeader("accept");
        // Accept header is usually an array
        if (is_array($acceptType) && count($acceptType) > 0){
            $acceptType = $acceptType[0];

            // As many format may match for a given accept header, trying to determine the one that "hits" the most
            $counters = [];
            foreach (self::$formats as $format => $values){
                foreach ($values as $value){
                    if (strpos($acceptType, $value) !== false){
                        if (!isset($counters[$format])){
                            $counters[$format] = 0;
                        }
                        $counters[$format] ++;
                    }
                }
            }
        }
        // Sort the array to retrieve the format that best matches the Accept header
        if (count($counters) > 0){
            asort($counters);
            $counters = array_reverse($counters);
            return array_keys($counters)[0];
        }else{
            return $default;
        }
    }

}
