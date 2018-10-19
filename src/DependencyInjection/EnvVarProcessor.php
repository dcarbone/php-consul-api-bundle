<?php
namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

use DCarbone\PHPConsulAPI\Consul;
use DCarbone\PHPConsulAPIBundle\Cache\Persister;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;


class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * @var Consul
     */
    private $consul;
    /**
     * @var null|Persister
     */
    private $cache;

    static private $providedTypes = [];

    /**
     * EnvVarProcessor constructor.
     * @param Consul $consul
     * @param null|Persister $cache
     */
    public function __construct(Consul $consul, ?Persister $cache = null)
    {
        $this->consul = $consul;
        $this->cache = $cache;
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

    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        $key = $this->getKey($name);

        if($this->cache && $result = $this->cache->get($name, $prefix)){
            return $result;
        }

        $default = $this->consul->KV();
        $item = $default->get($key);

        $this->checkKvResponse($item, $name, $prefix);

        $result = $item[0]->getValue();
        if($this->cache){
            $this->cache->set($name, $prefix);
        }

        return $result;
    }

    protected function checkKvResponse($item, $name, $prefix){
        if(!($item[0] instanceof \DCarbone\PHPConsulAPI\KV\KVPair)){

            throw new RuntimeException(
                sprintf('A consul variable "%s" couldn\'t be found (backend: %s)', $name, $prefix)
            );

        }else if($item[2] instanceof \DCarbone\PHPConsulAPI\Error){
            throw new RuntimeException($item[2]->getMessage());
        }
    }

}