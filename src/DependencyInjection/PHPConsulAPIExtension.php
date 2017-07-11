<?php namespace DCarbone\PHPConsulAPIBundle\DependencyInjection;

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

use DCarbone\PHPConsulAPIBundle\Twig\PHPConsulAPITwigExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PHPConsulAPIExtension
 * @package DCarbone\PHPConsulAPIBundle\DependencyInjection
 */
class PHPConsulAPIExtension extends Extension {
    /**
     * @return string
     */
    public function getAlias() {
        return 'consul_api';
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return Configuration
     */
    public function getConfiguration(array $config, ContainerBuilder $container) {
        return new Configuration();
    }

    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container) {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('consul_api.yml');

        $bundles = $container->getParameter('kernel.bundles');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $defaultConfiguration = $config['default_configuration'];
        $namedConfigurations = $config['named_configurations'];

        $container->setParameter('consul_api.default_configuration_name', $defaultConfiguration);
        $container->setAlias('consul_api.default.config', sprintf('consul_api.%s.config', $defaultConfiguration));

        $configNames = [];

        foreach($namedConfigurations as $name => $conf) {
            $configNames[] = $name;
            $this->_addServiceDefinition($name, $conf, $container);
        }

        $container->setParameter('consul_api.config_names', $configNames);

        $bag = $container->getDefinition('consul_api.bag');
        $bag->addArgument(new Parameter('consul_api.default_configuration_name'));

        $namedConsuls = [];
        foreach($configNames as $configName) {
            $namedConsuls[$configName] = new Reference(sprintf('consul_api.%s', $configName));
        }
        $bag->addArgument($namedConsuls);

        $container->setAlias('consul_api.default', sprintf('consul_api.%s', $defaultConfiguration));

        // Load twig extension if twig is loaded
        if (isset($bundles['TwigBundle'])) {
            $service = new Definition(
                PHPConsulAPITwigExtension::class,
                array(new Reference('consul_api.bag'), new Parameter('consul_api.config_names'))
            );

            $service->addTag('twig.extension');

            $container->setDefinition('consul_api.twig.extension', $service);
        }
    }

    /**
     * @param string $name
     * @param array $conf
     * @param ContainerBuilder $container
     */
    private function _addServiceDefinition($name, array $conf, ContainerBuilder $container) {
        $serviceName = sprintf('consul_api.%s', $name);
        $configName = sprintf('%s.config', $serviceName);

        $container->setDefinition(
            $configName,
            $this->_newConfigDefinition($conf)
        );

        $service = $container->setDefinition(
            $serviceName,
            new ChildDefinition('consul_api.local')
        );

        $service->setArguments([new Reference($configName)]);
    }

    /**
     * @param array $conf
     * @return Definition
     */
    private function _newConfigDefinition(array $conf) {
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
        foreach($conf as $k => $v){
            if (isset($mapping[$k])) {
                if ('http_client' === $k) {
                    $args[$mapping[$k]] = new Reference($v);
                } else {
                    $args[$mapping[$k]] = $v;
                }
            }
        }

        return new Definition('DCarbone\\PHPConsulAPI\\Config', [$args]);
    }
}