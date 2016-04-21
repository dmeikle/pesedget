<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Utils;

/**
 * EntityConfigurationLoader
 *
 * @author Dave Meikle
 */
class EntityConfigurationLoader {

    private $entityName;
    private $entityPath;

    public function loadConfiguration($namespacedPath) {
        // components\staff\entities\Staff
        $this->stripValues($namespacedPath);
        $config = null;
        if (strpos($namespacedPath, 'core') === 0) {
            $config = $this->checkDirectory(DIRECTORY_SEPARATOR . 'app');
        } else {
            $config = $this->checkDirectory(DIRECTORY_SEPARATOR . 'src');
        }


        if (is_array($config)) {
            return $config;
        }

        return $this->checkDirectory('app' . DIRECTORY_SEPARATOR . 'core');
    }

    private function checkDirectory($directory) {
        if (!file_exists(__SITE_PATH . $directory . DIRECTORY_SEPARATOR . $this->entityPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'entities.yml')) {
            return false;
        }
        $parser = new YAMLParser();
        $parser->setFilePath(__SITE_PATH . $directory . DIRECTORY_SEPARATOR . $this->entityPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'entities.yml');

        $config = $parser->loadConfig();
        $parser = null;

        return $config;
    }

    private function stripValues($namespacedPath) {
        $tmp = explode('\\', $namespacedPath);

        $this->entityName = array_pop($tmp);

        //drop the entities folder
        array_pop($tmp);

        $this->entityPath = implode(DIRECTORY_SEPARATOR, $tmp);
    }

}
