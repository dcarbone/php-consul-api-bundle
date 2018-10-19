<?php
namespace DCarbone\PHPConsulAPIBundle\Cache;


use Psr\Cache\CacheItemInterface;

interface PersisterInterface
{
    public function get(string $name);
    public function set(string $name, $value = null);
    public function decorateItem(CacheItemInterface $item);
}