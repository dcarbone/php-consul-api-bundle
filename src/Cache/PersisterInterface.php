<?php

namespace DCarbone\PHPConsulAPIBundle\Cache;

use Psr\Cache\CacheItemInterface;

interface PersisterInterface
{
    public function get(string $name, string $prefix = null);

    public function set(string $name, $value = null, string $prefix = null);

    public function decorateItem(CacheItemInterface $item, string $prefix = null);
}
