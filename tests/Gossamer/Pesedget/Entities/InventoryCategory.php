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

use Gossamer\Pesedget\Entities\AbstractI18nEntity;
use Gossamer\Pesedget\Database\SQLInterface;

/**
 * Staff
 *
 * @author Dave Meikle
 */
class InventoryCategory extends AbstractI18nEntity implements SQLInterface {
    
    public function __construct() {
        parent::__construct();
        $this->tablename = 'InventoryCategories';
    }
}
