<?php namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package DCarbone\PHPConsulAPIBundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('consul_api');

        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('default_configuration')
                    ->info('Name of the configuration to use as the default configuration for your app')
                    ->defaultValue('local')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('named_configurations')
                    ->info('Custom Consul connection configurations')
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('curl_opts')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('opt')
                                                ->info('CURLOPT constant')
                                                ->isRequired()
                                                ->validate()
                                                    ->ifTrue(function($opt) {
                                                        if (!is_string($opt))
                                                            return true;

                                                        $opt = strtoupper($opt);

                                                        return 0 !== strpos($opt, 'CURLOPT_')
                                                               && 'CURLINFO_HEADER_OUT' !== $opt
                                                               && !defined($opt);
                                                    })
                                                    ->thenInvalid('"%s" is not a valid CURLOPT constant!  Please see'.
                                                                  ' http://php.net/manual/en/function.curl-setopt.php.')
                                                ->end()
                                            ->end()
                                            ->scalarNode('val')
                                                ->info('Value for CURLOPT')
                                                ->isRequired()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->booleanNode('insecure_skip_verify')
                                    ->info('Whether to enable SSL verification (for HTTPS)')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('key_file')
                                    ->info('Default Certificate Key file path')
                                ->end()
                                ->scalarNode('cert_file')
                                    ->info('Default Certificate file path')
                                ->end()
                                ->scalarNode('ca_file')
                                    ->info('Default Certificate Authority file path')
                                ->end()
                                ->scalarNode('token')
                                    ->info('Default authentication token to use in queries')
                                ->end()
                                ->scalarNode('http_auth')
                                    ->info('default username:password combination to use in queries')
                                ->end()
                                ->scalarNode('wait_time')
                                    ->info('Blocking call wait time')
                                ->end()
                                ->scalarNode('datacenter')
                                    ->info('Default datacenter')
                                ->end()
                                ->enumNode('scheme')
                                    ->values(['http', 'https'])
                                    ->info('Scheme to use (currently supports HTTP and HTTPS)')
                                    ->defaultValue('http')
                                ->end()
                                ->scalarNode('addr')
                                    ->info('Address:Port to Consul Agent WITHOUT scheme')
                                    ->isRequired()
                                    ->cannotBeEmpty()
            ;

        return $treeBuilder;
    }
}