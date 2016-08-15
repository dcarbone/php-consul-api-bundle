<?php namespace DCarbone\PHPConsulAPIBundle\Twig;

/*
   Copyright 2016 Daniel Carbone (daniel.p.carbone@gmail.com)

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
use DCarbone\PHPConsulAPI\Consul;

/**
 * Class PHPConsulAPIExtension
 * @package DCarbone\PHPConsulAPIBundle\Twig
 */
class PHPConsulAPIExtension extends \Twig_Extension
{
    /** @var Consul */
    private $_c;

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'php_consul_api';
    }

    /**
     * PHPConsulAPIExtension constructor.
     * @param Consul $consul
     */
    public function __construct(Consul $consul)
    {
        $this->_c = $consul;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'consul_kv_get',
                array($this, 'consulKVGet')
            ),
            new \Twig_SimpleFunction(
                'consul_catalog_services',
                array($this, 'consulCatalogServices')
            ),
            new \Twig_SimpleFunction(
                'consul_catalog_service',
                array($this, 'consulCatalogService')
            )
        );
    }

    /**
     * @param string $key
     * @return \DCarbone\PHPConsulAPI\KV\KVPair
     */
    public function consulKVGet($key)
    {
        /** @var \DCarbone\PHPConsulAPI\KV\KVPair $kvp */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($kvp, $_, $err) = $this->_c->KV->get($key);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $kvp;
    }

    /**
     * @param string $serviceName
     * @param string $tags
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogService[]
     */
    public function consulCatalogServices($serviceName, $tags = '')
    {
        /** @var \DCarbone\PHPConsulAPI\Catalog\CatalogService[] $services */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($services, $_, $err) = $this->_c->Catalog->service($serviceName, (string)$tags);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $services;
    }

    /**
     * @param string $serviceName
     * @param string $tags
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogService
     */
    public function consulCatalogService($serviceName, $tags = '')
    {
        $services = $this->consulCatalogServices($serviceName, $tags);
        return reset($services);
    }
}