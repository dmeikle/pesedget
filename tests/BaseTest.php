<?php

namespace tests;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use triagens\ArangoDb\UpdatePolicy;
use triagens\ArangoDb\ConnectionHandler;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    const GET = 'GET';
    
    const POST = 'POST';
    
    protected function getLogger() {
        
            $logger = new Logger('phpUnitTest');
            $logger->pushHandler(new StreamHandler("logs/phpunit.log", Logger::DEBUG));  
       
        
        return $logger;
    }
    
    public function setRequestMethod($method) {
        define("__REQUEST_METHOD", $method);
    }
    
    public function setURI($uri) {
        define('__URI', $uri);
        define("__REQUEST_URI", $uri . '/');
    }
    
    public function testBase() {
        
    }


    protected function getCredentials()
    {
        return array(
            // database name
            'database' => 'PHPUnit',
            // server endpoint to connect to
            'endpoint' => 'tcp://127.0.0.1:8529',
            // authorization type to use (currently supported: 'Basic')
            'AuthType' => 'Basic',
            // user for basic authorization
            'AuthUser' => 'root',
            // password for basic authorization
            'AuthPasswd' => 'isnothere',
            // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
            'Connection' => 'Keep-Alive',
            // connect timeout in seconds
            'timeout' => 3,
            // whether or not to reconnect when a keep-alive connection has timed out on server
            'Reconnect' => true,
            // optionally create new collections when inserting documents
            'createCollection' => true,
            // optionally create new collections when inserting documents
            'policy' => UpdatePolicy::LAST,
        );
    }
}
