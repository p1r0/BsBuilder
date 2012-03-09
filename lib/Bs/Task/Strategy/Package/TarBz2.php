<?php

class Bs_Task_Strategy_Package_TarBz2 implements Bs_Task_Strategy_Package
{

    protected $_name = '';
    protected $_project = null;

    public function loadConfig(DOMDocument $config)
    {
        
    }

    public function package($name, $destination)
    {
        $currentDir = getcwd();
        chdir($destination);
        $console = new Bs_Console();
        
        $cmd = 'tar -jcf '.$name.' ./*';
        
        exec($cmd);
        
        chdir($currentDir);
    }

    public function setProject(Bs_Project $project)
    {
        $this->_project = $project;
    }
    
}
