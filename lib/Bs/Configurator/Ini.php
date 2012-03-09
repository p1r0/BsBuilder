<?php

class Bs_Configurator_Ini
{
    private static $_instance = null;
    private $_options = array();
    
    public static function getInstance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function loadConfig($file = null)
    {
        if($file == null)
        {
            $file = './config/config.ini';
        }
        
        $this->_options = parse_ini_file($file);
        //var_dump($this->_options);
        //exit();
        //
        //
        //php settings
        
        foreach($this->_options as $option => $value)
        {
            if(substr($option, 0, 4) == 'php.')
            {
                $key = substr($option, 4, strlen($option) -4);
                //echo $key.' = '.$value;
                ini_set($key, $value);
            }
        }           
        
        //var_dump($this->_options);
    }
    
    public function getVal($option)
    {
        if(!isset($this->_options[$option]))
        {
            throw new Exception('Value for '.$option.' not found');
        }
        
        return $this->_options[$option];
    }
    
    public function hasVal($option)
    {
        return isset($this->_options[$option]);
    }
}
?>
