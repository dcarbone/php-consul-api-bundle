<?php

namespace DCarbone\PHPConsulAPIBundle\Bag;

use DCarbone\PHPConsulAPI\Consul;

interface ConsulBagInterface extends \Iterator
{
    public function getDefault(): Consul;

    public function getNamed($name): Consul;

    public function getNames();
}
