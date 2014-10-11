<?php
namespace Plum\Rest\Server\Tests\Impl\ContentNegotiation;

use Plum\Rest\Server\Impl\ContentNegotiation\AcceptHeaderParser;

class AcceptHeaderParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider provideParsedAcceptHeaderPairs
     */
    function it_should_parse_accept_header_accordingly(
        $accept, array $parsed, $message = null
    )
    {
        $p = new AcceptHeaderParser();

        $this->assertEquals(
            $parsed,
            $p->parse($accept),
            $message
        );
    }

    function provideParsedAcceptHeaderPairs()
    {
        return [
            [
                "application/xml;q=0.1, application/json;q=0.9",
                ["application/json", "application/xml"],
                "Failed to sort media types based on it's qualities"
            ],
            [
                "application/xml;q=0.1, application/json",
                ["application/json", "application/xml"],
                "Failed to handle accept header with implicit qualities"
            ],
            [
                "application/json, application/*",
                ["application/json", "application/*"],
                "Failed to prioritize complete media type than wildcard"
            ],
            [
                "application/*, application/json",
                ["application/json", "application/*"],
                "Failed to prioritize complete media type than wildcard"
            ]
        ];
    }
} 
