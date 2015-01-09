<?php

namespace Gossamer\Pesedget\Entities;


abstract class AbstractEntity
{
    protected $tablename;

    protected $primaryKeys = array('id');

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

    public function getIdentityColumn(){
        return $this->primaryKeys;
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
