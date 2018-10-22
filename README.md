# php-consul-api-bundle
Bundle to enable usage of dcarbone/php-consul-api inside of a Symfony 4 project

## Installation

In your `composer.json` file:

```json
{
    "require": {
        "dcarbone/php-consul-api-bundle": "1.0.*"
    }
}
```

## Default Configuration

There will always be at least one registered Consul service using the standard Consul
environment variables.  If you wish to use this, at the very least `CONSUL_HTTP_ADDR` should be defined.

This is also the default target of the `consul_api.default` alias.

If you want to override just host and keep other params default, use:

```yaml
consul_api:
    backends: dev.local:8500
```

## Specify default backend

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
- resolve_env
    - enabled
    - cache

As an example:

```yaml
consul_api:
    backends:
        nifty_name:
            addr:                   hostname.domain.woot
            scheme:                 https
            insecure_skip_verify:   false
            http_client:            ~ # Enter service name of GuzzleHttp\ClientInterface compatible http client you wish to use
            resolve_env:
                enabled: true       # allow this backend to resolve %env(consul_nifty_name:variable)% within container config
                cache: 60           # cache for a minute results
```

This will create a new service named `consul_api.nifty_name` with the specified configuration options.

## Environment variables resolving

### syntax

Sometimes you don't want to use [consul-template](https://github.com/hashicorp/consul-template) because change of environment
variables require cache-warmup. This bundle allows you to inject parameters into container just like pure envs:

```yaml
parameters:
    my_database_dsn: '%env(consul:credentials__db_connection)%'
```

Syntax:
```
%env(consul[_<backend name>]:<path with "__" instead of "/">)%
```

This syntax is processed just like a pure environment variables, so the case:

```yaml
parameters:
    %env(consul_ohai)%: test
    
services:
    
    MyNamespace\Class:
        arguments:
            - '%env(consul_ohai)%' 
```

is processed as well.

### caching

If you provide [PSR-6](https://www.php-fig.org/psr/psr-6/) compatible package ([symfony/cache](https://packagist.org/packages/symfony/cache) is supported out-of-the-box),
this bundle is capable of caching results in specified pool (or provided one). Provided the package is installed and you
define a number or just set ``cache`` flag of backend to true, the bundle will use ``PhpFilesAdapter``. Number specifies
TTL, but you are also able to provide very own implementation of caching as long as it implements ```CachePoolInterface```.

To use own backend, define service:

```yaml
services:
    app.envs_cache:
        class: App\Cache\Envs

``` 

and tell you're going to use this:
```yaml
consul_api:
    backends:
        default:
            resolve_env:
                cache: 'app.envs_cache'

```

And let the music play. 

## Overrides
Also, you may override any part of this bundle using [Decorate](https://symfony.com/doc/current/service_container/service_decoration.html)
feature of Symfony's DI container. For example, you want just to adjust TTL of cache item: decorate the ``consul_api.cache_persister`` service
(keep in mind the ``PersisterInterface``) and play freely with ``decorateItem`` method:

```yaml
# app/config/services.yml

services:
    app.my_service:
        decorates: consul_api.default.cache_persister
        class: App\Consul\CachePersister

``` 

```php
<?php
namespace App\Consul;

use DCarbone\PHPConsulAPIBundle\Cache\PersisterInterface;
use DCarbone\PHPConsulAPIBundle\Cache\Persister;
use Psr\Cache\CacheItemInterface;

class CachePersister implements PersisterInterface {
    
    protected $parent;
    
    public function __construct(Persister $parent){
        $this->parent = $parent;
    }
    
    public function get(string $name, string $prefix = null){
        return $this->parent->get($name, $prefix);
    }
    
    public function set(string $name, $value = null, string $prefix = null){
        return $this->parent->set($name, $value, $prefix);
    }
    
    public function decorateItem(CacheItemInterface $item, string $prefix = null){
        $item->expiresAfter(86400);
    }  
}

```

And that's all.

PS. Extending ``DependencyInjection\EnvVarProcessor`` has almost no point — its purpose is to tell Symfony that custom
prefixes are available and fetch data from adapters. Whole main work is done under particular``Processor\Adapter``.

## Twig Integration

If you are using [Twig](http://twig.sensiolabs.org/) and
[TwigBundle](https://packagist.org/packages/symfony/twig-bundle) in your Symfony app, there are a few 
functions exposed to you.  You can see the full list here:
[PHPConsulAPIExtension](./src/Twig/PHPConsulAPITwigExtension.php#L71).

## ConsulBag

If you have multiple named configurations present and want to be able to access them all, one possible way is to
utilize the [ConsulBag](./src/Bag/ConsulBag.php) service.  It is defined as `consul_api.bag`  