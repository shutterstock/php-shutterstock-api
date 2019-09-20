<?php

namespace Shutterstock\Api;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class JsonStreamTest extends TestCase
{

    public function testIsInstanceOfJsonStream()
    {
        $jsonStream = $this->getJsonStream('');

        $this->assertInstanceOf('Shutterstock\Api\JsonStream', $jsonStream);
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $jsonStream);
    }

    /**
     * @dataProvider dataJsonSerialize
     */
    public function testJsonSerialize($expectedArray, $string)
    {
        $jsonStream = $this->getJsonStream($string);
        $array = $jsonStream->jsonSerialize();

        $this->assertEquals($expectedArray, $array);
    }

    public function dataJsonSerialize()
    {
        return [
            [
                'expectedArray' => ['key' => 'value'],
                'string' => '{"key":"value"}',
            ],
            [
                'expectedArray' => null,
                'string' => '',
            ],
        ];
    }

    /**
     * @expectedException RuntimeException
     */
    public function testJsonSerializeException()
    {
        $jsonStream = $this->getJsonStream('words');
        $jsonStream->jsonSerialize();
    }

    protected function getJsonStream($string)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $string);
        fseek($stream, 0);
        $streamObject = new Stream($stream);

        return new JsonStream($streamObject);
    }
}
