<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Commands;

use Gossamer\Pesedget\Commands\ListCommand;
use Gossamer\Pesedget\Database\QueryBuilder;

/**
 * SearchCommand
 *
 * @author Dave Meikle
 */
class SearchCommand extends ListCommand {
    
    public function execute($params = array()) {
        
        $this->getQueryBuilder()->where($params);
        $this->getQueryBuilder()->setIsLikeSearch(true);
        
        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ALL_ITEMS_QUERY, QueryBuilder::PARENT_AND_CHILD);
   
        $result = $this->query($query);
        $param = get_class($this->entity) . 's';
             
        return array($param => $result, $param . 'Count' => $this->getTotalRowCount());
    }

}
