<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 8/18/2016
 * Time: 2:04 PM
 */

namespace Gossamer\Pesedget\Collections;


use Gossamer\Pesedget\Documents\ArangoDocument;
use Gossamer\Pesedget\Exceptions\NoMatchingFieldsException;
use Gossamer\Pesedget\Exceptions\TableNotFoundException;
use Gossamer\Pesedget\Utils\YAMLParser;
use triagens\ArangoDb\Document;
use triagens\ArangoDb\Statement;
use triagens\ArangoDb\CollectionHandler;
use triagens\ArangoDb\DocumentHandler;
use triagens\ArangoDb\ServerException;


class ArangoDocumentBuilder
{

    private $conn = null;


    public function __construct(ArangoDBConnection $conn)
    {
        $this->conn = $conn;
    }

    public function getDefaultParams() {
        return array("query" => '',
            "count" => true,
            "batchSize" => 1000,
            "_sanitize" => false,);
    }

    public function listAll(ArangoDocument $document, array $params) {

        $document->populate($params, $this->loadFields($document));

        $collectionHandler = new CollectionHandler($this->conn->getConnection());
        $cursor = null;

        try{
            $cursor = $collectionHandler->byExample($document->getTableName(), $document);
        }catch(ServerException $e) {
            throw new NoMatchingFieldsException("No fields specified in config for " . $document->getClassName());
        }

        return $cursor->getAll();
    }

    public function save(ArangoDocument $document) {
        // create a new collection
        $collectionName = $document->getTableName();

        $collection = new Collection($collectionName);
        $collectionHandler = new CollectionHandler($this->conn->getConnection());

        if (!$collectionHandler->has($collectionName)) {
            // drops an existing collection with the same name to make
            // tutorial repeatable
            $collectionHandler->create($collectionName);
        }
        $documentHandler = new DocumentHandler($this->conn->getConnection());
        $documentId = $documentHandler->save($collectionName, $document);

        return $documentId;
    }

    public function get(ArangoDocument $document, array $params) {
        // create a new collection
        $collectionName = $document->getTableName();

        $collection = new Collection($collectionName);
        $collectionHandler = new CollectionHandler($this->conn->getConnection());

        if (!$collectionHandler->has($collectionName)) {
            // drops an existing collection with the same name to make
            // tutorial repeatable
            $collectionHandler->create($collectionName);
        }

        $documentHandler = new DocumentHandler($this->conn->getConnection());
        $documentId = $documentHandler->save($collectionName, $document);

        return $documentId;
    }

    private function loadFields(ArangoDocument $document) {
        $loader = new YAMLParser();
        //check to see if it's a core component, then add 'core' to the path if yes
//        $loader->setFilePath(__SITE_PATH. DIRECTORY_SEPARATOR . $document->getNamespace() . DIRECTORY_SEPARATOR . ((strpos( $document->getNamespace(), 'framework') !== false) ? 'core' . DIRECTORY_SEPARATOR : '') .
//            __COMPONENT_FOLDER. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'fields.yml');
        $loader->setFilePath( ((strpos( $document->getNamespace(), 'framework') !== false) ? 'core' . DIRECTORY_SEPARATOR : '') .
            __COMPONENT_FOLDER. DIRECTORY_SEPARATOR .  'config' . DIRECTORY_SEPARATOR . 'fields.yml');

        $config = $loader->loadConfig();

        unset($loader);

        $tableName = $document->getTableName();
        if(is_null($config) || !array_key_exists($tableName, $config)) {
            throw new TableNotFoundException($tableName . ' not found in fields config');
        }

        return $config[$tableName];
    }

}