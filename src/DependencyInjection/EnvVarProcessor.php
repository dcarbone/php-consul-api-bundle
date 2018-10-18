<?php
namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;



use DCarbone\PHPConsulAPI\Consul;
use DCarbone\PHPConsulAPIBundle\Bag\ConsulBag;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;


class EnvVarProcessor implements EnvVarProcessorInterface
{
    const CACHE_PREFIX = 'consul_env';
    /**
     * @var Consul
     */
    private $consul;
    /**
     * @var null|CacheInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $prefix;

    static private $providedTypes = [];

    /**
     * EnvVarProcessor constructor.
     * @param Consul $bag
     * @param string $prefix
     * @param null|CacheInterface $cache
     */
    public function __construct(Consul $bag, $prefix = 'default', ?CacheInterface $cache = null)
    {
        $this->consul = $bag;
        $this->cache = $cache;
        $this->prefix = $prefix;
    }

    public static function addProvidedType($type)
    {
        self::$providedTypes[$type] = 'string';
    }

    public static function getProvidedTypes()
    {
        return self::$providedTypes;
    }

    protected function getKey($name){
        return str_replace('__', '/', $name);
    }

    protected function getCacheKey($name){
        return sprintf('%s_%s', self::CACHE_PREFIX, $name);
    }

    protected function getItem($name){

        $key = $this->getKey($name);

        if($this->cache && $result = $this->cache->get($this->getCacheKey($name))){
            return $result;
        }

        $default = $this->bag->getDefault()->KV();
        $item = $default->get($key);

        if(!($item[0] instanceof \DCarbone\PHPConsulAPI\KV\KVPair)){

            throw new RuntimeException(
                sprintf('A consul variable "%s" couldn\'t be found', $name)
            );

        }else if($item[2] instanceof \DCarbone\PHPConsulAPI\Error){
            throw new RuntimeException($item[2]->getMessage());
        }

        $result = $item[0]->getValue();
        if($this->cache){
            $this->cache->set($this->getCacheKey($name), $result);
        }

        return $result;

    }

    public function getEnv($prefix, $name, \Closure $getEnv)
    {

        if($prefix==self::ENV_PREFIX){
            return $this->getItem($name);
        }

    }


}