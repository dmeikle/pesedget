<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/18/2016
 * Time: 12:32 PM
 */

namespace Gossamer\Pesedget\Entities;


use triagens\ArangoDb\Document;

class ArangoEntity extends Document
{

    /**
     * can be used as an internal configuration, but can be overwritten in each class if needed
     * 
     * @var array
     */
    protected $fields = array('id');

    /**
     * assigns the values of the passed in params to the document.
     *
     * @param array $params - the values to assign
     * @param array|null $fields - the field names to look for in the array
     */
    public function populate(array $params, array $fields = null) {
        if(!is_null($fields)) {
            $this->fields = $fields;
        }

        $list = array_intersect_key($this->fields, $params);
        foreach($list as $key => $value) {
            $this->set($key, $value);
        }
    }
}