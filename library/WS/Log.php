<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author magentodeveloper
 */
class WS_Log {
    
    protected $logger;
    
    protected static $filelogger = null;
    
    public static function getInstance()
    {
        if(self::$filelogger === null)
        {
            self::$filelogger = new self();
        }
        return self::$filelogger;
    }
    
    protected function __construct() 
    {
        $this->logger = Zend_Registry::get('activity_logger');
    }
    
    public function getLog()
    {
        return $this->logger;
    }
    
    public static function info($message)
    {
        self::getInstance()->getLog()->info('User:'.$message);
    }
    
    
}
