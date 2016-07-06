<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/5/2016
 * Time: 7:21 PM
 */

namespace Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Entities\AbstractEntity;
use Monolog\Logger;

interface ConnectionInterface
{

    public function __construct(array $credentials = null);

    public function __destruct();

    public function getRowCount();

    public function setLogger(Logger $logger);

    public function getAllRowsAsArray();

    public function setCustomer(SQLInterface $customer);

    public function beginTransaction();

    public function commitTransaction();

    public function rollbackTransaction();

    public function getConnection();

    public function preparedQuery($query, array $params, $fetch = true);

    public function query($query, $fetch = true);

    public function getTableColumnMappings(AbstractEntity $entity);

    public function getLastQuery();

    public function getCredentials();

}