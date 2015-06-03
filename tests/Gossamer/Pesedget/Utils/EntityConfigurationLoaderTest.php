<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
namespace tests\Gossamer\Pesedget\Utils;

use Gossamer\Pesedget\Utils\EntityConfigurationLoader;

/**
 * EntityConfigurationLoaderTest
 *
 * @author Dave Meikle
 */
class EntityConfigurationLoaderTest extends \tests\BaseTest {
    
    public function testLoadConfiguration() {
        $loader = new EntityConfigurationLoader();
        $config = ($loader->loadConfiguration('components\staff\entities\Staff'));
        
        $this->assertEquals(count($config), 2);
    }
    
    public function testLoadCoreConfiguration() {
        $loader = new EntityConfigurationLoader();
        $config = ($loader->loadConfiguration('core\components\locales\entities\Locale'));
        
        $this->assertEquals(count($config), 1);
    }
    
    public function testLoadMissingConfiguration() {
       
        $loader = new EntityConfigurationLoader();
        $config = ($loader->loadConfiguration('core\components\cms\entities\Locale'));
        
        $this->assertFalse($config);
    }
}
