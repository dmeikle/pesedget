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
    
    public function testConnection() {
        
        $conn = EntityManager::getInstance()->getConnection();
   
        $result = $conn->query("select * from Staff limit 10");
       
    }
}
