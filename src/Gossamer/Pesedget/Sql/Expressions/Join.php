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
 * Join
 *
 * @author Dave Meikle
 */
class Join extends SqlDecorator {
      
    public function __construct($tableName, $alias = null) {
        parent::set("$tableName $alias");
    }

    public function __toString() {
        return 'JOIN ' . $this->sqlStatement;
    }


}
