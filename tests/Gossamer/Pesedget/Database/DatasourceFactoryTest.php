<?php

namespace tests\Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\DatasourceFactory;

/**
 * Description of DBConnectionTest
 *
 * @author davem
 */
class DatasourceFactoryTest extends \tests\BaseTest{
    
    public function testTheConnection() {
        $factory = new DatasourceFactory();
        
        $ds = $factory->getDatasource('mysql', $this->getLogger());
        
        //print_r($ds);
       
    }
    
   
}
