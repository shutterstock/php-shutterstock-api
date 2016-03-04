<?php

namespace Shutterstock\Api;

trait MockClientTrait
{

    protected function getClient()
    {
        return new Client('client_id', 'client_secret');
    }
}
