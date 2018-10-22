<?php

namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

use DCarbone\PHPConsulAPIBundle\Processor\AdapterInterface;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * @var AdapterInterface[]
     */
    private static $adapters = [];

    /**
     * need a separate array because adapters are populated later.
     *
     * @var string[]
     */
    private static $providedTypes = [];

    public static function getProvidedTypes()
    {
        return self::$providedTypes;
    }

    public static function addProvidedType(string $prefix)
    {
        self::$providedTypes[$prefix] = 'string';
    }

    public static function addAdapter(string $prefix, AdapterInterface $adapter)
    {
        self::$adapters[$prefix] = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        if (isset(self::$adapters[$prefix])) {
            return self::$adapters[$prefix]->getEnv($prefix, $name, $getEnv);
        }
    }
}
