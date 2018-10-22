<?php

namespace DCarbone\PHPConsulAPIBundle\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class Persister implements PersisterInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $pool;
    /**
     * @var int
     */
    private $defaultTtl;

    public function __construct(CacheItemPoolInterface $pool, $defaultTtl = 60)
    {
        $this->pool = $pool;
        $this->defaultTtl = $defaultTtl;
    }

    protected function getCacheItemName($name, $prefix): string
    {
        return $prefix.crc32($name);
    }

    public function get(string $name, string $prefix = null)
    {
        try {
            $result = $this->pool->getItem($this->getCacheItemName($name, $prefix));

            if ($result->isHit()) {
                return $result->get();
            }
        } catch (InvalidArgumentException $e) {
        }
    }

    public function set(string $name, $value = null, string $prefix = null)
    {
        $item = $this->pool->getItem(
            $this->getCacheItemName($name, $prefix)
        );
        $item->set($value);

        $this->decorateItem($item, $prefix);

        $this->pool->save($item);
    }

    public function decorateItem(CacheItemInterface $item, string $prefix = null)
    {
        $item->expiresAfter($this->defaultTtl);
    }
}
