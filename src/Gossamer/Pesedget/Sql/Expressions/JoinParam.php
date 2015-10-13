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
 * JoinParam
 *
 * @author Dave Meikle
 */
class JoinParam extends SqlDecorator {
    
    public function __construct(array $values) {
        parent::set($values);
    }
    
    public function __toString() {
        $retval = '';
        foreach($this->sqlStatement as $key => $value) {
            $retval .= ' AND (' . $key . ' = ' . $value . ')';
        }
        
        
        
        return ' ON ' . substr($retval, 4) ;
    }
    
}
