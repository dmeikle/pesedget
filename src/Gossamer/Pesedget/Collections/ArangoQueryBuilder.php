<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/18/2016
 * Time: 4:10 PM
 */

namespace Gossamer\Pesedget\Collections;


use Gossamer\Pesedget\Documents\ArangoDocument;

class ArangoQueryBuilder
{

    const SAVE_QUERY = 'save';
    const DELETE_QUERY = 'delete';
    const GET_ITEM_QUERY = 'get';
    const GET_ALL_ITEMS_QUERY = 'getall';
    const GET_COUNT_QUERY = 'getcount';
    const PARENT_ONLY = 'parentOnly';
    const CHILD_ONLY = 'childOnly';
    const PARENT_AND_CHILD = 'parentAndChild';

    private $params = array();

    public function getQuery(ArangoDocument $document, $queryType = 'getall', $i18nQueryType = null, $queryingI18n = false, $resetParams = true) {

    }

    public function setParams(array $params) {
        $this->params = $params;
    }
    
    private function buildSelect(ArangoDocument $document) {

    }
}