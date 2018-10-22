<?php

namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

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

use DCarbone\PHPConsulAPI\Config;
use DCarbone\PHPConsulAPIBundle\Cache\Persister;
use DCarbone\PHPConsulAPIBundle\Processor\Adapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PHPConsulAPIExtension.
 */
class PHPConsulAPIExtension extends Extension
{
    /**
     * @return string
     */
    public function getAlias()
    {
        return 'consul_api';
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('consul_api.yml');

        $bundles = $container->getParameter('kernel.bundles');
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('consul_api.default_configuration_name', $config['default_configuration']);

        $this->addBackends($config, $container);

        // remove extension if Twig is not enabled
        if (!isset($bundles['TwigBundle'])) {
            $container->removeDefinition('consul_api.twig.extension');
        }
    }

    protected function addBackends(array $config, ContainerBuilder $container)
    {
        $bag = $container->getDefinition('consul_api.bag');

        $backendDefinitions = [];
        foreach ($config['backends'] as $name => $b) {
            $this->addEnvListener($name, $b, $container);
            $backendDefinitions[$name] = $this->addServiceDefinition($name, $b, $container);
        }

        $bag->addArgument($backendDefinitions);
    }

    protected function addEnvListener($name, $config, ContainerBuilder $builder)
    {
        if (empty($config['resolve_env']['enabled'])) {
            return;
        }

        $namePrefix = 'default' == $name ? 'consul' : 'consul_'.$name;

        $arguments = [
            new Reference(sprintf('consul_api.%s', $name)),
        ];

        $adapter = new Definition(Adapter::class);
        if (!empty($config['resolve_env']['cache'])) {
            $arguments[] = $this->getCacheArgument($config, $name, $builder);
        }

        $adapter->setArguments($arguments);
        $adapter->setPublic(true);

        $definitionId = sprintf('consul_api.%s.env_processor', $name);
        $builder->setDefinition($definitionId, $adapter);

        $processor = $builder->findDefinition('consul_api.env_processor');
        $processor->addMethodCall('addAdapter', [
            $namePrefix, new Reference($definitionId),
        ]);

        EnvVarProcessor::addProvidedType($namePrefix);
    }

    protected function getCacheArgument(array $config, $name, ContainerBuilder $builder): Reference
    {
        if (
            is_numeric($config['resolve_env']['cache'])
            && class_exists($builder->findDefinition('consul_api.default_cache')->getClass())
        ) {
            $def = new ChildDefinition('consul_api.default_cache_persister');
            $def->setArgument(1, (int) $config['resolve_env']['cache']);
        } else {
            $def = new Definition(Persister::class, [
                new Reference($config['resolve_env']['cache']),
            ]);
        }

        $srvName = sprintf('consul_api.%s.cache_persister', $name);
        $builder->setDefinition($srvName, $def);

        return new Reference($srvName);
    }

    /**
     * @param string           $name
     * @param array            $conf
     * @param ContainerBuilder $container
     *
     * @return Reference
     */
    private function addServiceDefinition($name, array $conf, ContainerBuilder $container): Reference
    {
        $serviceName = sprintf('consul_api.%s', $name);

        $definition = new ChildDefinition('consul_api.prototype');
        $service = $container->setDefinition(
            $serviceName,
            $definition
        );

        $service->setArguments([$this->getConfigReference($name, $conf, $container)]);

        return new Reference($serviceName);
    }

    /**
     * @param string           $name
     * @param array            $conf
     * @param ContainerBuilder $builder
     *
     * @return Reference
     */
    private function getConfigReference(string $name, array $conf, ContainerBuilder $builder): Reference
    {
        static $mapping = [
            'http_client' => 'HttpClient',
            'addr' => 'Address',
            'scheme' => 'Scheme',
            'datacenter' => 'Datacenter',
            'http_auth' => 'HttpAuth',
            'token' => 'Token',
            'ca_file' => 'CAFile',
            'client_cert' => 'CertFile',
            'client_key' => 'KeyFile',
            'insecure_skip_verify' => 'InsecureSkipVerify',
            'token_in_header' => 'TokenInHeader',
        ];

        $args = [];
        foreach ($conf as $k => $v) {
            if (isset($mapping[$k])) {
                if ('http_client' === $k && $v) {
                    $args[$mapping[$k]] = new Reference($v);
                } else {
                    $args[$mapping[$k]] = $v;
                }
            }
        }

        $name = sprintf('consul_api.%s.config', $name);
        $def = new Definition(Config::class, [$args]);
        $builder->setDefinition($name, $def);

        return new Reference($name);
    }
}
