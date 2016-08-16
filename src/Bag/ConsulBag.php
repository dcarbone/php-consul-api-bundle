<?php namespace DCarbone\PHPConsulAPIBundle\Bag;

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
 * Class ConsulBag
 * @package DCarbone\PHPConsulAPIBundle\Bag
 */
class ConsulBag
{
    /** @var Consul[] */
    private $_consuls = array();

    /** @var Consul */
    private $_default;

    /**
     * ConsulBag constructor.
     * @param Consul $consul
     */
    public function __construct(Consul $consul)
    {
        $this->_default = $consul;
    }

    /**
     * @param string $name
     * @param Consul $consul
     */
    public function addConsul($name, Consul $consul)
    {
        $this->_consuls[$name] = $consul;
    }

    /**
     * @return Consul
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * @param string $name
     * @return Consul
     */
    public function getNamed($name)
    {
        if ('default' === $name)
            return $this->_default;

        if (isset($this->_consuls[$name]))
            return $this->_consuls[$name];

        throw new \OutOfBoundsException(sprintf(
            'There is no Consul service registered with name "%s"',
            $name
        ));
    }
}