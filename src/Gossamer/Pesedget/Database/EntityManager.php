<?php

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Utils\YAMLParser;

/**
 * Description of EntityManager
 *
 * @author davem
 */
class EntityManager {

    private static $manager = null;
    private $connections = array();
    private $defaultConnection = null;
    private $config;
    private $entityList = null;

    //to make sure it's not instantiated
    protected function __construct() {

        if (!defined(__SITE_PATH)) {

            //throw new \RuntimeException('__SITE_PATH must be defined in bootstrap');
        }

        $this->loadDatabaseCredentials();
        $this->setDefaultConnection();
    }

    public function __destruct() {
        $this->defaultConnection = null;
        $this->entityList = null;
        self::$manager = null;
    }

    private function setDefaultConnection() {
        if (!array_key_exists('default', $this->config)) {
            throw new \RuntimeException('default key not specified in db credentials configuration');
        }

        $this->defaultConnection = $this->config['default'];
    }

    public static function getInstance() {
        if (is_null(self::$manager)) {
            self::$manager = new EntityManager();
        }

        return self::$manager;
    }

    public function getConnection($dbKey = null) {
        if (is_null($dbKey)) {
            if (is_null($this->defaultConnection)) {
                throw new \Exception('dbkey not passed and no default key specified in entity manager');
            }

            $dbKey = $this->defaultConnection;
        }

        if (!array_key_exists($dbKey, $this->config)) {
            throw new \Exception('dbkey does not exist in entity manager collection');
        }

        return $this->_getConnection($dbKey);
    }

    private function _getConnection($dbKey) {
        if (!array_key_exists($dbKey, $this->connections) || !is_object($this->connections[$dbKey])) {
            $dbClass = $this->config[$dbKey]['class'];
            $credentials = $this->config[$dbKey]['credentials'];

            $this->connections[$dbKey] = new $dbClass($credentials);
        }

        return $this->connections[$dbKey];
    }

    private function loadDatabaseCredentials() {
        $parser = new YAMLParser();
        $parser->setFilePath(__SITE_PATH . '/app/config/credentials.yml');

        $config = $parser->loadConfig();

        $this->config = $config['database'];
    }

    public function getCredentials($dbKey = null) {
        if (is_null($dbKey)) {
            if (is_null($this->defaultConnection)) {
                throw new \Exception('dbkey not passed and no default key specified in entity manager');
            }

            $dbKey = $this->defaultConnection;
        }

        if (!array_key_exists($dbKey, $this->config)) {
            throw new \Exception('dbkey does not exist in entity manager credentials');
        }

        $config = $this->config[$dbKey];

        return $config['credentials'];
    }

    public function getKeys() {
        return array_keys($this->connections);
    }

    public function getEntity($namespacedPath) {
        $entityList = $this->getEntityList();
        //get entityName and entityPath
        $values = $this->getEntityValues($namespacedPath);

        if (!array_key_exists($values['entityPath'], $entityList)) {

            //load the config for this component
            $config = $this->loadEntityConfig($namespacedPath);

            $entityList[$values['entityPath']] = $config;
        }
        $entity = new $namespacedPath();

        if ($entityList[$values['entityPath']] === false) {
            return $entity;
        }
        //ensure the key exists for this component config
        if (array_key_exists($values['entityName'], $entityList[$values['entityPath']])
                //ensure the entity key is there also
                && array_key_exists('entity_db', $entityList[$values['entityPath']][$values['entityName']])) {
            $entity->setDbName($entityList[$values['entityPath']][$values['entityName']]['entity_db']);
        }

        return $entity;
    }

    private function getEntityValues($namespacedPath) {
        $tmp = explode('\\', $namespacedPath);
        $retval = array();
        $retval['entityName'] = array_pop($tmp);

        //drop the entities folder
        array_pop($tmp);

        $retval['entityPath'] = implode(DIRECTORY_SEPARATOR, $tmp);

        return $retval;
    }

    private function loadEntityConfig($namespacedPath) {
        $loader = new \Gossamer\Pesedget\Utils\EntityConfigurationLoader();

        return $loader->loadConfiguration($namespacedPath);
    }

    private function getEntityList() {
        if (is_null($this->entityList)) {
            $this->entityList = array();
        }

        return $this->entityList;
    }

}
