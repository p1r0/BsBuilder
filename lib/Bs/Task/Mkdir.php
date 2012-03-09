<?php

class Bs_Task_Mkdir extends Bs_Task
{
    protected $_dirname = '';
    
    public function run()
    {
        $console = new Bs_Console();
        $console->printLn('Creating '.$this->_dirname.' folder...');
        @mkdir($this->_dirname, 0777, true);
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $dirname = $taskConf->item(0)->attributes->getNamedItem('dirname')->nodeValue;
        
        $this->_dirname = $this->_project->replaceProperties($dirname);
    }
}