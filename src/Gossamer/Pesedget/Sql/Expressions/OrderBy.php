<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Sql\Expressions;

use Gossamer\Pesedget\Sql\SqlDecorator;

/**
 * OrderBy
 *
 * @author Dave Meikle
 */
class OrderBy extends SqlDecorator {
    
    public function __construct($column, $direction) {
        parent::set(" $column $direction");
    }
    
    public function __toString() {
        if(strlen(str_replace(' ', '', $this->sqlStatement)) == 0) {
            return '';
        }
        
        return 'ORDER BY ' . $this->sqlStatement;
    }

}
