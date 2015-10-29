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

    private $concatenator;

    public function __construct(array $filters, $concatenator = 'AND') {
        parent::set($filters);
        $this->concatenator = $concatenator;
    }

    public function __toString() {
        $retval = '';

        foreach ($this->sqlStatement as $row) {
            ;
            if (is_array($row)) {
                $key = key($row);
                $retval .= ' ' . $key . ' (' . $row[$key] . ')';
            } else {
                $retval .= ' ' . $this->concatenator . ' (' . $row . ')';
            }
        }

        //return ' WHERE (' . implode(') ' . $this->concatenator . ' (', $this->sqlStatement) . ')';
        return ' WHERE ' . (strlen($retval) > 0 ? substr($retval, 4) : '');
    }

}
