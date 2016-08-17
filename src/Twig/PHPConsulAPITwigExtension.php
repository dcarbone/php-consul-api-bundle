<?php namespace DCarbone\PHPConsulAPIBundle\Twig;

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

use DCarbone\PHPConsulAPI\Agent\AgentCheckRegistration;
use DCarbone\PHPConsulAPI\Agent\AgentServiceRegistration;
use DCarbone\PHPConsulAPI\KV\KVPair;
use DCarbone\PHPConsulAPI\Session\SessionEntry;
use DCarbone\PHPConsulAPIBundle\Bag\ConsulBag;

/**
 * Class PHPConsulAPIExtension
 * @package DCarbone\PHPConsulAPIBundle\Twig
 */
class PHPConsulAPITwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /** @var array */
    private $_cns;
    /** @var ConsulBag */
    private $_cb;

    /**
     * PHPConsulAPIExtension constructor.
     * @param ConsulBag $consulBag
     * @param array $configNames
     */
    public function __construct(ConsulBag $consulBag, array $configNames)
    {
        $this->_cns = $configNames;
        $this->_cb = $consulBag;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'php_consul_api';
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'consul_api_config_names' => $this->_cns,
        );
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(

            // Client getters
            new \Twig_SimpleFunction('consul_kv', array($this, 'kv')),
            new \Twig_SimpleFunction('consul_agent', array($this, 'agent')),
            new \Twig_SimpleFunction('consul_catalog', array($this, 'catalog')),
            new \Twig_SimpleFunction('consul_status', array($this, 'status')),
            new \Twig_SimpleFunction('consul_event', array($this, 'event')),
            new \Twig_SimpleFunction('consul_health', array($this, 'health')),
            new \Twig_SimpleFunction('consul_coordinate', array($this, 'coordinate')),
            new \Twig_SimpleFunction('consul_session', array($this, 'session')),


            // Shortcuts
            new \Twig_SimpleFunction('consul_kv_get', array($this, 'kvGet')),
            new \Twig_SimpleFunction('consul_kv_list', array($this, 'kvList')),
            new \Twig_SimpleFunction('consul_kv_keys', array($this, 'kvKeys')),

            new \Twig_SimpleFunction('consul_catalog_services', array($this, 'catalogServices')),
            new \Twig_SimpleFunction('consul_catalog_service', array($this, 'catalogService')),
            new \Twig_SimpleFunction('consul_catalog_datacenters', array($this, 'catalogDatacenters')),
            new \Twig_SimpleFunction('consul_catalog_node', array($this, 'catalogNode')),
            new \Twig_SimpleFunction('consul_catalog_nodes', array($this, 'catalogNodes')),

            new \Twig_SimpleFunction('consul_coord_datacenters', array($this, 'coordinateDatacenters')),
            new \Twig_SimpleFunction('consul_coord_nodes', array($this, 'coordinateNodes')),

            new \Twig_SimpleFunction('consul_event_list', array($this, 'eventList')),

            new \Twig_SimpleFunction('consul_health_node', array($this, 'healthNode')),
            new \Twig_SimpleFunction('consul_health_checks', array($this, 'healthChecks')),
            new \Twig_SimpleFunction('consul_health_service', array($this, 'healthService')),
            new \Twig_SimpleFunction('consul_health_state', array($this, 'healthState')),

            new \Twig_SimpleFunction('consul_session_info', array($this, 'sessionInfo')),
            new \Twig_SimpleFunction('consul_session_node', array($this, 'sessionNode')),
            new \Twig_SimpleFunction('consul_session_list', array($this, 'sessionList')),

            new \Twig_SimpleFunction('consul_status_leader', array($this, 'statusLeader')),
            new \Twig_SimpleFunction('consul_status_peers', array($this, 'statusPeers')),

        );
    }


    // Client getter methods


    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\KV\KVClient
     */
    public function kv($configName = 'default') { return $this->_cb->getNamed($configName)->KV; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Agent\AgentClient
     */
    public function agent($configName = 'default') { return $this->_cb->getNamed($configName)->Agent; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogClient
     */
    public function catalog($configName = 'default') { return $this->_cb->getNamed($configName)->Catalog; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Status\StatusClient
     */
    public function status($configName = 'default') { return $this->_cb->getNamed($configName)->Status; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Event\EventClient
     */
    public function event($configName = 'default') { return $this->_cb->getNamed($configName)->Event; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Health\HealthClient
     */
    public function health($configName = 'default') { return $this->_cb->getNamed($configName)->Health; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Coordinate\CoordinateClient
     */
    public function coordinate($configName = 'default') { return $this->_cb->getNamed($configName)->Coordinate; }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Session\SessionClient
     */
    public function session($configName = 'default') { return $this->_cb->getNamed($configName)->Session; }


    // Object creator methods


    /**
     * @param array $params
     * @return KVPair
     */
    public function newKVPair(array $params = array()) { return new KVPair($params); }

    /**
     * @param array $params
     * @return AgentServiceRegistration
     */
    public function newAgentServiceRegistration(array $params = array()) { return new AgentServiceRegistration($params); }

    /**
     * @param array $params
     * @return AgentCheckRegistration
     */
    public function newAgentCheckRegistration(array $params = array()) { return new AgentCheckRegistration($params); }

    /**
     * @param array $params
     * @return SessionEntry
     */
    public function newSessionEntry(array $params = array()) { return new SessionEntry($params); }


    // Shortcut methods


    /**
     * @param string $key
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\KV\KVPair
     */
    public function kvGet($key, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\KV\KVPair $kvp */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($kvp, $_, $err) = $this->_cb->getNamed($configName)->KV->get($key);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $kvp;
    }

    /**
     * @param string $prefix
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\KV\KVPair[]
     */
    public function kvList($prefix = '', $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\KV\KVPair[] $list */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($list, $_, $err) = $this->_cb->getNamed($configName)->KV->valueList($prefix);
        if (null !== $err)
            throw new \RuntimeException($err);

        return $list;
    }

    /**
     * @param string $prefix
     * @param string $configName
     * @return string[]
     */
    public function kvKeys($prefix = '', $configName = 'default')
    {
        /** @var string[] $keys */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($keys, $_, $err) = $this->_cb->getNamed($configName)->KV->keys($prefix);
        if (null !== $err)
            throw new \RuntimeException($err);

        return $keys;
    }

    /**
     * @param string $name
     * @param string $tags
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogService[]
     */
    public function catalogServices($name, $tags = '', $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Catalog\CatalogService[] $services */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($services, $_, $err) = $this->_cb->getNamed($configName)->Catalog->service($name, (string)$tags);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $services;
    }

    /**
     * @param string $name
     * @param string $tags
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogService
     */
    public function catalogService($name, $tags = '', $configName = 'default')
    {
        $services = $this->catalogServices($name, $tags, $configName);
        return reset($services);
    }

    /**
     * @param string $configName
     * @return string[]
     */
    public function catalogDatacenters($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($datacenters, $err) = $this->_cb->getNamed($configName)->Catalog->datacenters();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $datacenters;
    }

    /**
     * @param string $node
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogNode
     */
    public function catalogNode($node, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Catalog\CatalogNode $node */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($node, $_, $err) = $this->_cb->getNamed($configName)->Catalog->node($node);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $node;
    }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Catalog\CatalogNode[]
     */
    public function catalogNodes($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($nodes, $_, $err) = $this->_cb->getNamed($configName)->Catalog->nodes();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $nodes;
    }

    /**
     * @param $node
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Health\HealthCheck[]
     */
    public function healthNode($node, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Health\HealthCheck[] $hcs */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($hcs, $_, $err) = $this->_cb->getNamed($configName)->Health->node($node);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $hcs;
    }

    /**
     * @param string $service
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Health\HealthCheck[]
     */
    public function healthChecks($service, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Health\HealthCheck[] $hcs */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($hcs, $_, $err) = $this->_cb->getNamed($configName)->Health->checks($service);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $hcs;
    }

    /**
     * @param string $service
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Health\ServiceEntry[]
     */
    public function healthService($service, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Health\ServiceEntry[] $services */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($services, $_, $err) = $this->_cb->getNamed($configName)->Health->service($service);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $services;
    }

    /**
     * @param string $state
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Health\HealthCheck[]
     */
    public function healthState($state, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Health\HealthCheck[] $hcs */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($hcs, $_, $err) = $this->_cb->getNamed($configName)->Health->state($state);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $hcs;
    }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Coordinate\CoordinateDatacenterMap[]
     */
    public function coordinateDatacenters($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Coordinate\CoordinateDatacenterMap[] $dcm */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($dcm, $err) = $this->_cb->getNamed($configName)->Coordinate->datacenters();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $dcm;
    }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Coordinate\CoordinateEntry[]
     */
    public function coordinateNodes($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Coordinate\CoordinateEntry[] $ces */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($ces, $_, $err) = $this->_cb->getNamed($configName)->Coordinate->nodes();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $ces;
    }

    /**
     * @param string $name
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Event\UserEvent[]
     */
    public function eventList($name = '', $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Event\UserEvent[] $el */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($el, $_, $err) = $this->_cb->getNamed($configName)->Event->eventList($name);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $el;
    }

    /**
     * @param string $id
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Session\SessionEntry[]
     */
    public function sessionInfo($id, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Session\SessionEntry[] $ses */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($ses, $_, $err) = $this->_cb->getNamed($configName)->Session->info($id);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $ses;
    }

    /**
     * @param string $node
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Session\SessionEntry[]
     */
    public function sessionNode($node, $configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Session\SessionEntry[] $ses */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($ses, $_, $err) = $this->_cb->getNamed($configName)->Session->node($node);
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $ses;
    }

    /**
     * @param string $configName
     * @return \DCarbone\PHPConsulAPI\Session\SessionEntry[]
     */
    public function sessionList($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Session\SessionEntry[] $ses */
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($ses, $_, $err) = $this->_cb->getNamed($configName)->Session->listSessions();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $ses;
    }

    /**
     * @param string $configName
     * @return string
     */
    public function statusLeader($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($name, $err) = $this->_cb->getNamed($configName)->Status->leader();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $name;
    }

    /**
     * @param string $configName
     * @return string[]
     */
    public function statusPeers($configName = 'default')
    {
        /** @var \DCarbone\PHPConsulAPI\Error $err */
        list($peers, $err) = $this->_cb->getNamed($configName)->Status->peers();
        if (null !== $err)
            throw new \RuntimeException($err->getMessage());

        return $peers;
    }
}