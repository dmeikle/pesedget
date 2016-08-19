<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/18/2016
 * Time: 2:44 PM
 */

namespace tests\Gossamer\Pesedget\Collections;


use Gossamer\Pesedget\Collections\ArangoDocumentBuilder;
use Gossamer\Pesedget\Documents\ArangoDocument;
use tests\src\components\users\documents\User;
use Gossamer\Pesedget\Collections\ArangoDBConnection;

class ArangoDocumentBuilderTest extends\tests\BaseTest
{

    public function testListAll() {
        define('__NAMESPACE', 'src/components');
        define('__COMPONENT_FOLDER', 'users');


        $conn = new ArangoDBConnection($this->getCredentials());
        
        $builder = new ArangoDocumentBuilder($conn);
        $document = new User();

        $params = array(
            'gender'=>'m'
        );
        $result = $builder->listAll($document, $params);

        $this->assertTrue(count($result) > 0);
    }
}