<?php

namespace tests\Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\DBConnection;
use Gossamer\Pesedget\Database\EntityManager;

/**
 * Description of DBConnectionTest
 *
 * @author davem
 */
class DBConnectionTest extends \tests\BaseTest{
    
    public function testTheConnection() {
        
        $conn = EntityManager::getInstance()->getConnection();
   
        $result = $conn->query("select * from Staff limit 10");
       
    }
    
    public function testConnectionConstructorNoCredentials() {
        $conn = new DBConnection();
        
        $result = $conn->query("select * from Staff limit 10");
        
        print_r($result);
    }
    
   
    /**
     * @group pdo
     */
    public function testPreparedStatement() {
        $conn = new DBConnection();
        
        $result = $conn->preparedQuery('select * from Staff where id = ?', array('i', '2'));
        echo "num rows: ".$conn->getRowCount()."\r\n";
        echo "this is result:\r\n";
        print_r($result);
    }
}
