<?php
namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

use DCarbone\PHPConsulAPIBundle\Processor\Adapter;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;


class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * @var Adapter[]
     */
    static private $adapters = [];

    /**
     * need a separate array because adapters are populated later
     * @var string[]
     */
    static private $providedTypes = [];

    public static function getProvidedTypes()
    {
        return self::$providedTypes;
    }

    public static function addProvidedType(string $prefix)
    {
        self::$providedTypes[$prefix] = 'string';
    }

    public static function addAdapter(string $prefix, Adapter $adapter)
    {
        self::$adapters[$prefix] = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        if(isset(self::$adapters[$prefix])){
            return self::$adapters[$prefix]->getEnv($prefix, $name, $getEnv);
        }
    }
}