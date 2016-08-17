<?php

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Entities\AbstractEntity;
use Gossamer\Pesedget\Database\ColumnMappings;
use Gossamer\Pesedget\Database\EntityManager;
use Monolog\Logger;

class DBConnection implements ConnectionInterface, GossamerDBConnection{

    protected $host;
    protected $user;
    protected $pass;
    protected $db;
    private $lastQuery = '';
    protected $logger = null;
    protected $stack;
    private $rows;
    protected $conn = null;
    private $rowCount = 0;

    public function __construct(array $credentials = null) {
        if (!is_null($credentials)) {
            $this->initCredentials($credentials);
        } else {
            //uh-oh... no db credentials exist.
            $this->initCredentials(EntityManager::getInstance()->getCredentials());
        }
    }

    public function __destruct() {
        $this->logger = null;
        $this->conn = null;
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

    public function getAllRowsAsArray() {

        if (isset($this->stack)) {
            return $this->stack;
        }

        $this->stack = array();

        while ($ra = mysqli_fetch_array($this->rows)) {
            array_push($this->stack, $ra);
        }

        unset($this->rows);

        return $this->stack;
    }

    public function setCustomer(SQLInterface $customer) {
        if (!($customer instanceof SQLInterface)) {
            throw new InterfaceNotImplementedException();
        }

        $this->user = $customer->dbUsername;
        $this->pass = $customer->dbPassword;
        $this->db = $customer->dbName;
        $this->host = $customer->dbHost;
    }

    public function beginTransaction() {
        $this->getConnection();
        mysqli_query($this->conn, "BEGIN");
    }

    public function commitTransaction() {
        $this->getConnection();
        mysqli_query($this->conn, "COMMIT");
    }

    public function rollbackTransaction() {
        $this->getConnection();
        mysqli_query($this->conn, "ROLLBACK");
    }

    public function getConnection() {
        if (is_null($this->conn) || !$this->conn->ping()) {
            $this->conn = @mysqli_connect($this->host, $this->user, $this->pass, $this->db);
            if (is_bool($this->conn) || !mysqli_ping($this->conn)) {
                throw new \Exception('unable to connect to db with provided credentials');
            }
        }

        mysqli_query($this->conn, 'SET NAMES "utf8"');

        return $this->conn;
    }

    public function preparedQuery($query, array $params, $fetch = true) {

        $this->lastQuery = $query;

        //mysql_select_db($this->db);
        if (!is_null($this->logger)) {
            $this->logger->addDebug(utf8_decode($query));
        }

        $stmt = $this->getConnection()->prepare($query);
        //with bind() the first element must be a list of datatypes that correspond
        //to each of the remaining elements of the array.
        //eg: (ssi, 'dave', 'meikle', '10')
//        i - integer
//        d - double
//        s - string
//        b - BLOB
        //stmt does not accept an array so we'll bypass with CUFA method
        $bindNames[] = array_shift($params);
        for ($i = 0; $i < count($params); $i++) {
            $bindName = 'bind' . $i;
            $$bindName = $params[$i];
            $bindNames[] = &$$bindName;
        }

        call_user_func_array(array($stmt, 'bind_param'), $bindNames);
        $results = $stmt->execute();

        //since we are using PDO we need to handle this differently than mysqli
        if (strtolower(substr($query, 0, 6)) == 'delete') {
            return 0;
        } elseif (strtolower(substr($query, 0, 6)) == 'insert') {
            return $stmt->insert_id;
        } elseif (strtolower(substr($query, 0, 6) == 'update')) {
            return;
        } else {

            $stmt->store_result();
            $this->rowCount = $stmt->num_rows;
        }

        $retval = $this->fetchArray($stmt);
        unset($stmt);

        return $retval;
    }

    protected function fetchArray($stmt) {
        $meta = $stmt->result_metadata();
        while ($field = $meta->fetch_field()) {
            $params[] = &$row[$field->name];
        }

        call_user_func_array(array($stmt, 'bind_result'), $params);
        $result = array();
        while ($stmt->fetch()) {
            foreach ($row as $key => $val) {
                $c[$key] = $val;
            }
            $result[] = $c;
        }

        $stmt->close();

        return $result;
    }

    public function query($query, $fetch = true) {

        $this->lastQuery = $query;

        //mysql_select_db($this->db);
        if (!is_null($this->logger)) {
            $this->logger->addDebug(utf8_decode($query));
        }


        $results = mysqli_query($this->getConnection(), utf8_decode($query));


        if (strtolower(substr($query, 0, 6)) == 'delete') {
            return 0;
        } elseif (strtolower(substr($query, 0, 6)) == 'insert') {
            return mysqli_insert_id($this->conn);
        } elseif (strtolower(substr($query, 0, 6) == 'update')) {
            return;
        } else {
            // $this->rowCount = mysqli_query($this->getConnection(), 'SELECT FOUND_ROWS()');
        }

        //mysql_close($conn);
        if ($fetch && $results) {
            $stack = array();
            while ($ra = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                array_push($stack, $ra);
            }

            unset($results);

            return $stack;
        } elseif ($fetch && !$results) {
            return;
        }

        $insertId = mysqli_insert_id($this->getConnection());

        return $insertId;
    }

    public function getTableColumnMappings(AbstractEntity $entity) {
        if (!$entity instanceof AbstractEntity) {
            throw new \RuntimeException('DBConnection::getTableColumnMappings - entity my be instance of AbstractEntity');
        }
        // $columns = $this->query('SHOW COLUMNS FROM ' . $tableName);

        $mappings = new ColumnMappings($this);
        $columns = $mappings->getTableColumnList($entity->getTableName());
        return $columns;
    }

    public function getLastQuery() {
        return $this->lastQuery;
    }

    public function getCredentials() {
        return array('username' => $this->user,
            'password' => $this->pass,
            'dbName' => $this->db,
            'host' => $this->host);
    }

}
