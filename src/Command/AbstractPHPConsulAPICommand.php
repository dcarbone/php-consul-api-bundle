<?php namespace DCarbone\PHPConsulAPIBundle\Command;

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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class AbstractPHPConsulAPICommand
 * @package DCarbone\PHPConsulAPIBundle\Command
 */
abstract class AbstractPHPConsulAPICommand extends ContainerAwareCommand
{
    /**
     * @return string
     */
    protected function getPrefix()
    {
        return 'consul-api';
    }

    /**
     * @param string $client
     * @param string $command
     * @return string
     */
    protected function buildName($client, $command)
    {
        return sprintf('%s:%s:%s', $this->getPrefix(), $client, $command);
    }
}