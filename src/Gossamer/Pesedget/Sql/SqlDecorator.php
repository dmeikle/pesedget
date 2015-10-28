<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Sql;

/**
 * SqlDecorator
 *
 * @author Dave Meikle
 */
abstract class SqlDecorator extends SqlStatement {
    
    protected $sqlStatement;
    
    protected function set($statement) {
        $this->sqlStatement = $statement;
    }
   
    

}
