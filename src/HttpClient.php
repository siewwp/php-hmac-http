<?php


namespace Siewwp\HmacHttp;

use Siewwp\HmacHttp\Exceptions\UndefinedKeyException;
use Siewwp\HmacHttp\Contracts\HttpClient as HttpClientContract;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\KeyInterface;
use GuzzleHttp\HandlerStack;

class HttpClient extends \GuzzleHttp\Client implements HttpClientContract 
{
    public $retry;
    protected $key;
    protected $baseUri;
    
    /** @var HandlerStack */
    protected $handler;

    /**
     * Registration constructor.
     * @param array $config
     * @param KeyInterface $key
     * @param string $baseUri
     * @param int $retry
     */
    public function __construct(array $config, KeyInterface $key = null, $retry = 1)
    {
        $this->retry = $retry;

        if (!isset($config['handler'])) {
            $config['handler'] = HandlerStack::create();
        }
        
        if (!isset($config['headers']['Accept'])) {
            $config['headers']['Accept'] = 'application/json';
        }

        $this->handler = $config['handler'];
        
        if (!is_null($key)) {
            $this->setKey($key);
        }
        
        parent::__construct($config);
    }

    public function pushMiddleware(callable $middleware)
    {
        $this->handler->push($middleware);
    }
    
    public function setKey(KeyInterface $key)
    {
        $this->key = $key;
        $this->pushMiddleware(new HmacAuthMiddleware($this->key));
    }

    /**
     * @param $method
     * @param string $path
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function request($method, $path = '', array $options = [])
    {
        if (is_null($this->key)) {
            throw new UndefinedKeyException;
        }
        
        return retry($this->retry, function () use ($method, $path, $options) {
            $response = parent::request($method, $path, $options);

            return json_decode((string)$response->getBody(), true);
        }, 1000);
    }
}