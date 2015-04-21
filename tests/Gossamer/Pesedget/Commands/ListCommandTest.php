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

use Gossamer\Pesedget\Commands\ListCommand;
use tests\Gossamer\Pesedget\Entities\Staff;
use Gossamer\Pesedget\Database\EntityManager;


/**
 * GetCommandTest
 *
 * @author Dave Meikle
 */
class ListCommandTest extends \tests\BaseTest{
    
    /**
     * @group execute
     */
    public function testExecute() {
        $cmd = new ListCommand(new Staff(), null, EntityManager::getInstance()->getConnection());
        $result = $cmd->execute(array());
      
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\Staffs', $result));
        $this->assertTrue(count($result['tests\\Gossamer\\Pesedget\\Entities\\Staffs']) > 0);
    }
    
    /**
     * @group test
     */
    public function testAmbiguousColumns() {
        $ticket = new \tests\Gossamer\Pesedget\Entities\Ticket();
        $cmd = new ListCommand($ticket, null, EntityManager::getInstance()->getConnection());
        
        $params = array(
            'directive::OFFSET' => '0',
            'directive::LIMIT' => '20',
            'locale' => 'en_US',
            'isActive' => '1'
        );
       
        $result = $cmd->execute($params);
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\Tickets', $result));

    }
    
    /**
     * @group locale
     */
    public function testLocaleColumns() {
        $ticket = new \tests\Gossamer\Pesedget\Entities\TicketType();
        $cmd = new ListCommand($ticket, null, EntityManager::getInstance()->getConnection());
        
        $params = array(
            'directive::OFFSET' => '0',
            'directive::LIMIT' => '20',
            'locale' => 'en_US',
            'isActive' => '1'
        );
       
        $result = $cmd->execute($params);
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('tests\\Gossamer\\Pesedget\\Entities\\TicketTypes', $result));
        $this->assertTrue(count($result['tests\\Gossamer\\Pesedget\\Entities\\TicketTypes']) > 0);
    }
}
