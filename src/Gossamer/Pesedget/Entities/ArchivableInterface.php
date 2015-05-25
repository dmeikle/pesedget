<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Entities;

/**
 * ArchivableInterface - used by QueryBuilder to see if an entity is
 * archive capable. If it is, QueryBuilder will work with command object
 * to check automatically for the correct 'shard' of a table to query.
 * 
 * @example 
 * function getArchiveSuffix() {
 *      return "_" .
 *
 * @author Dave Meikle
 */
class ArchivableInterface {
    
    /**
     * returns the suffix to append to an archived table
     */
    public function getArchiveSuffix();
    
}
