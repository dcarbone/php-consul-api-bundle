<?php namespace DCarbone\PHPConsulAPIBundle;

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

use DCarbone\PHPConsulAPIBundle\DependencyInjection\PHPConsulAPIExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PHPConsulAPIBundle
 * @package DCarbone\PHPConsulAPIBundle
 */
class PHPConsulAPIBundle extends Bundle
{
    /**
     * @return string
     */
    public function getNamespace()
    {
        return 'DCarbone\\PHPConsulAPIBundle';
    }

    /**
     * @return PHPConsulAPIExtension
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension))
            $this->extension = $this->createContainerExtension();

        return $this->extension;
    }

    /**
     * @return PHPConsulAPIExtension
     */
    protected function createContainerExtension()
    {
        return new PHPConsulAPIExtension();
    }

    /**
     * @return string
     */
    protected function getContainerExtensionClass()
    {
        return 'DCarbone\\PHPConsulAPIBundle\\DependencyInjection\\PHPConsulAPIExtension';
    }
}