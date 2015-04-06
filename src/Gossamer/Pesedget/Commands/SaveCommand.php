<?php
namespace Gossamer\Pesedget\Commands;

use Gossamer\Pesedget\Commands\AbstractCommand;
use Gossamer\Pesedget\Entities\OneToManyChildInterface;
use Gossamer\Pesedget\Database\QueryBuilder;
use Gossamer\Pesedget\Entities\AbstractI18nEntity;
use Gossamer\Pesedget\Entities\MultiRowInterface;
use Gossamer\Pesedget\Entities\OneToManyJoinInterface;

class SaveCommand extends AbstractCommand {
   

    /**
     * saves an entity into the database
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($requestParams = array()){

        $this->getQueryBuilder()->setValues($requestParams);
        
        $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::SAVE_QUERY, QueryBuilder::PARENT_ONLY);

        $this->beginTransaction();
        try{
            //first save into the parent table 
            $result = $this->query("$query", FALSE);
           
            if(intval($result) > 0) {
                 
                $requestParams['id'] = $result;               
                $this->saveI18nValues($result,  $requestParams, $requestParams['locale']);
                $this->saveChildTableParams($result, $requestParams);
                $this->saveOneToManyJoins($result, $requestParams);
            }elseif(array_key_exists('id', $requestParams) && intval($requestParams['id']) > 0){      
          
                if(array_key_exists('locale', $requestParams)) {
                    $this->saveI18nValues($requestParams['id'], $requestParams, $requestParams['locale']); 
                }
       
                $this->saveChildTableParams($requestParams['id'],  $requestParams);
                $this->saveOneToManyJoins($requestParams['id'],  $requestParams);
                
            }   
            $this->saveMultiRowValues($requestParams);
      
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
    
    private function getEntityName() {
        $function = new \ReflectionClass($this->entity);

        return strtolower($function->getShortName());
    }

    private function saveMultiRowValues(&$requestParams) {
        if(!$this->entity instanceof MultiRowInterface) {
            return;
        }
        
        $cleanedBeforeInsert = false;
        
        $objectKey = $this->entity->getMultiRowArrayKey();
        $idKey = key($this->entity->getMultiRowIdentifier());
        $idValues = current($this->entity->getMultiRowIdentifier());
        $columnId = $idValues[0];
        $formId = $idValues[1];
        $items = $this->parseJson($requestParams[$objectKey]);        
        
        foreach($items as $item) {
            //get the id field from submitted params and add it to the item
            $temp = $this->parseJson($requestParams[$idKey]);
            if(!$cleanedBeforeInsert) {
                 
                $filter = array($columnId => $temp[$formId]);
                $this->getQueryBuilder()->where($filter);
                $this->query($this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::DELETE_QUERY, QueryBuilder::CHILD_ONLY));
                 
                $cleanedBeforeInsert = true;
            }
            $item[$columnId] = $temp[$formId];
           
            $this->getQueryBuilder()->setValues($item);
            $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::SAVE_QUERY, QueryBuilder::CHILD_ONLY);
                        
            $this->query($query);
            
        }
    }
    
    private function saveChildTableParams($firstResult, &$requestParams) {
        if(!$this->entity instanceof OneToManyChildInterface) {
            return;
        }
        $parsedComponent = array();
        $tables = $this->entity->getChildRelationships();
        
        foreach($tables as $tablename => $table) {
             //first remove any records for this item
            $row = array($this->entity->getIdentityColumn() => $firstResult);
            
           
             
            $this->getQueryBuilder()->where($row);
            $this->query($this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::DELETE_QUERY, QueryBuilder::CHILD_ONLY));
            
            foreach($table as $keyColumn => $foreignColumn) {                 
                list($component, $column) = $foreignColumn;    
                
                if(!array_key_exists($component, $requestParams)) {
                    // file_put_contents('/var/www/db-repo/logs/save-test.log', "$component not found\r\n", FILE_APPEND);
                    continue;
                }
                
                $parsedComponent = $requestParams[$component]; //$this->parseJson($requestParams[$component]); 
                
                if(!array_key_exists($column, $parsedComponent)) {
                    continue;
                }
                
                foreach($parsedComponent as $id=>$key) {
                   
                    $row = array(
                        $this->entity->getIdentityColumn() => $firstResult,
                        $keyColumn => $key
                    );
                    
                    //iterate and save each row
                    $this->getQueryBuilder()->setValues($row);
                    $this->query($this->getQueryBuilder()->getQuery(new $tablename(), QueryBuilder::SAVE_QUERY, QueryBuilder::CHILD_ONLY));
                }                
            }
            
            if(count($parsedComponent) > 0) {
                $requestParams[$component] = $parsedComponent;
            }
           
        }
    }

    private function saveOneToManyJoins($firstResult, &$requestParams) {
        
        if(!$this->entity instanceof OneToManyJoinInterface) {
            return;
        }
        
        $tables = $this->entity->getManyJoinRelationships();
               
        foreach($tables as $entity => $columns) {
           
             //first remove any records for this item
            $row = array($this->entity->getIdentityColumn() => $firstResult);
            $this->getQueryBuilder()->where($row);
            $this->query($this->getQueryBuilder()->getQuery(new $entity(), QueryBuilder::DELETE_QUERY, QueryBuilder::CHILD_ONLY));

            foreach($columns as $column => $passedParam) {
                 if(!array_key_exists($passedParam, $requestParams)) {
                
                    continue;
                }
                $passvaluesList = $requestParams[$passedParam];
                $values = explode(',', $passvaluesList);
                $insertParams = '';
                
                foreach($values as $value) {                
                    $insertParams .= ", (null, '" . $firstResult . "','" . preg_replace("/[^0-9]/","",$value) . "')";
                }
                $object = new $entity();
               
                $this->query('insert into ' . $object->getTableName() . ' values ' . substr($insertParams, 1));
            }

        }
    }
    
    
    /**
     * save child rows into I18n specific to saved entity
     */
    private function saveI18nValues($firstResult, $requestParams, &$locale) {
       // file_put_contents('/var/www/db-repo/logs/cms.log', print_r($requestParams, true), FILE_APPEND);
        if(!$this->entity instanceof AbstractI18nEntity || is_null($locale)) {
            
            return;
        }
       // file_put_contents('/var/www/db-repo/logs/cms.log', print_r($requestParams, true), FILE_APPEND);
        
        $parsedLocales = $locale;//$this->parseJson($locale); 
       
        foreach($parsedLocales as $parsedLocale => $row) {
  
            $params = array($this->entity->getI18nIdentifier() => $firstResult, 'locale' => $parsedLocale);

            //need to add this, since it is what associates us to the parent table
            $row[$this->entity->getI18nIdentifier()] = $firstResult;
            $row['locale'] = $parsedLocale;
            $this->getQueryBuilder()->setValues($row);
            //file_put_contents('/var/www/db-repo/logs/test.log', "i18nquery:row ".print_r($row, true) ."\r\n", FILE_APPEND);
        
            $this->getQueryBuilder()->where($params);
            $query = $this->getQueryBuilder()->getQuery($this->entity, QueryBuilder::SAVE_QUERY, QueryBuilder::CHILD_ONLY);

            $this->query($query);
           
        }

    }
}