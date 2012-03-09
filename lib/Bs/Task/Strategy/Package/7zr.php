<?php

class Bs_Task_Strategy_Package_7zr implements Bs_Task_Strategy_Package
{

    protected $_name = '';
    protected $_project = '';

    public function loadConfig(DOMDocument $config)
    {
        
    }

    public function package($name, $destination)
    {
        $currentDir = getcwd();
        chdir($destination);
        $console = new Bs_Console();
        
        $cmd = '7zr -t7z -m0=lzma -mx=9 -mfb=64 -md=32m -ms=on a '.$name.' ./*';
        
        exec($cmd);
        
        chdir($currentDir);
    }

    public function setProject(Bs_Project $project)
    {
        $this->_project = $project;
    }
    
}
