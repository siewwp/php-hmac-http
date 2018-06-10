## Guzzle http client with hmac

### Usage

Providing the key on constructor

```php
$key = Acquia\Hmac\Key($appId, $appSecret);

$client = new \Siewwp\HmacHttp\HttpClient($options, $key);

// your usual guzzle stuff
```

Providing the key using key setter

```php
$client = new \Siewwp\HmacHttp\HttpClient($options, $key);

$key = Acquia\Hmac\Key($appId, $appSecret);

$client->setKey($key);

// your usual guzzle stuff
```