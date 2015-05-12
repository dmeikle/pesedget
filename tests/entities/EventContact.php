<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\entities;

use Gossamer\Pesedget\Entities\OneToOneJoinInterface;
use Gossamer\Pesedget\Entities\AbstractEntity;
use Gossamer\Pesedget\Database\SQLInterface;

/**
 * Event
 *
 * @author Dave Meikle
 */
class EventContact  extends AbstractEntity implements SQLInterface, OneToOneJoinInterface {
    
 
    public function getFields($namespacedClass) {
        
    }

    public function getJoinRelationships() {
        return $this->joinRelationships;
    }

    private $joinRelationships = array(
        'tests\entities\Event' => array('EventContacts.Events_id', 'Events.id')
    );
    
    private $joinFields = array(
        'components\claims\entities\Claim' => array('jobNumber'),
         'components\claims\entities\ClaimsLocation' => array('unitNumber')
    );

    
}
