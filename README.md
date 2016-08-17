# php-consul-api-bundle
Bundle to enable usage of dcarbone/php-consul-api inside of a Symfony 3 project

## Installation

In your `composer.json` file:

```json
{
    "require": {
        "dcarbone/php-consul-api-bundle": "0.4.*"
    }
}
```

In your `AppKernel.php` file:

```php
    public function registerBundles()
    {
        $bundles = [
            // --
            new \DCarbone\PHPConsulAPIBundle\PHPConsulAPIBundle(),
            // --
        ];

        // --

        return $bundles;
    }
```

## Default Configuration

By default this lib provides a "default" Consul client that will be constructed using the standard Consul
environment variables.  If you wish to use this, at the very least `CONSUL_HTTP_ADDR` should be defined.

This default Consul client is registered as `consul_api.default`.

## Named Configuration

If you wish to connect to multiple Consul agents, or just want to have things named differently, you may optionally
configure them under the `consul_api` configuration namespace.

The available configuration parameters are:

- addr
- scheme
- datacenter
- wait_time
- http_auth
- token
- ca_file
- cert_file
- key_file
- insecure_skip_verify
- curl_opts

As an example:

```yaml
consul_api:
    nifty_name:
        addr:                   hostname.domain.woot
        scheme:                 https
        insecure_skip_verify:   false
        curl_opts:
            curlopt_fresh_connect: 1
```

This will create a new service named `consul_api.nifty_name` with the specified configuration options.

## Twig Integration

If you are using [Twig](http://twig.sensiolabs.org/) and
[TwigBundle](https://packagist.org/packages/symfony/twig-bundle) in your Symfony app, there are a few 
functions exposed to you.  You can see the full list here:
[PHPConsulAPIExtension](./src/Twig/PHPConsulAPITwigExtension.php#L71).
