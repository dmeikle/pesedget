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
 * SqlBuilder
 *
 * @author Dave Meikle
 */
class SqlBuilder {
    
     
    
    private $statement = array();
    
    private $params = array();
    
    public function add($sqlPartial, SqlStatement $statement) {
        $this->statement[$sqlPartial] = $statement;
        
        return $this;
    }
    
    public function toSql() {
        $sql = implode(' ', $this->statement);
        
        return $sql;
    }
    
    
}
