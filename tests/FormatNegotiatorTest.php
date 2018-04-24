<?php

namespace Franzl\Middleware\Whoops\Test;

use Franzl\Middleware\Whoops\FormatNegotiator;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class FormatNegotiatorTest extends TestCase
{
    public function test_requests_without_accept_header_returns_html()
    {
        $request = new ServerRequest;
        $format = FormatNegotiator::getPreferredFormat($request);

        $this->assertEquals('html', $format);
    }

    /**
     * @dataProvider knownTypes
     */
    public function test_known_mimetypes_will_return_preferred_format($mimeType, $expectedFormat)
    {
        $format = FormatNegotiator::getPreferredFormat(
            $this->makeRequestWithAccept($mimeType)
        );

        $this->assertEquals($expectedFormat, $format);
    }

    public function test_multiple_mimetypes_will_prefer_the_first_match()
    {
        $format = FormatNegotiator::getPreferredFormat(
            $this->makeRequestWithAccept('application/xml, application/json')
        );

        $this->assertEquals('xml', $format);
    }

    public function test_unknown_mimetypes_will_fall_back_to_plain_text()
    {
        $format = FormatNegotiator::getPreferredFormat(
            $this->makeRequestWithAccept('foo/bar, x/custom')
        );

        $this->assertEquals('txt', $format);
    }

    public function knownTypes()
    {
        return [
            ['text/html', 'html'],
            ['application/xhtml+xml', 'html'],
            ['application/json', 'json'],
            ['text/json', 'json'],
            ['application/x-json', 'json'],
            ['text/xml', 'xml'],
            ['application/xml', 'xml'],
            ['application/x-xml', 'xml'],
            ['text/plain', 'txt'],
        ];
    }

    private function makeRequestWithAccept($acceptHeader)
    {
        $request = new ServerRequest;

        return $request->withHeader('accept', $acceptHeader);
    }
}
