<?php


namespace Gossamer\Pesedget\Utils;

use Gossamer\Pesedget\Utils\ManagerInterface;
use Gossamer\Pesedget\Utils\Config;

/**
 * ConfigManager Class
 *
 * Author: Dave Meikle
 * Copyright: Quantum Unit Solutions 2013
 */
class ConfigManager implements ManagerInterface
{

    /**
     * path to locate/save configurations
     */
    private $workingPath;

    /**
     * access rights for IO
     */
    const FILE_PUT_CONTENTS_ATOMIC_MODE = '0777';

    /**
     * constructor
     *
     * @param array injectables
     */
    public function __construct($injectables = array())
    {

    }

    /**
     * parseFilepath - parses the path to access files, dropping the current filename
     *
     * @param string    filepath
     *
     * @return string   path to folder for saving/access
     */
    private function parseFilepath($filepath)
    {

        $retval = explode('/', $filepath);
        array_pop($retval);
 
        return implode('/', $retval);
    }

    /**
     * filePutContentsAtomic - writes file while avoiding write collisions
     *
     * @param string    filename
     * @param string    content
     */
    private function filePutContentsAtomic($filename, $content)
    {
        // if (!file_exists($this->workingPath . $filename)) {
        	// try{
        		// mkdir($this->workingPath, 0777, true);
        	// }catch(\Exception $e){
        		// error_log('unable to write to '.$this->workingPath);
				// error_log($e->getMessage());
        	// }
//             
        // }
        $temp = tempnam($this->workingPath, 'temp');


        if (!($f = @fopen($temp, 'wb'))) {
            $temp = $this->workingPath . DIRECTORY_SEPARATOR . uniqid('temp');
            if (!($f = @fopen($temp, 'wb'))) {
                trigger_error("filePutContentsAtomic() : error writing temporary file '$temp'", E_USER_WARNING);
                return false;
            }
        }

        fwrite($f, $content);
        fclose($f);

        if (!@rename($temp, $filename)) {
            @unlink($filename);
            @rename($temp, $filename);
        }

        @chmod($filename, self::FILE_PUT_CONTENTS_ATOMIC_MODE);

        return true;

    }

    /**
     * getConfiguration - loads the configuration
     *
     * @param string    filename
     * @param Config    loaded config
     */
    public function getConfiguration($filename)
    {
        if (file_exists($filename)) {
            $config = new Config(json_decode(file_get_contents($filename)));

            return $config;
        }

        return null;
    }

    /**
     * saveConfiguration - serializes a configuration
     *
     * @param string    filename
     * @param Config    config
     */
    public function saveConfiguration($filename, Config $config)
    {
        $this->workingPath = $this->parseFilepath($filename);

        if (!$config instanceof Config) {
            throw new \RuntimeException('Not an instance of Config');
        }

        $this->filePutContentsAtomic($filename, json_encode($config->toArray()));
    }

}

