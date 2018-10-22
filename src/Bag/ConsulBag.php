<?php

namespace DCarbone\PHPConsulAPIBundle\Bag;

/*
   Copyright 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)

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
 * Class ConsulBag.
 */
class ConsulBag implements ConsulBagInterface
{
    /** @var \DCarbone\PHPConsulAPI\Consul[] */
    private $_namedConsuls = [];
    /** @var string */
    private $_defaultName;

    /**
     * ConsulBag constructor.
     *
     * @param string   $defaultName
     * @param Consul[] $namedConsuls
     */
    public function __construct($defaultName = 'default', array $namedConsuls = [])
    {
        $this->_namedConsuls = $namedConsuls;
        $this->_defaultName = $defaultName;
    }

    public function getDefault(): Consul
    {
        return $this->getNamed($this->_defaultName);
    }

    /**
     * @param string $name
     *
     * @return \DCarbone\PHPConsulAPI\Consul|mixed
     */
    public function getNamed($name): Consul
    {
        if ('default' === $name) {
            $name = $this->_defaultName;
        }

        if (isset($this->_namedConsuls[$name])) {
            return $this->_namedConsuls[$name];
        }

        throw new \OutOfBoundsException(sprintf(
            'There is no Consul Configuration registered with name "%s".  Available configurations: ["%s"]',
            $name,
            implode('", "', array_keys($this->_namedConsuls))
        ));
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return array_keys($this->_namedConsuls);
    }

    /**
     * @return Consul
     */
    public function current()
    {
        return current($this->_namedConsuls);
    }

    public function next()
    {
        next($this->_namedConsuls);
    }

    /**
     * @return string
     */
    public function key()
    {
        return key($this->_namedConsuls);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return null !== key($this->_namedConsuls);
    }

    public function rewind()
    {
        reset($this->_namedConsuls);
    }
}
