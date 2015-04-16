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

use Gossamer\Pesedget\Commands\SearchCommand;
use tests\Gossamer\Pesedget\Entities\Staff;
use Gossamer\Pesedget\Database\EntityManager;


/**
 * GetCommandTest
 *
 * @author Dave Meikle
 */
class SearchCommandTest extends \tests\BaseTest{
    
    /**
     * 
     */
    public function testExecute() {
        $cmd = new SearchCommand(new Staff(), null, EntityManager::getInstance()->getConnection());
        $result = $cmd->execute(array('firstname' => 'd'));
   
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\Staffs', $result));
        $this->assertTrue(count($result['tests\\Gossamer\\Pesedget\\Entities\\Staffs']) > 0);
    }
}
