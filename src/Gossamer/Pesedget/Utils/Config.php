<?php

namespace Gossamer\Pesedget\Utils;

/**
 * Description of Config
 *
 * @author davem
 */
class Config
{
    private $configuration;
    
    public function __construct($configuration = array()){
        $this->configuration = $configuration;
        
    }
    
    public function get($item){
        
        if(array_key_exists($item, $this->configuration)){
            return $this->configuration[$item];
        }
        
        return null;
    }
    
    public function set($item, $params){
        $this->configuration[$item] = $params;
    }
    
    public function toArray(){
        
        return array_values(array_keys($this->configuration));
    }
    
    public function toDetailsArray() {
        return $this->configuration;
    }
}

