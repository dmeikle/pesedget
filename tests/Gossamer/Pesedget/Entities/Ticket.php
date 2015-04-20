<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\Gossamer\Pesedget\Entities;

use Gossamer\Pesedget\Entities\AbstractEntity;
use Gossamer\Pesedget\Database\SQLInterface;
use Gossamer\Pesedget\Entities\OneToOneJoinInterface;

/**
 * Ticket
 *
 * @author Dave Meikle
 */
class Ticket extends AbstractEntity implements SQLInterface, OneToOneJoinInterface {
    
    private $joinRelationships = array(
        'tests\Gossamer\Pesedget\Entities\Staff' => array('Tickets.Staff_id', 'Staff.id')
    );
    
    private $oneToOneJoinFields = array(
        'tests\Gossamer\Pesedget\Entities\Staff' => array('firstname', 'lastname')
    );
    
    public function getJoinRelationships() {
       return $this->joinRelationships; 
    }

    public function getFields($namespacedClass) {
        if(array_key_exists($namespacedClass, $this->oneToOneJoinFields)) {
            return $this->oneToOneJoinFields[$namespacedClass];
        }
        
        return null;
    }
}