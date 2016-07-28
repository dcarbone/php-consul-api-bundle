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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class KeysCommand
 * @package DCarbone\PHPConsulAPIBundle\Command
 */
class KeysCommand extends AbstractPHPConsulAPICommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName($this->buildName('keys'))
            ->setDescription('Get list of keys in Consul with optional prefix')
            ->addArgument(
                'prefix',
                InputArgument::OPTIONAL,
                'Prefix to look under for KVP keys'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consul = $this->getContainer()->get('php_consul_api.client.default');

        /** @var string[] $keys */
        /** @var \DCarbone\PHPConsulAPI\QueryMeta $qm */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($keys, $qm, $err) = $consul->KV->keys($input->getArgument('prefix'));
        if (null !== $err)
        {
            $output->writeln('ERROR: '.$err->getMessage());
            return 1;
        }

        $output->writeln($keys);
        return 0;
    }
}