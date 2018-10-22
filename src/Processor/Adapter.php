<?php
namespace DCarbone\PHPConsulAPIBundle\Processor;


use DCarbone\PHPConsulAPI\Consul;
use DCarbone\PHPConsulAPIBundle\Cache\PersisterInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class Adapter implements AdapterInterface
{

    /**
     * @var Consul
     */
    private $consul;
    /**
     * @var null|PersisterInterface
     */
    private $cache;

    /**
     * EnvVarProcessor constructor.
     * @param Consul $consul
     * @param null|PersisterInterface $cache
     */
    public function __construct(Consul $consul, ?PersisterInterface $cache = null)
    {
        $this->consul = $consul;
        $this->cache = $cache;
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
            $this->cache->set($name, $result, $prefix);
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