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

use DCarbone\PHPConsulAPI\Config;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListConfigsCommand
 * @package DCarbone\PHPConsulAPIBundle\Command
 */
class ListConfigsCommand extends ContainerAwareCommand
{
    /** @var int */
    private $_longestConfigName = 0;

    /** @var array */
    private static $_preOut = [
        'PHP Consul API Configurations:',
        '  default',
    ];

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->setName('consul-api:config:list')
            ->setDescription('List Consul API configurations')
            ->addOption(
                'dump',
                'd',
                InputOption::VALUE_NONE,
                'Dump configuration'
            )
            ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dump'))
            $out = $this->_buildDumpOutput($input, $output);
        else
            $out = $this->_buildSimpleOutput($input, $output);

        $output->writeln($out);
        $output->writeln('');

        return 1;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function _buildSimpleOutput(InputInterface $input, OutputInterface $output)
    {
        $out = self::$_preOut;
        foreach($this->getContainer()->getParameter('consul_api.config_names') as $name)
        {
            $out[] = sprintf('  %s', $name);
        }
        return $out;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function _buildDumpOutput(InputInterface $input, OutputInterface $output)
    {
        $out = self::$_preOut;

        $container = $this->getContainer();

        foreach($container->get('consul_api.default.config') as $k => $v)
        {
            if ($this->_longestConfigName < ($len = strlen($k)))
                $this->_longestConfigName = $len;

            if ($v !== null)
                $out[] = sprintf('    %s:%%s%s', $k, $v);
        }

        $out[] = '';

        foreach($container->getParameter('consul_api.config_names') as $name)
        {
            $out[] = sprintf('  %s', $name);
            foreach($container->get(sprintf('consul_api.config.%s', $name)) as $k => $v)
            {
                if ($v !== null)
                    $out[] = sprintf('    %s:%%s%s', $k, $v);
            }

            $out[] = '';
        }

        return $out;
    }
}