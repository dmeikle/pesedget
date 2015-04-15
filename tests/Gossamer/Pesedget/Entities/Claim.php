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
 * Staff
 *
 * @author Dave Meikle
 */
class Claim extends AbstractEntity implements SQLInterface, OneToOneJoinInterface {
    

    
    public function getFields($namespacedClass) {
        $list = array('tests\\Gossamer\\Pesedget\\Entities\\Staff' => array('firstname', 'lastname'));
        
        if(array_key_exists($namespacedClass, $list)) {
            return $list[$namespacedClass];
        }
        
        return null;
    }

    public function getJoinRelationships() {
        return array(
            'tests\\Gossamer\\Pesedget\\Entities\\Staff' => array('Claims.Staff_id' => 'Staff.id')
        );
    }
    
}
