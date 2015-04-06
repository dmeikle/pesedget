<?php


namespace Gossamer\Pesedget\Database;


use Gossamer\Pesedget\Entities\AbstractEntity;
use Gossamer\Pesedget\Database\ColumnMappings;
use Gossamer\Pesedget\Database\EntityManager;
use Monolog\Logger;


class DBConnection
{
    
    protected $host;

    protected $user;

    protected $pass;

    protected $db;

    private $lastQuery='';
    
    protected $logger = null;

    protected $stack;

    private $rows;

    protected $conn = null;
    
    private $rowCount = 0;
    
    public function __construct($credentials = null) {        
        if(!is_null($credentials)) {
            $this->initCredentials($credentials);
        } else {
            //uh-oh... no db credentials exist.
            $this->initCredentials(EntityManager::getInstance()->getCredentials());
        }
    }
    
    private function initCredentials(array $credentials) {
        
        $this->user = $credentials['username'];
        $this->pass = $credentials['password'];
        $this->db = $credentials['dbName'];
        $this->host = $credentials['host'];
    }

    public function getRowCount() {
        return $this->rowCount;
    }
    
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }
    
    function getAllRowsAsArray(){
        
        if(isset($this->stack)){
            return $this->stack;
        }
        
        $this->stack = array();
        
        while ($ra=mysqli_fetch_array($this->rows)) {
             array_push($this->stack,$ra);
        }

        unset($this->rows);

        return $this->stack;
    }

    public function setCustomer(SQLInterface $customer) {
        if(!($customer instanceof SQLInterface)) {
            throw new InterfaceNotImplementedException();
        }

        $this->user = $customer->dbUsername;
        $this->pass = $customer->dbPassword;
        $this->db = $customer->dbName;
        $this->host = $customer->dbHost;

    }

    public function beginTransaction(){
        $this->getConnection();
        mysqli_query($this->conn, "BEGIN" );
    }

    public function commitTransaction(){
       $this->getConnection();
        mysqli_query($this->conn, "COMMIT");
    }

    public function rollbackTransaction(){
        $this->getConnection();
        mysqli_query($this->conn, "BEGIN");
    }

    public function getConnection(){
        if(is_null($this->conn)) {
            $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
            
            if (!$this->conn) {
                die('Could not connect: ' . mysql_error());
            }
        }
        mysqli_query($this->conn, 'SET NAMES "utf8"');
        
        return $this->conn;
    }

    public function query($query, $fetch = true){

        $this->lastQuery = $query;
      
        //mysql_select_db($this->db);
        if(!is_null($this->logger)) {
            $this->logger->addDebug(utf8_decode($query));
        }
        
        
        $results = mysqli_query($this->getConnection(), utf8_decode($query));
        
        if (!$results) {
          //  die('Invalid query: ' . mysqli_error());
        }
        if(strtolower(substr($query,0,6)) == 'delete' ) {
            return 0;
        }elseif(strtolower(substr($query,0,6)) == 'insert') {           
            return mysqli_insert_id($this->conn);
        }elseif(strtolower(substr($query,0,6) =='update')) {
            return;
        } else {
            $this->rowCount = mysqli_query($this->getConnection(), 'SELECT FOUND_ROWS()');
        }
        
        //mysql_close($conn);
        if($fetch && $results){           
            $stack=array();        
            while ($ra=mysqli_fetch_array($results, MYSQL_ASSOC)) {                
                 array_push($stack,$ra);
            }

            unset($results);

            return $stack;
        } elseif($fetch && !$results) {
            return;
        }
        
        $insertId = mysqli_insert_id($this->getConnection());

        return $insertId;
    }

    public function getTableColumnMappings(AbstractEntity $entity){
        if(!$entity instanceof AbstractEntity){
            throw new \RuntimeException('DBConnection::getTableColumnMappings - entity my be instance of AbstractEntity');
        }
       // $columns = $this->query('SHOW COLUMNS FROM ' . $tableName);

        $mappings = new ColumnMappings($this);
        $columns = $mappings->getTableColumnList($entity->getTableName());
        return $columns;
    }

    public function getLastQuery(){
        return $this->lastQuery;
    }
}
