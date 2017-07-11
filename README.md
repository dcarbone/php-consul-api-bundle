# php-consul-api-bundle
Bundle to enable usage of dcarbone/php-consul-api inside of a Symfony 3 project

## Installation

In your `composer.json` file:

```json
{
    "require": {
        "dcarbone/php-consul-api-bundle": "0.6.*"
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

## Local Configuration

There will always be at least one registered Consul service using the standard Consul
environment variables.  If you wish to use this, at the very least `CONSUL_HTTP_ADDR` should be defined.

The service can be accessed using the `consul_api.local` service.  This is also the default target of the
`consul_api.default` alias.

## Default Configuration

You may optionally override the default configuration with one of the named configurations you have specified as such:

```yaml
consul_api:
    default_configuration: nifty_name
```

## Named Configurations

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
- client_cert
- client_key
- insecure_skip_verify
- token_in_header
- http_client

As an example:

```yaml
consul_api:
    named_configurations:
        nifty_name:
            addr:                   hostname.domain.woot
            scheme:                 https
            insecure_skip_verify:   false
            http_client:            ~ # Enter service name of GuzzleHttp\ClientInterface compatible http client you wish to use
```

This will create a new service named `consul_api.nifty_name` with the specified configuration options.

## Twig Integration

If you are using [Twig](http://twig.sensiolabs.org/) and
[TwigBundle](https://packagist.org/packages/symfony/twig-bundle) in your Symfony app, there are a few 
functions exposed to you.  You can see the full list here:
[PHPConsulAPIExtension](./src/Twig/PHPConsulAPITwigExtension.php#L71).

## ConsulBag

If you have multiple named configurations present and want to be able to access them all, one possible way is to
utilize the [ConsulBag](./src/Bag/ConsulBag.php) service.  It is defined as `consul_api.bag`  