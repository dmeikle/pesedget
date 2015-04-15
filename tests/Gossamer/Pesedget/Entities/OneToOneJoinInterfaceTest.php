<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\Gossamer\Pesedget\Entities;

use tests\Gossamer\Pesedget\Entities\OneToOneStaff;
use Gossamer\Pesedget\Database\QueryBuilder;

/**
 * OneToOneJoinInterfaceTest
 *
 * @author Dave Meikle
 */
class OneToOneJoinInterfaceTest extends \tests\BaseTest {
    
//    public function testQuery() {
//        $staff = new OneToOneStaff();
//        $builder = new QueryBuilder();
//        
//        $query = $builder->getQuery($staff, QueryBuilder::GET_ITEM_QUERY);
//        
//        echo $query;
//    }
    
    public function testOneToOneQuery() {
        $claim = new Claim();
        $builder = new QueryBuilder();
        $firstResult[] = array(
            'Staff_id' => '12'
        );
        $tables = $claim->getJoinRelationships();

        foreach ($tables as $objectName => $columns) {
            $object = new $objectName();
            $key = $object->getTableName() . '_id';

            if (array_key_exists($key, $firstResult[0])) {

                $filter = array('id' => $firstResult[0][$key]);
                $builder->where($filter);
                $fields = $claim->getFields($objectName);
             
                if(!is_null($fields) && is_array($fields)) {
                    $builder->setFields($fields);
                }
                $query = $builder->getQuery($object, QueryBuilder::GET_ITEM_QUERY, QueryBuilder::CHILD_ONLY, null, null, FALSE);

                echo $query."\r\n";

            }
        }
    }
}
