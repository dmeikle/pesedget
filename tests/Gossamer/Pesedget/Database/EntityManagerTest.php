<?php


namespace tests\Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\EntityManager;

/**
 * Description of EntityManagerTest
 *
 * @author davem
 */
class EntityManagerTest extends \tests\BaseTest{
    
    public function testInstanceNoSitePathDefined() {
        try{
            $manager = EntityManager::getInstance();
        }  catch (\Exception $e) {
            //$this->assertEquals($e->getMessage(), '__SITE_PATH must be defined in bootstrap');
        }
    }
    
    public function testInstance() {
        
       $manager = EntityManager::getInstance();       
       $this->assertTrue($manager instanceof EntityManager);
    }
    
    public function testGetDefaultConnection() {
        
       $manager = EntityManager::getInstance();       
       $this->assertTrue($manager instanceof EntityManager);
       
       $conn = $manager->getConnection();
       
       $this->assertTrue($conn instanceof \Gossamer\Pesedget\Database\DBConnection);
    }
    
  
}
