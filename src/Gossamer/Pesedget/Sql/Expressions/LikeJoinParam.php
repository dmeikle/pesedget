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
 * LikeJoinParam
 *
 * @author Dave Meikle
 */
class LikeJoinParam extends SqlDecorator {

    public function __construct(array $values) {
        
        parent::set($values);
    }

    public function __toString() {
        $retval = '';

        foreach ($this->sqlStatement as $key => $value) {
            if (!is_array($value)) {
                $retval .= ' OR (' . $key . ' like \'%' . $value . '%\')';
            } else {
                foreach ($value as $item) {
                    $retval .= ' OR (' . $key . ' like \'%' . $item . '%\')';
                }
            }
        }

        return ' ON (' . substr($retval, 3) . ')';
    }

}
