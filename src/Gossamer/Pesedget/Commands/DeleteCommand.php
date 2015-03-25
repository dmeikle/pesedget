<?php
namespace Gossamer\Pesedget\Commands;

use Gossamer\Pesedget\Commands\AbstractCommand;
use Gossamer\Pesedget\Database\QueryBuilder;
use Gossamer\Pesedget\Entities\AbstractI18nEntity;
use Gossamer\Pesedget\Entities\OneToOneJoinInterface;


/**
 * Delete Command Class
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
class DeleteCommand extends AbstractCommand
{

    /**
     * Deletes an entity row from the database
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($params = array(), $requestParams = array()){
      
        $deleteParams = $params;
        if(count($requestParams) > 0) {
            $deleteParams = $requestParams;
        }
        $this->beginTransaction();
        $firstResult = null;
        try{
            //first delete the child tables
            $this->deleteI18nLocales($deleteParams); 
            $this->deleteOneToOneRows($deleteParams);

            //now delete the main tables
            $this->getQueryBuilder()->where($deleteParams);
            $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::DELETE_QUERY);

            $firstResult = $this->query($query);

            $this->commitTransaction();
        }catch(Exception $e){
            $this->logger->addError($e->getMessage());
            $this->rollbackTransaction();
        }
        return $firstResult;

    }

    /**
     * deletes a row from the I18n table
     *
     * @param array     URI params
     */
    private function deleteI18nLocales($params) {
        if(!$this->entity instanceof AbstractI18nEntity) {               
            return; 
        }
        $filter = array($this->entity->getI18nIdentifier() => $params['id']);

        $this->getQueryBuilder()->where($filter);

        $this->query($this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::DELETE_QUERY, QueryBuilder::CHILD_ONLY));
    }
    
    private function deleteOneToOneRows($params) {
        if(!$this->entity instanceof OneToOneJoinInterface) {               
            return; 
        }
        //first get the id of the row we are deleting
        $this->getQueryBuilder()->where($params);
      
        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::GET_ITEM_QUERY);
        
        $result = $this->query($query); 
       
        $list = $this->entity->getJoinRelationships();
        foreach($list as $joinName => $columns) {          
            $object = new $joinName();
            $filter = array('id' => $result[0][$object->getTableName() . '_id']);
            $this->getQueryBuilder()->where($filter);
            $query = $this->getQueryBuilder()->getQuery($object, QueryBuilder::DELETE_QUERY, QueryBuilder::CHILD_ONLY);
            $this->query($query);            
        }
    }
}
