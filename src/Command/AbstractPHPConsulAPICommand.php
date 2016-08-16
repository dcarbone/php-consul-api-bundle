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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class AbstractPHPConsulAPICommand
 * @package DCarbone\PHPConsulAPIBundle\Command
 */
abstract class AbstractPHPConsulAPICommand extends ContainerAwareCommand
{
    /**
     * Constructor.
     *
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws \LogicException When the command name is empty
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->addOption(
            'client',
            null,
            InputOption::VALUE_OPTIONAL,
            'Client configuration to use',
            'default'
        );
    }

    /**
     * @param InputInterface $input
     * @return \DCarbone\PHPConsulAPI\Consul
     */
    protected function getClient(InputInterface $input)
    {
        $container = $this->getContainer();

        $client = $input->getOption('client');
        if ('default' === $client)
            return $container->get('consul_api.default');

        $s = sprintf('consul_api.%s', $client);

        if ($container->has($s))
            return $container->get($s);

        throw new \LogicException(sprintf(
            'Unable to locate Consul service definition labeled "%s".  Please check your configuration.',
            $client
        ));
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
     * @return string
     */
    protected function buildName($client, $command)
    {
        return sprintf('%s:%s:%s', $this->getPrefix(), $client, $command);
    }
}