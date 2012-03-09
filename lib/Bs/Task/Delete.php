<?php

class Bs_Task_Delete extends Bs_Task
{
    protected $_dirname = '';
    
    public function run()
    {
        $console = new Bs_Console();
        $console->printLn('Deleting '.$this->_dirname.' folder...');
        $cmd = 'rm -rf '.$this->_dirname;
        exec($cmd);
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $dirname = $this->_project->replaceProperties($taskConf->item(0)->attributes->getNamedItem('dirname')->nodeValue);
        
        $this->_dirname = $dirname;
    }
}