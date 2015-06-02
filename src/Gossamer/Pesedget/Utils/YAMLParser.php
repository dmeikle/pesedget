<?php

namespace Gossamer\Pesedget\Utils;

use Symfony\Component\Yaml\Yaml;
use Monolog\Logger;

class YAMLParser
{
    protected $ymlFilePath = null;
    
    protected $logger = null;
    
    public function __construct(Logger $logger = null) {
        $this->logger = $logger;
    }
    
    public function findNodeByURI( $uri, $searchFor) {
        $this->log('YAMLParser opening ' . $this->ymlFilePath);
       
        $config = $this->loadConfig();
       
        if(!is_array($config)) {
            return null;
        }
       
        if(array_key_exists($uri, $config) && array_key_exists($searchFor, $config[$this->getSectionKey($uri)])) {
         
            return $config[$this->getSectionKey($uri)][$searchFor];
                        
        }
        return null;
    }
    
    public function loadConfig() {
        return Yaml::parse(file_get_contents($this->ymlFilePath));
    }
    private function getSectionKey($uri) {
        
        $pieces = explode('/',strtolower($uri));
        $pieces = array_filter($pieces);

        return implode('_', $pieces);
    }
    
    public function setFilePath($ymlFilePath) {
        $this->ymlFilePath = $ymlFilePath;
    }
    
    private function log($message) {
        if(!is_null($this->logger)) {
            $this->logger->addDebug($message);
        }
    }
}
