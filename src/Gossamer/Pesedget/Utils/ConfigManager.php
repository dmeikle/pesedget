<?php

namespace Gossamer\Pesedget\Utils;

use Gossamer\Pesedget\Utils\ManagerInterface;
use Gossamer\Pesedget\Utils\Config;
use Gossamer\Caching\CacheManager;

/**
 * ConfigManager Class
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
class ConfigManager implements ManagerInterface {

    /**
     * path to locate/save configurations
     */
    private $workingPath;

    /**
     * access rights for IO
     */
    const FILE_PUT_CONTENTS_ATOMIC_MODE = '0777';

    /**
     * constructor
     *
     * @param array injectables
     */
    public function __construct($injectables = array()) {

    }

    /**
     * parseFilepath - parses the path to access files, dropping the current filename
     *
     * @param string    filepath
     *
     * @return string   path to folder for saving/access
     */
    private function parseFilepath($filepath) {

        $retval = explode('/', $filepath);
        array_pop($retval);

        return implode('/', $retval);
    }

    /**
     * getConfiguration - loads the configuration
     *
     * @param string    filename
     * @param Config    loaded config
     */
    public function getConfiguration($filename) {

        $cacheManager = new CacheManager();
        $configuration = $cacheManager->retrieveFromCache('/' . $filename);
        if (!is_array($configuration)) {
            return null;
        }
        $config = new Config($configuration);
        $cacheManager = null;

        return $config;
    }

    /**
     * saveConfiguration - serializes a configuration
     *
     * @param string    filename
     * @param Config    config
     */
    public function saveConfiguration($filename, Config $config) {
        $this->workingPath = $this->parseFilepath($filename);

        $cacheManager = new CacheManager();
        $cacheManager->saveToCache('/' . $filename, $config->toDetailsArray());
    }

}
