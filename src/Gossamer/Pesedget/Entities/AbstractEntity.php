<?php

namespace Gossamer\Pesedget\Entities;


abstract class AbstractEntity
{
    protected $tablename;

    protected $primaryKeys = array('id');

    protected $dbName = null;
    
    public function __construct(){	    
        $this->tablename = $this->stripNamespacing(get_class($this)) . 's';
    }

    private function stripNamespacing($namespacedEntity) {
        $chunks = explode('\\', $namespacedEntity);

        return array_pop($chunks);
    }

    public function getTableName(){
        return $this->tablename;
    }
    
    /**
     * intended for use with ShardableInterface
     * @return string
     */
    public function getDBName() {
        if(is_null($this->dbName)) {
            return '';
        }
        
        return '`' . $this->dbName . '`.';
    }

    /**
     * intended for use with ShardableInterface
     * @param string
     */
    public function setDbName($dbName) {
       $this->dbName = $dbName; 
    }

    /**
     * returns the FIRST column as the ID column - intended to be 'id'
     * 
     * @return type string
     */
    public function getIdentityColumn(){
        return $this->primaryKeys[0];
    }

    public function getI18nIdentifier(){
        return $this->getTableName() . '_id';
    }

    public function populate($params = array()){
        foreach ($params as $key => $value) {
            if(is_int($key)){
                continue;
            }

            $this->$key = $value;
        }
    }
    
    public function getPrimaryKeys(){
        return $this->primaryKeys;
    }
}
