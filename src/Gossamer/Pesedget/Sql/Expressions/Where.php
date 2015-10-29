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
 * Where
 *
 * @author Dave Meikle
 */
class Where extends SqlDecorator {

    public function __construct(array $filters, $concatenator = 'AND') {
        parent::set($filters);
    }

    public function __toString() {
        return ' WHERE (' . implode(') ' . $concatenator . ' (', $this->sqlStatement) . ')';
    }

}
