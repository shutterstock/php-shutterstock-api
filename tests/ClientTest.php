<?php

namespace Shutterstock\Api;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    use MockClientTrait,
        MockHandlerTrait,
        ClientWithMockHandlerTrait;

    public function testIsInstanceOfClient()
    {
        $client = $this->getClient();

        $this->assertInstanceOf(
            Client::class,
            $client
        );
    }

    public function testConstructSetsGuzzle()
    {
        $client = $this->getClient();

        $this->assertAttributeInstanceOf(
            Guzzle::class,
            'guzzle',
            $client
        );
    }

    public function testConstructSetsJsonMiddleware()
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::mapResponse(function (Response $response) {
            $jsonStream = new JsonStream($response->getBody());
            return $response->withBody($jsonStream);
        }));

        $guzzle = new Guzzle([
            'base_uri' => 'https://api.shutterstock.com/v2/',
            'auth' => ['client_id', 'client_secret'],
            'handler' => $stack,
        ]);

        $client = $this->getClient();

        $this->assertAttributeEquals(
            $guzzle,
            'guzzle',
            $client
        );
    }

    /**
     * @dataProvider dataGet
     */
    public function testGet($expectedUri, $uri, $query)
    {
        $mockHandler = $this->getMockHandler();
        $client = $this->getClient();
        $this->setClientWithMockHandler($client, $mockHandler);

        $response = $client->get($uri, $query);
        $lastRequest = $mockHandler->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals($expectedUri, (string) $lastRequest->getUri());
    }

    /**
     * @dataProvider dataGet
     */
    public function testGetAsync($expectedUri, $uri, $query)
    {
        $mockHandler = $this->getMockHandler();
        $client = $this->getClient();
        $this->setClientWithMockHandler($client, $mockHandler);

        $promise = $client->getAsync($uri, $query);
        $lastRequest = $mockHandler->getLastRequest();

        $this->assertInstanceOf(PromiseInterface::class, $promise);
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals($expectedUri, (string) $lastRequest->getUri());
    }

    public function dataGet()
    {
        return [
            [
                'expectedUri' => 'resource/action',
                'uri' => 'resource/action',
                'query' => [],
            ],
            [
                'expectedUri' => 'test?key=value',
                'uri' => 'test',
                'query' => ['key' => 'value'],
            ],
        ];
    }

    /**
     * @dataProvider dataBuildQuery
     */
    public function testBuildQuery($expectedQuery, $query, $separator)
    {
        $client = $this->getClient();
        $queryString = $client->buildQuery($query, $separator);

        $this->assertEquals($expectedQuery, $queryString);
    }

    public function dataBuildQuery()
    {
        return [
            [
                'expectedQuery' => 'key=value',
                'query' => ['key' => 'value'],
                'separator' => '&',
            ],
            [
                'expectedQuery' => 'key_a=value_a&key_b=value_b',
                'query' => ['key_a' => 'value_a', 'key_b' => 'value_b'],
                'separator' => '&',
            ],
            [
                'expectedQuery' => 'key_a=value_a&amp;key_b=value_b',
                'query' => ['key_a' => 'value_a', 'key_b' => 'value_b'],
                'separator' => '&amp;',
            ],
            [
                'expectedQuery' => 'key=value_a&key=value_b',
                'query' => ['key' => ['value_a', 'value_b']],
                'separator' => '&',
            ],
        ];
    }

    /**
     * @dataProvider dataPost
     */
    public function testPost($expectedUri, $expectedBody, $uri, $body)
    {
        $mockHandler = $this->getMockHandler();
        $client = $this->getClient();
        $this->setClientWithMockHandler($client, $mockHandler);

        $response = $client->post($uri, $body);
        $lastRequest = $mockHandler->getLastRequest();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedUri, (string) $lastRequest->getUri());
        $this->assertEquals($expectedBody, (string) $lastRequest->getBody());
    }

    /**
     * @dataProvider dataPost
     */
    public function testPostAsync($expectedUri, $expectedBody, $uri, $body)
    {
        $mockHandler = $this->getMockHandler();
        $client = $this->getClient();
        $this->setClientWithMockHandler($client, $mockHandler);

        $promise = $client->postAsync($uri, $body);
        $lastRequest = $mockHandler->getLastRequest();

        $this->assertInstanceOf(PromiseInterface::class, $promise);
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedUri, (string) $lastRequest->getUri());
        $this->assertEquals($expectedBody, (string) $lastRequest->getBody());
    }

    public function dataPost()
    {
        return [
            [
                'expectedUri' => 'resource/action',
                'expectedBody' => '',
                'uri' => 'resource/action',
                'body' => [],
            ],
            [
                'expectedUri' => 'test',
                'expectedBody' => '{"key":"value"}',
                'uri' => 'test',
                'body' => ['key' => 'value'],
            ],
        ];
    }
}
