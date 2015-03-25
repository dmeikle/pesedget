<?php
namespace Gossamer\Pesedget\Commands;

//use database\DBConnection;
use Gossamer\Pesedget\Database\SQLInterface;
use Gossamer\Pesedget\Database\QueryBuilder;
use Gossamer\Horus\Core\Request;
use Gossamer\Pesedget\Database\DBConnection;


/**
 * Abstract Command Class extending from Database Connection
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
abstract class AbstractCommand
{
    /**
     * SQLInterface Object mapped to a database table
     * */
    protected $entity = null;

 
    /**
     * QueryBuilder for generating all queries, standard and I18n
     */
    private $queryBuilder = null;

     
    protected $dbConnection = null;
    
    /**
     * Constructor
     *
     * @param SQLInterfce   $entity
     * @param Registry      $registry
     */
    public function __construct(SQLInterface $entity, $credentials = null, DBConnection $connection = null){

        if(!is_null($credentials) && is_null($connection)) {  
            //this is a last ditch effort - we prefer to use the same connection
            //throughout rather than creating a new one each time
           $this->dbConnection = new DBConnection($credentials);
        } elseif(!is_null($connection)) {
            $this->dbConnection = $connection;
        } 
               
        $this->entity = $entity;
	$this->queryBuilder = new QueryBuilder(array());
    }

    
    public function getEntity() {
        return $this->entity;
    }
    

    /**
     * @return string query
     */
    protected function getQueryBuilder(){
            return $this->queryBuilder;
    }

    /**
     * executes code specific to the child class
     *
     * @param array     URI params
     * @param array     POST params
     */
    public abstract function execute($params = array());

    public function getRequest() {
        return $this->Request;
    }

    

    protected function parseJson($string) {
        
        if(is_array($string)) {
            return $string;
        }
              
        $string = str_replace('\"','"', $string);
        $string = str_replace("\'", "`", $string);
       
        $tempVal = (array) json_decode($string);
        $retval = array();      
      
        foreach($tempVal as $key => $row) {
        
            if(is_object($row)) {
                $retval[$key] = (array) $row;
            }else {
                $retval[$key] = $row;
            }
        }

        return $retval;

    }
    
    
    

    protected function beginTransaction(){
        $this->dbConnection->beginTransaction();
    }

    protected function commitTransaction(){
       $this->dbConnection->commitTransaction();
    }

    protected function rollbackTransaction(){
        $this->dbConnection->rollbackTransaction();
    }

    public function getConnection(){
        return $this->dbConnection->getConnection();
    }

    public function query($query, $fetch = true){
        if(!is_object($this->dbConnection)) {
            error_log('** NO DB CONNECTION FOR ' . $query);
            return array();
        }
        return $this->dbConnection->query($query, $fetch);
    }
}
