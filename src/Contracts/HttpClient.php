<?php


namespace Siewwp\HmacHttp\Contracts;

use Acquia\Hmac\KeyInterface;
use GuzzleHttp\ClientInterface;

interface HttpClient extends ClientInterface
{
    public function pushMiddleware(callable $middleware);

    public function setKey(KeyInterface $key);
}