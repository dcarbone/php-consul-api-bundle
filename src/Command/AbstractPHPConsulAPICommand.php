<?php

namespace DCarbone\PHPConsulAPIBundle\Command;

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

use DCarbone\PHPConsulAPIBundle\Bag\ConsulBag;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class AbstractPHPConsulAPICommand.
 */
abstract class AbstractPHPConsulAPICommand extends Command
{
    /**
     * @var ConsulBag
     */
    private $_consulBag;

    /**
     * Constructor.
     *
     * @param ConsulBag $consulBag
     */
    public function __construct(ConsulBag $consulBag)
    {
        parent::__construct();
        $this->_consulBag = $consulBag;
    }

    protected function configure()
    {
        $this->addOption(
            'config',
            null,
            InputOption::VALUE_OPTIONAL,
            'Named configuration to use',
            'default'
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return \DCarbone\PHPConsulAPI\Consul
     */
    protected function getConsul(InputInterface $input)
    {
        return $this->_consulBag->getNamed(
            $input->getOption('config')
        );
    }

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
     *
     * @return string
     */
    protected function buildName($client, $command)
    {
        return sprintf('%s:%s:%s', $this->getPrefix(), $client, $command);
    }
}
