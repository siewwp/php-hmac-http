<?php


namespace Siewwp\HmacHttp;

use Acquia\Hmac\Exception\MalformedResponseException;
use Acquia\Hmac\ResponseAuthenticator;
use Psr\Http\Message\ResponseInterface;

class HmacAuthMiddleware extends \Acquia\Hmac\Guzzle\HmacAuthMiddleware
{
    /**
     * Called when the middleware is handled.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            $request = $this->signRequest($request);

            $promise = function (ResponseInterface $response) use ($request) {
                if ($response->getStatusCode() < 400) {
                    $authenticator = new ResponseAuthenticator($request, $this->key);

                    if (!$authenticator->isAuthentic($response)) {
                        throw new MalformedResponseException(
                            'Could not verify the authenticity of the response.',
                            null,
                            0,
                            $response
                        );
                    }
                }

                return $response;
            };

            return $handler($request, $options)->then($promise);
        };
    }
}