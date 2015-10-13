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
 * Select
 *
 * @author Dave Meikle
 */
class Select extends SqlDecorator {
    
    public function __construct(array $columns) {
        parent::set($columns);
    }


    public function __toString() {
        return 'SELECT ' . implode(',', $this->sqlStatement);
    }
}
