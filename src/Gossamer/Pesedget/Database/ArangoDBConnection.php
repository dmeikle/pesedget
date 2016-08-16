<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/16/2016
 * Time: 4:23 PM
 */

namespace Gossamer\Pesedget\Database;


use Gossamer\Pesedget\Entities\AbstractEntity;
use Monolog\Logger;
use triagens\ArangoDb\Connection;

class ArangoDBConnection  implements ConnectionInterface
{

    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function getRowCount()
    {
        // TODO: Implement getRowCount() method.
    }

    public function setLogger(Logger $logger)
    {
        // TODO: Implement setLogger() method.
    }

    public function getAllRowsAsArray()
    {
        // TODO: Implement getAllRowsAsArray() method.
    }

    public function setCustomer(SQLInterface $customer)
    {
        // TODO: Implement setCustomer() method.
    }

    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    public function commitTransaction()
    {
        // TODO: Implement commitTransaction() method.
    }

    public function rollbackTransaction()
    {
        // TODO: Implement rollbackTransaction() method.
    }

    public function getConnection()
    {
        // TODO: Implement getConnection() method.
    }

    public function preparedQuery($query, array $params, $fetch = true)
    {
        // TODO: Implement preparedQuery() method.
    }

    public function query($query, $fetch = true)
    {
        // TODO: Implement query() method.
    }

    public function getTableColumnMappings(AbstractEntity $entity)
    {
        // TODO: Implement getTableColumnMappings() method.
    }

    public function getLastQuery()
    {
        // TODO: Implement getLastQuery() method.
    }

    public function getCredentials()
    {
        // TODO: Implement getCredentials() method.
    }
}