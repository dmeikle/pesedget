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

    public function getQuery(ArangoDocument $document, $queryType = 'getall', $i18nQueryType = null, $queryingI18n = false, $resetParams = true)
    {

    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

//    /https://www.arangodb.com/2012/06/querying-a-nosql-database-the-elegant-way/
    private function buildSelect(ArangoDocument $document)
    {
        $alias = preg_split('/(?=[A-Z])/', $document->getTableName());
        $query = 'FOR ' . $alias . ' in ' . $document->getTableName();
        $query .= $this->getFilter($alias);
        $query .= $this->getOffsetLimit();

        $query .= 'RETURN o';
    }

    private function getFilter($alias)
    {
        $query = '';
        foreach ($this->getFilterParams() as $key => $value) {
            $query .= ' FILTER ' . $alias . '.' . $key . ' == "' . $value . '"';
        }

        return $query;
    }

    private function getFilterParams()
    {
        $params = $this->params;
        foreach ($params as $key => $value) {
            if (strpos($key, 'directive::') !== false) {
                unset($params);
            }
        }

        return $params;
    }

    private function getOffsetLimit()
    {
        $offset = '';
        $limit = '';
        $query = '';
        if (array_key_exists('directive::LIMIT', $this->params)) {
            $limit = $this->params['directive::LIMIT'];
        }

        if (array_key_exists('directive::OFFSET', $this->params)) {
            $offset = $this->params['directive::OFFSET'];
        }

        if (strlen($offset) > 0) {
            $query = ' LIMIT ' . $offset . ', ' . $limit;
        } elseif (strlen($limit) > 0) {
            $query = ' LIMIT ' . $limit;
        }

        return $query;
    }
}