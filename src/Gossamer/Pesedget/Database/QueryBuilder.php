<?php

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Utils\ManagerInterface;
use Gossamer\Pesedget\Database\SQLInterface;
use Gossamer\Pesedget\Entities\AbstractI18nEntity;
use Gossamer\Pesedget\Database\DBConnection;
use Gossamer\Pesedget\Database\ColumnMappings;

class QueryBuilder implements ManagerInterface {

    const SAVE_QUERY = 'save';
    const DELETE_QUERY = 'delete';
    const GET_ITEM_QUERY = 'get';
    const GET_ALL_ITEMS_QUERY = 'getall';
    const GET_COUNT_QUERY = 'getcount';
    const PARENT_ONLY = 'parentOnly';
    const CHILD_ONLY = 'childOnly';
    const PARENT_AND_CHILD = 'parentAndChild';

    private $fields = null; //derived from passed in array
    private $fieldNames = array(); //derived from values
    private $values = null;
    private $andFilter = null;
    private $orFilter = null;
    private $offset = 0;
    private $limit = 0;
    private $concatenator = ' AND ';
    private $tableName = '';
    private $primaryKeys = null;
    private $tableColumns = null;
    private $tableI18nColumns = null;
    private $i18nJoin = null;
    private $orderBy = null;
    private $dbConnection = null;
    private $joinTables = null;
    private $encodingHandler = null;
    private $queryingI18n = false;
    private $isBulkInsert = false;
    private $isLikeSearch = false;
    
    public function __construct($injectables = array()) {
        if (array_key_exists('dbConnection', $injectables)) {
            //perhaps using a project db
            $this->dbConnection = $injectables['dbConnection'];
        }
    }

    public function setIsLikeSearch($isLike) {
        $this->isLikeSearch = $isLike;
    }
    
    public function join(array $joins) {
        if (is_null($this->joinTables)) {
            $this->joinTables = array();
        }
        $this->joinTables = $joins;
    }

    private function buildJoins() {
        $retval = '';
        foreach ($this->joinTables as $objectName => $join) {
            $object = new $objectName();

            $retval .= ' LEFT JOIN ' . $object->getDBName() . $object->getTableName() . ' ON ' . $join[0] . ' = ' . $join[1];
            if ($object instanceof AbstractI18nEntity) {
                //add its locale values
                $retval .= ' LEFT JOIN ' . $object->getDBName() . $object->getI18nTablename() . ' ON ' . $object->getI18nTablename() .
                        '.' . $object->getTableName() . '_id' . ' = ' . $join[1];
                if (array_key_exists('locale', $this->andFilter)) {
                    $retval .= ' AND ' . $object->getDBName() . $object->getI18nTablename() . '.locale = \'' . $this->andFilter['locale'] . '\'';
                }
            }
        }

        return $retval;
    }

    private function setTablename(SQLInterface $entity, $i18nQueryType) {
        if (self::CHILD_ONLY == $i18nQueryType && $entity instanceof AbstractI18nEntity) {
            $this->tableName = $entity->getI18nTablename();
        } else {
            $this->tableName = $entity->getTableName();
        }
    }

    private function setPrimaryKeys(SQLInterface $entity, $i18nQueryType) {
        if (!is_null($i18nQueryType) && $entity instanceof AbstractI18nEntity) {
            $this->primaryKeys = $entity->getI18nPrimaryKeys();
        } else {
            $this->primaryKeys = $entity->getPrimaryKeys();
        }
    }

    private function init(SQLInterface $entity, $i18nQueryType, $queryType, $resetParams) {
        if($resetParams) {
            $this->fields = null;
        }
        $this->tableColumns = null;
        $this->tableI18nColumns = null;
        $this->fieldNames = null;
        $this->i18nJoin = null;
        $this->setTablename($entity, $i18nQueryType);


        $this->setPrimaryKeys($entity, $i18nQueryType);

        $this->loadActualColumns($entity, $i18nQueryType);
    }

    public function getQuery(SQLInterface $entity, $queryType = 'getall', $i18nQueryType = null, $queryingI18n = false, $resetParams = true) {
        if (!$entity instanceof SQLInterface) {
            throw new \RuntimeException('entity must implement SQLInterface');
        }
        if ($entity instanceof AbstractI18nEntity) {
            //u$this->encodingHandler = new UnicodeHandler();
        }
        $this->queryingI18n = $queryingI18n;

        $this->init($entity, $i18nQueryType, $queryType, $resetParams);
        $query = '';
        if ($queryType == self::DELETE_QUERY) {
            $query = $this->buildDeleteStatement();
        } elseif ($queryType == self::SAVE_QUERY) {
            $query = $this->buildSaveStatement();
        } elseif ($queryType == self::GET_ITEM_QUERY) {
            $query = $this->buildSelectStatement(true, $entity, $queryType);
        } elseif ($queryType == self::GET_ALL_ITEMS_QUERY) {
            $query = $this->buildSelectStatement(false, $entity, $queryType);
        } elseif ($queryType == self::GET_COUNT_QUERY) {
            $query = $this->buildCountStatement();
        }

        return $query; // preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $query);
    }

    private function getDBConnection() {

        //did we NOT receive an overriding connection? ok, create a default instance
        if (is_null($this->dbConnection)) {

            $this->dbConnection = new DBConnection();
        }

        return $this->dbConnection;
    }

    private function loadActualColumns(SQLInterface $entity, $i18nQueryType) {

        $dbConnection = $this->getDBConnection();

        $columnMappings = new ColumnMappings($dbConnection);

        if (!$entity instanceof AbstractI18nEntity || (is_null($i18nQueryType))) {

            $this->tableColumns = $columnMappings->getTableColumnList($entity->getTableName());
        } elseif (($entity instanceof AbstractI18nEntity) && self::PARENT_AND_CHILD == $i18nQueryType) {

            //used for select statements only, but needs to join across tables
            $this->tableColumns = $columnMappings->getTableColumnList($entity->getTableName());
            //if it's an i18n select then query the locales table too
            //array_merge($this->tableColumns, $columnMappings->getTableColumnList($entity->getI18nTablename()));
            $this->tableI18nColumns = $columnMappings->getTableColumnList($entity->getI18nTablename());
            //set this flag so we can call it later
            $this->i18nJoin = $this->joinI18nTable($entity);
        } elseif (($entity instanceof AbstractI18nEntity) && self::CHILD_ONLY == $i18nQueryType) {


            $this->tableColumns = $columnMappings->getTableColumnList($entity->getI18nTablename());
        } elseif (($entity instanceof AbstractI18nEntity) && self::PARENT_ONLY == $i18nQueryType) {

            $this->tableColumns = $columnMappings->getTableColumnList($entity->getTableName());
        }


        unset($columnMappings);
    }

    private function joinI18nTable(SQLInterface $entity) {

        return ' JOIN ' . $entity->getDBName() . $entity->getI18nTablename() . ' ON ' . $entity->getTableName() . '.id = ' . $entity->getI18nTablename() .
                '.' . $entity->getI18nIdentifier();
    }

    public function where($filter) {

        $this->andFilter = $filter;
    }

    public function getWhere() {
        return $this->getWhereStatement();
    }

    public function orWhere($filter) {
        $this->orFilter = $filter;
    }

    public function setValues($values) {
        $this->values = $values;
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

    private function buildCountStatement() {
        $select = 'SELECT COUNT(' . $this->tableName . '.id) as rowCount ';


        $select .= ' FROM ' . $this->tableName;
        if (!is_null($this->i18nJoin)) {
            $select .= $this->i18nJoin;
        }

        if (!is_null($this->joinTables)) {
            $select .= $this->buildJoins();
        }
        unset($this->andFilter['directive::OFFSET']);
        unset($this->andFilter['directive::LIMIT']);
        unset($this->andFilter['directive::DIRECTIVE']);
        
        $select .= $this->getWhereStatement();

        return $select;
    }

    private function buildSelectStatement($firstRowOnly = false, SQLInterface $entity, $queryType) {
        $select = 'SELECT ';
//        if(!$firstRowOnly) {
//            $select .= 'SQL_CALC_FOUND_ROWS ';
//        }
     
        if (!is_null($this->fields)) {
            $select .= implode(',', $this->fields);
        }elseif (in_array('id', $this->tableColumns)) {
            $select .= '*, ' . $this->tableName . '.id as ' . $this->tableName . '_id';
        }elseif($queryType == self::CHILD_ONLY) {
            $select .= '*'; //this is because there's no guarantee we have an id column on child only queries
        } elseif (!$this->queryingI18n && substr($this->tableName, -4) != 'I18n' && in_array($this->tableName . '_id', $this->tableColumns)) {
            $select .= '*, ' . $this->tableName . '.id as ' . $this->tableName . '_id';
        } else {
            //don't build a custom column since the 'id' column does not exist in i18n tables
            $select .= '*';
        }

        $select .= ' FROM ' . $entity->getDBName() . $this->tableName;
        if (!is_null($this->i18nJoin)) {
            $select .= $this->i18nJoin;
        }

        if (!is_null($this->joinTables)) {
            $select .= $this->buildJoins();
        }
        $this->parseDirectives();
        $select .= $this->getWhereStatement();


        $select .= $this->getOrderBy();

        $select .= $this->getOffset($firstRowOnly);

        return $select;
    }

    private function getOrderBy() {
        if (!is_null($this->orderBy)) {
            return $this->orderBy;
        }
    }

    public function joinTable($tablenameToJoin, $columnsToJoinOn = array()) {

        return ' JOIN ' . $tablenameToJoin . ' ON ' . $columnsToJoinOn[0] . ' = ' . $columnsToJoinOn[1];
    }

    private function getOffset($firstRowOnly = false) {
        if ($firstRowOnly) {
            return ' LIMIT 1';
        }
        if ($this->limit > 0) {
            return ' LIMIT ' . $this->offset . ',' . $this->limit;
        }
        return '';
    }

    private function getWhereStatement() {

        if (is_null($this->andFilter) && is_null($this->orFilter)) {
            return '';
        }

        $where = '';
        $hasFilter = false;

        $andWhere = $this->buildAndWhereFilter();

        $orWhere = $this->buildOrWhereFilter();

        if (strlen($andWhere) > 0) {
            $where .= $andWhere;
            $hasFilter = true;
        }
        if (strlen($orWhere) > 0) {
            if ($hasFilter) {
                
            }
            $where .= (($hasFilter) ? ' OR ' : '') . $orWhere;
            $hasFilter = true;
        }

        return ($hasFilter && strlen($where) > 2) ? ' WHERE ' . $where : '';
    }

    private function buildAndWhereFilter() {

        if (is_null($this->andFilter) || count($this->andFilter) == 0) {
            return '';
        }

        //method has passed in a hard string for filtering
        if (!is_null($this->andFilter) && !is_array($this->andFilter)) {
            return $this->andFilter;
        }

        $where = '';
        
        foreach ($this->andFilter as $key => $val) {
         
            if (!$this->checkColumnExists($key)) {
                
                continue;
            }
        
            $whereTable = '';
            if(in_array($key, $this->tableColumns)) {
                $whereTable = '`' . $this->tableName . '`.';
            }elseif(!is_null($this->tableI18nColumns) && in_array($key, $this->tableI18nColumns)) {
                $whereTable = '`' . $this->tableName . 'I18n' . '`.';
            }
            if($this->isLikeSearch) {
                $where .= ' AND (' . $whereTable . '`' . $key . '` like \'%' . $val . '%\'';
            } else {
                $where .= ' AND (' . $whereTable . '`' . $key . '` = \'' . $val . '\'';
            }
            
//            if(!is_null($this->encodingHandler)) {
//                $where .= ' or `' . $key. '` = \'' . $this->encodingHandler->ascii2hex($val).'\'';
//            }

            $where .= ')';
        }

        return '(' . substr($where, 4) . ')';
    }

    private function checkColumnExists($column) {
        if(in_array($column, $this->tableColumns)) {
            return true;
        }
        if(!is_null($this->tableI18nColumns) && in_array($column, $this->tableI18nColumns)) {
            return true;
        }
        
        return false;
    }
    private function buildOrWhereFilter() {
        if (is_null($this->orFilter) || count($this->orFilter) == 0) {
            return '';
        }
        //method has passed in a hard string for filtering
        if (!is_null($this->orFilter) && !is_array($this->orFilter)) {
            return $this->orFilter;
        }

        $where = '';
        foreach ($this->orFilter as $key => $val) {
            if (!$this->checkColumnExists($key)) {
                continue;
            }
            $whereTable = '';
            if(in_array($key, $this->tableColumns)) {
                $whereTable = '`' . $this->tableName . '`.';
            }elseif(!is_null($this->tableI18nColumns) &&in_array($key, $this->tableI18nColumns)) {
                $whereTable = '`' . $this->tableName . 'I18n' . '`.';
            }
            if($this->isLikeSearch) {
                $where .= ' OR (' . $whereTable . '`' . $key . '` like \'%' . $val . '%\'';
            } else {
                $where .= ' OR (' . $whereTable . '`' . $key . '` = \'' . $val . '\'';
            }

            $where .= ')';
        }

        return '(' . substr($where, 3) . ')';
    }

    private function buildDeleteStatement() {
        return 'DElETE FROM ' . $this->tableName . $this->getWhereStatement();
    }

    private function buildSaveStatement() {
        $query = '';
        //need to parse values first since this will configure our matching field names
        //parse values before all else so we know if it's a bulk insert or not
        $values = $this->parseValuesToInsert();

        if (!$this->isBulkInsert) {
            //insert into table
            $query = 'INSERT INTO ' . $this->tableName;
        } else {
            $query = 'REPLACE INTO ' . $this->tableName;
        }


        //(col1,col2,col3)
        $fieldNames = $this->parseFieldNames();

        $query .= $fieldNames . $values;

        if (!$this->isBulkInsert) {
            //this would normally be enough, but we rely on primary key for detecting updates
            $query .= ' ON DUPLICATE KEY UPDATE ' . $this->buildUpdateStatement();
        }

        return $query;
    }

    private function buildUpdateStatement() {
        $values = '';
        $modifiedColumn = false;
        foreach ($this->values as $key => $val) {
            //don't try to update primary keys
            if (in_array($key, $this->primaryKeys)) {
                continue;
            }
            //only accept columns that exist in the table
            if (!in_array($key, $this->tableColumns)) {
                continue;
            }
            if (strtolower($val) == 'null') {
                $values .= ', `' . $key . '` = null';
            } else {
                $values .= ', `' . $key . '` = \'' . $val . '\'';
            }
            if($key == 'lastModified') {
                $modifiedColumn = true;
            }
        }
        
        if(!$modifiedColumn && in_array('lastModified', $this->tableColumns)) {
            $values .= ', `lastModified` = null';
        }
        
        return substr($values, 1);
    }

    public function setBulkInsert(array $values) {
        $this->isBulkInsert = true;
        $this->values = $values;
       
    }

    private function parseValuesToInsert() {
        $values = '';
        $modifiedColumn = false;
        

        if (is_array(current($this->values)) && $this->isBulkInsert) {
            return $this->parseArray();
        }

        foreach ($this->values as $key => $value) {

            if (!in_array($key, $this->tableColumns)) {

                continue;
            }
            $this->fieldNames[] = $key;
            
            if (strtolower($value) == 'null') {
                $values .= ', null';
            } else {
                $values .= ', \'' . ($value) . '\'';
            }
            if($key == 'lastModified') {
                $modifiedColumn = true;
            }
        }

//        if(!$modifiedColumn && in_array('lastModified', $this->tableColumns)) {
//            $values .= ', `lastModified` = null';
//        }
        
        return ' VALUES (' . substr($values, 1) . ')';
    }

    private function parseArray() {
        $retval = '';
        $this->isBulkInsert = true;
        
        
        foreach ($this->values as $row) {
            $rowVal = '';
            foreach ($row as $key => $value) {
                
                if (!in_array($key, $this->tableColumns)) {
                    continue;
                }
                if (!is_null($this->fieldNames) && !in_array($key, $this->fieldNames)) {
                    $this->fieldNames[] = $key;
                }

                if (strtolower($value) == 'null') {
                    $rowVal .= ', null';
                } else {
                    $rowVal .= ', \'' . ($value) . '\'';
                }
                
            }
            
            if (strlen($rowVal) > 4) {
                //only add if we hold a value
                $retval .= ', (' . substr($rowVal, 1) . ')';
            }
            
        }

        
        
        return ' VALUES ' . substr($retval, 1);
    }

    private function parseDirectives() {
        if(is_null($this->andFilter)) {
            return;
        }
        foreach ($this->andFilter as $key => $value) {

            if (strpos($key, 'directive::') === FALSE) {
                continue;
            }

            if ('directive::ORDER_BY' == $key) {
                $this->setOrderBy($value);
                unset($this->andFilter['directive::ORDER_BY']);
            }
            if ('directive::DIRECTION' == $key) {
                $this->setDirection($value);
                unset($this->andFilter['directive::DIRECTION']);
            }
            if ('directive::LIMIT' == $key) {
                $this->setLimit($this->andFilter['directive::OFFSET'], $this->andFilter['directive::LIMIT']);
                unset($this->andFilter['directive::OFFSET']);
                unset($this->andFilter['directive::LIMIT']);
            }
        }
    }

    private function setOrderBy($columnAndDirection) {
        $this->orderBy = ' order by ' . $columnAndDirection;
    }

    private function setDirection($direction) {
        $this->orderBy .= ' ' . $direction;
    }

    private function setLimit($offset, $limit) {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    private function parseFieldNames() {

        if (is_null($this->fields)) {
           
            if (is_null($this->fieldNames)) {
                
                $firstRow = ($this->values[0]);
               //we only want column names for passed values so that our insert column count matches
                $passedColumns = array_intersect(array_keys($firstRow), $this->tableColumns);
       
                return '(`' . implode('`,`', $passedColumns) . '`)';
            }
            return '(`' . implode('`,`', $this->fieldNames) . '`)';
        }
        
        if (!is_array($this->fields)) {
            return '(`' . $this->fields . '`)';
        }
        
        return '(`' . implode('`,`', $this->fields) . '`)';
    }

    public function setDBConnection(DBConnection $conn) {
        $this->dbConnection = $conn;
    }
}
