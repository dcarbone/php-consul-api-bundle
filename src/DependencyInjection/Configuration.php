<?php namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

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
            ->beforeNormalization()
            ->ifNull()
                ->then(function(){
                    return ['backends'=>[
                        'default'=>[]
                    ]];
                })
            ->end()
            ->children()
                ->scalarNode('default_configuration')
                    ->info('Name of the configuration to use as the default configuration for your app')
                    ->defaultValue('default')
                    ->treatNullLike('default')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('backends')
                    ->info('Custom Consul connection configurations')
                    ->requiresAtLeastOneElement()
                    ->beforeNormalization()
                    ->ifString()
                        ->then(function($value) {
                            return ['default'=>[
                                'addr'=>$value
                            ]];
                        })
                    ->end()
                    ->beforeNormalization()
                    ->ifNull()
                        ->then(function(){
                            return ['default'=>[]];
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                        ->beforeNormalization()
                        ->ifString()
                        ->then(function($value){
                            return [
                                'addr'=>$value
                            ];
                        })
                        ->end()
                            ->children()
                                ->arrayNode('resolve_env')
                                ->info('An environment variables resolving via consul')
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('enabled')
                                            ->info('Resolving enabled')
                                            ->defaultTrue()
                                        ->end()
                                        ->scalarNode('cache')
                                            ->info('TTL of entry if numeric or service identifier')
                                            ->defaultNull()
                                        ->end()
                                    ->end()
                                ->end()
                                ->scalarNode('http_client')
                                    ->info('Name of Registered Service of an instance of a class that implements Guzzle\'s ClientInterface')
                                    ->defaultValue('consul_api.default_guzzle_client')
                                ->end()
                                ->scalarNode('addr')
                                    ->info('Address:Port to Consul Agent WITHOUT scheme')
                                    ->isRequired()
                                    ->defaultValue('127.0.0.1:8500')
                                ->end()
                                ->enumNode('scheme')
                                    ->values(['http', 'https'])
                                    ->info('Scheme to use (currently supports HTTP and HTTPS)')
                                    ->defaultValue('http')
                                ->end()
                                ->scalarNode('datacenter')
                                    ->info('Default datacenter')
                                ->end()
                                ->scalarNode('http_auth')
                                    ->info('username:password combination to use in queries')
                                ->end()
                                ->scalarNode('token')
                                    ->info('Default authentication token to use in queries')
                                ->end()
                                ->scalarNode('ca_file')
                                    ->info('Certificate Authority file path')
                                ->end()
                                ->scalarNode('client_cert')
                                    ->info('Client Certificate file path')
                                ->end()
                                ->scalarNode('client_key')
                                    ->info('Client Certificate Key file path')
                                ->end()
                                ->booleanNode('insecure_skip_verify')
                                    ->info('Disable SSL verification (for HTTPS)')
                                    ->defaultFalse()
                                ->end()
                                ->booleanNode('token_in_header')
                                    ->info('If true, will use "X-Consul-Token" header rather than "?token=" query param.  For Consul >= 0.7, this should be true.')
                                    ->defaultTrue()
            ;

        return $treeBuilder;
    }
}