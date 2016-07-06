<?php

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\DBConnection;
use Gossamer\Pesedget\Utils\Config;
use Gossamer\Pesedget\Utils\ConfigManager;
use Gossamer\Pesedget\Exceptions\TableNotFoundException;

/**
 * Column Mappings Class - gets entity columns from table and serializes array
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
class ColumnMappings {

    /**
     * list of tables mapped and loaded
     */
    private $tableList = array();
    
    /**
     * DBConnection
     */
    protected $dbConnection = null;

    /**
     * constructor
     *
     * @param DBConnection  connection
     */
    public function __construct(DBConnection $dbConnection) {
        if (strlen(__CACHE_DIRECTORY) < 1) {
            throw new \RuntimeException('__CACHE_DIRECTORY must be defined during bootstrap');
        }

        $this->dbConnection = $dbConnection;
    }

    /**
     * getTableColumnList
     *
     * @param string    $tableName
     *
     * @return array    list of columns
     */
    public function getTableColumnList($tableName) {

        if (!array_key_exists($tableName, $this->tableList)) {

            $this->addColumnMap($tableName, $this->getColumnMappingsFromConfig($tableName));
        }

        return $this->tableList[$tableName];
    }

    /**
     * addColumnMap - adds an array of columns to a the tableList
     *
     * @param string    tableName
     * @param array     list of columns
     */
    private function addColumnMap($tableName, $columns = array()) {

        $this->tableList[$tableName] = $columns;
    }

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

        return $config->toArray();
    }
    
   

}
