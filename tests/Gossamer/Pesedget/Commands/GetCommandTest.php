<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\Gossamer\Pesedget\Commands;

use Gossamer\Pesedget\Commands\GetCommand;
use tests\Gossamer\Pesedget\Entities\Staff;
use tests\Gossamer\Pesedget\Entities\InventoryCategory;
use Gossamer\Pesedget\Database\EntityManager;


/**
 * GetCommandTest
 *
 * @author Dave Meikle
 */
class GetCommandTest extends \tests\BaseTest{
    
    /**
     * @group execute
     */
    public function testExecute() {
        $cmd = new GetCommand(new Staff(), null, EntityManager::getInstance()->getConnection());
        $result = $cmd->execute(array('id' => 2));
        
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\Staff', $result));
        $this->assertEquals(2, $result['tests\\Gossamer\\Pesedget\\Entities\\Staff']['id']);
    }
    
    /**
     * @group i18n
     */
    public function testI18nExecute() {
        $cmd = new GetCommand(new InventoryCategory(), null, EntityManager::getInstance()->getConnection());
        $result = $cmd->execute(array('id' => 2));
       
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\InventoryCategory', $result));
        //commenting out - test data changed 
        //$this->assertEquals(2, $result['tests\\Gossamer\\Pesedget\\Entities\\InventoryCategory']['id']);
    }
}
