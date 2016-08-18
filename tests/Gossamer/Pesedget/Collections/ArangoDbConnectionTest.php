<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/17/2016
 * Time: 1:38 PM
 */

namespace tests\Gossamer\Pesedget\Collections;

use Gossamer\Pesedget\Collections\ArangoDBConnection;
use Gossamer\Pesedget\Database\EntityManager;
use triagens\ArangoDb\Collection;
use triagens\ArangoDb\Document;
use triagens\ArangoDb\DocumentHandler;
use triagens\ArangoDb\UpdatePolicy;
use triagens\ArangoDb\ConnectionHandler;

class ArangoDbConnectionTest extends \tests\BaseTest
{

    public function testGetConnection()
    {
        $conn = new ArangoDBConnection($this->getCredentials());

        $this->assertTrue($conn instanceof ArangoDBConnection);
    }

    public function testCreateCollection()
    {
        $conn = new ArangoDBConnection($this->getC());

        $collectionHandler = $conn->getCollectionHandler();
        $userCollection = new Collection();
        $userCollection->setName('users');
        try {
            $collectionHandler->drop($userCollection);
        } catch (\Exception $e) {
        }


        if ($collectionHandler->has($userCollection)) {
            // drops an existing collection with the same name to make
            // unit test repeatable
            $collectionHandler->drop($userCollection);
        }

        $id = $collectionHandler->add($userCollection);
        // print the collection id created by the server
        $this->assertTrue($id > 0);

        $collectionHandler->drop($userCollection);
    }

    public function testCreateDocument()
    {
        $conn = new ArangoDBConnection($this->getCredentials());

        $collectionHandler = $conn->getCollectionHandler();
        $userCollection = new Collection();
        $userCollection->setName('users');

        //need to recreate the collection for each unit test
        if ($collectionHandler->has($userCollection)) {
            // drops an existing collection with the same name to make
            // unit test repeatable
            $collectionHandler->drop($userCollection);
        }
        $id = $collectionHandler->add($userCollection);

        $document = new Document();
        $document->set("a", "Foo");
        $document->set("b", "bar");
        $documentHandler = new DocumentHandler($conn->getConnection());
        // save document in collection
        $documentId = $documentHandler->save($userCollection, $document);
        $this->assertTrue($id > 0);
    }
}