<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/21/2016
 * Time: 9:58 PM
 */

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Utils\Config;
use Gossamer\Pesedget\Utils\ConfigManager;
use Gossamer\Pesedget\Exceptions\TableNotFoundException;

class ColumnDataTypeMappings extends ColumnMappings
{

   
    /**
     * getColumnMappingsFromConfig retrieves serialized column list specific to a table
     *
     * @param string    tablename
     *
     * @return array    list of columns
     *
     */
    protected function getColumnMappingsFromConfig($tableName) {

        $filename = "$tableName.conf";
        $configManager = new ConfigManager();
        $config = $configManager->getConfiguration($filename);

        if (is_null($config)) {
            $result = $this->dbConnection->query('SHOW COLUMNS FROM ' . $tableName);

            if (is_null($result)) {

                throw new TableNotFoundException('table ' . $tableName . ' not found');
            }

            $columnNames = array();
            foreach ($result as $object => $values) {
                //array_push($columnNames, array($values['Field'] => $values));
                $columnNames[$values['Field']] = $values;
            }

            $config = new Config($columnNames);

            $configManager->saveConfiguration($filename, $config);
        }

        return $config->toDetailsArray();
    }
}