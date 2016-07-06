<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/5/2016
 * Time: 8:50 PM
 */

namespace tests\Gossamer\Pesedget\Database;

use Gossamer\Pesedget\Database\MSDBConnection;
use Gossamer\Pesedget\Database\EntityManager;
use Gossamer\Pesedget\Utils\YAMLParser;


class MSDBConnectionTest  extends \tests\BaseTest
{

    private $config;

    public function testTheConnection() {

        $conn = EntityManager::getInstance()->getConnection('mssql');

        $result = $conn->query("select * from MembershipMaster where Member_ID = 'K3380'");
        echo "here is result\r\n";
print_r($result);
    }

    public function testConnectionConstructorNoCredentials() {
        $this->loadDatabaseCredentials();
        $conn = new MSDBConnection($this->config['mssql']['credentials']);

        $result = $conn->query("select top 1 * from MembershipMaster");

    }


    /**
     * @group pdo
     */
//    public function testPreparedStatement() {
//        $conn = new DBConnection();
//
//        $result = $conn->preparedQuery('select * from Staff where id = ?', array('i', '2'));
//    }

    private function loadDatabaseCredentials() {
        $parser = new YAMLParser();
        $parser->setFilePath(__SITE_PATH . '/app/config/credentials.yml');

        $config = $parser->loadConfig();

        $this->config = $config['database'];
    }
}