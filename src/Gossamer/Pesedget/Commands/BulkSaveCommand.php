<?php
namespace Gossamer\Pesedget\Commands;

use Gossamer\Pesedget\Commands\AbstractCommand;
use Gossamer\Pesedget\Database\QueryBuilder;


class BulkSaveCommand extends AbstractCommand {
   

    /**
     * saves an entity into the database
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($requestParams = array()){

        $this->getQueryBuilder()->setBulkInsert($requestParams);
       
        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::SAVE_QUERY, QueryBuilder::PARENT_ONLY);

        $this->beginTransaction();
        try{
            $result = $this->query("$query", FALSE);
      
            $this->commitTransaction();
        }catch(Exception $e){
          
            error_log($e->getMessage());
            $this->rollbackTransaction();
        }
        if(array_key_exists('locale', $requestParams)) {
            $requestParams['locale'] = $this->parseJson($requestParams['locale']);
        }
      
        return array(get_class($this->entity) => $requestParams);
    }
    

}