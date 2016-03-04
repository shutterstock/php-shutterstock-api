<?php

namespace Shutterstock\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

trait MockHandlerTrait
{

    protected function getMockHandler(Response $response = null)
    {
        if (is_null($response)) {
            $response = new Response(200, [], json_encode([]));
        }
        return new MockHandler([$response]);
    }
}
