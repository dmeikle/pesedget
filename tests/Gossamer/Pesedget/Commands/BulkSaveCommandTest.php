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

use Gossamer\Pesedget\Commands\BulkSaveCommand;
use tests\Gossamer\Pesedget\Entities\Staff;
use Gossamer\Pesedget\Database\EntityManager;


/**
 * GetCommandTest
 *
 * @author Dave Meikle
 */
class BulkSaveCommandTest extends \tests\BaseTest{
    
    /**
     * 
     */
    public function testExecute() {
        $cmd = new BulkSaveCommand(new Staff(), null, EntityManager::getInstance()->getConnection());
        $params = array('id' => 'null', 'StaffTypes_id' => '1', 'firstname' => 'test', 'lastname'=>'phpunit');
        
        $result = $cmd->execute($params);

        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\Staff', $result));
    }
}
