<?php

namespace Shutterstock\Api;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use ReflectionClass;

trait ClientWithMockHandlerTrait
{

    protected function setClientWithMockHandler(Client $client, MockHandler $mockHandler)
    {
        $options = [
            'handler' => HandlerStack::create($mockHandler),
        ];
        $reflectedClient = new ReflectionClass($client);
        $reflectedProperty = $reflectedClient->getProperty('guzzle');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($client, new Guzzle($options));
        return true;
    }
}
