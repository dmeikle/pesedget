<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/18/2016
 * Time: 12:32 PM
 */

namespace Gossamer\Pesedget\Documents;


use triagens\ArangoDb\Document;

class ArangoDocument extends Document
{

    protected $tablename;

    /**
     * can be used as an internal configuration, but can be overwritten in each class if needed
     *
     * @var array
     */
    protected $fields = array('id');



    public function __construct(){
        $this->tablename = $this->stripNamespacing(get_class($this)) . 's';
    }

    public function getNamespace()
    {
        $reflector = new \ReflectionClass($this); // class Foo of namespace A

        return $reflector->getNamespaceName();
    }

    private function stripNamespacing($namespacedEntity) {
        $chunks = explode('\\', $namespacedEntity);

        return array_pop($chunks);
    }

    public function getTableName(){
        return $this->tablename;
    }


    public function getClassName() {
        $reflect = new \ReflectionClass($this);

        return $reflect->getShortName();
    }

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

        foreach($params as $key => $value) {
            if(!in_array($key, $this->fields)) {
                continue;
            }

            $this->set($key, $value);
        }
    }
}