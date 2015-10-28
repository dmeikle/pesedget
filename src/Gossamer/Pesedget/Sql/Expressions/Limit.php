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
 * Limit
 *
 * @author Dave Meikle
 */
class Limit extends SqlDecorator {
    
    public function __construct($offset = null, $limit = null) {
        parent::set("$offset, $limit");
    }

    public function __toString() {
        if(strlen(str_replace(' ','',$this->sqlStatement)) == 1) {
            return '';
        }
        return ' LIMIT ' . $this->sqlStatement;
    }

}
