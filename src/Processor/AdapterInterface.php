<?php

namespace DCarbone\PHPConsulAPIBundle\Processor;

interface AdapterInterface
{
    public function getEnv($prefix, $name, \Closure $getEnv);
}
