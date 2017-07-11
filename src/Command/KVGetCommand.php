<?php namespace DCarbone\PHPConsulAPIBundle\Command;

/*
   Copyright 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)

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
 * Class KVGetCommand
 * @package DCarbone\PHPConsulAPIBundle\Command
 */
class KVGetCommand extends AbstractPHPConsulAPICommand
{
    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->setName($this->buildName('kv', 'get'))
            ->setDescription('Query for and attempt to return KVP Value')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'Key to retrieve value for'
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
        $consul = $this->getConsul($input);

        /** @var \DCarbone\PHPConsulAPI\KV\KVPair $kvp */
        /** @var \DCarbone\PHPConsulAPI\QueryMeta $qm */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($kvp, $qm, $err) = $consul->KV->get($input->getArgument('key'));

        if (null !== $err)
        {
            $output->writeln('ERROR: '.$err->getMessage());
            return 1;
        }

        $output->writeln($kvp->Value);
        return 0;
    }
}