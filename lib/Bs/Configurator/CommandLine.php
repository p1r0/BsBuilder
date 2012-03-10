<?php

class Bs_Configurator_CommandLine
{
    protected $_opts = array();
    protected $_argv =  array();
    
    public function __construct()
    {

    }

    public function setOptions($argc, $argv, $options)
    {
        $pruneargv = array();
        foreach ($options as $option => $value)
        {
            foreach ($argv as $key => $chunk)
            {
                $regex = '/^' . (isset($option[1]) ? '--' : '-') . $option . '/';
                if ($chunk == $value && $argv[$key - 1][0] == '-' || preg_match($regex, $chunk))
                {
                    array_push($pruneargv, $key);
                }
            }
        }

        while ($key = array_pop($pruneargv))
            unset($argv[$key]);
        
        $this->_argv = $argv;
        $this->_opts = $options;
    }
    
    public function getGetOptConfig()
    {
        $parameters = array(
            'c:' => 'config-file:',
        );

        return array(implode('', array_keys($parameters)), $parameters);
    }
    
    public function getTarget()
    {
        if(count($this->_argv) > 1)
        {
            return $this->_argv[1];
        }
        else
        {
            return null;
        }
    }

}