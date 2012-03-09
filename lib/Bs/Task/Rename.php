<?php

class Bs_Task_Rename extends Bs_Task
{
    protected $_origin = '';
    protected $_dest = '';
    
    public function run()
    {
        rename($this->_origin, $this->_dest);
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $origin = $taskConf->item(0)->attributes->getNamedItem('origin')->nodeValue;
        $dest = $taskConf->item(0)->attributes->getNamedItem('dest')->nodeValue;
        
        $this->_origin = $this->_project->replaceProperties($origin);
        $this->_dest = $this->_project->replaceProperties($dest);
    }
}