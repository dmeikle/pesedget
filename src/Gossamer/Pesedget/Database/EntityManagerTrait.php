<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Database;

/**
 * EntityManagerTrait
 *
 * @author Dave Meikle
 */
trait EntityManagerTrait {
    
    protected $entityManager;
    
    function getEntityManager() {
        return $this->entityManager;
    }

    function setEntityManager($entityManager) {
        $this->entityManager = $entityManager;
    }


}
