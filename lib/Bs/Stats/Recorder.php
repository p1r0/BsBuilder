<?php

class Bs_Stats_Recorder
{
    private static $_instance = null;
    
    protected $_messages =  array();
    
    protected function __construct()
    {
        ;
    }
    
    /**
     *
     * @return Bs_Stats_Recorder
     */
    public static function instance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function _pushMessage($message)
    {
        $this->_messages[] = $message;
    }
    
    public static function pushMessage($message)
    {
        self::instance()->_pushMessage($message);
    }
    
    public function getMessages()
    {
        $ar = $this->_messages;
        $this->_messages = array();
        
        return $ar;
    }
    
    public static function printStats()
    {
        $console = new Bs_Console();
        
        $console->printLn('');
        $console->printLn('');
        $console->printLn('==================================================', 'dark_gray');
        foreach(self::instance()->getMessages() as $msg)
        {
            $console->printLn($msg, 'dark_gray');
        }
    }
    
}

?>
