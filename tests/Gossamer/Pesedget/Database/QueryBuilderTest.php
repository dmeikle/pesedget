<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\QueryBuilder;
use Gossamer\Pesedget\Database\DBConnection;
use Gossamer\Pesedget\Database\EntityManager;
use tests\entities\TaxRate;

/**
 * QueryBuilderTest
 *
 * @author Dave Meikle
 */
class QueryBuilderTest  extends \tests\BaseTest{
    
    public function testSaveValue() {
        $builder = new QueryBuilder(array('dbConnection' => EntityManager::getInstance()->getConnection()));
        
        $builder->setBulkInsert($this->getValues());
        echo $builder->getQuery(new TaxRate(), QueryBuilder::SAVE_QUERY);
    }
    
    public function testSaveArray() {
        $builder = new QueryBuilder(array('dbConnection' => EntityManager::getInstance()->getConnection()));
        
        $builder->setBulkInsert($this->getArrayValues());
        echo $builder->getQuery(new TaxRate(), QueryBuilder::SAVE_QUERY);
    }
    
    private function getValues() {
        return array( 'id' => '1', 'States_id' => 1, 'taxRate' => .05);
    }
    
    private function getArrayValues() {
        return array (
            array (
                'States_id' => 1,
                'taxRate' => .05
            ),
            array(
                'States_id' => 3,
                'taxRate' => .05
            ),
            array(
                'States_id' => 4,
                'taxRate' => .05
            )
        );
    }
}
