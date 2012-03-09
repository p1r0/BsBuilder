<?php

class Bs_Task_Copy extends Bs_Task
{
    protected $_source = '';
    protected $_destination = '';
    protected $_strategy = 'php';
    
    public function run()
    {
        $console = new Bs_Console();
        $console->printLn('Copying '.$this->_source.' to '.$this->_destination.
                           ' using the '.$this->_strategy.' strategy...');
        
        $inflector = new Bs_Task_Strategy_Inflector();
        $strategyClass = $inflector->toCammelCase($this->_strategy, 'Copy_');
        /** @var Bs_Task_Strategy_Copy */
        $strategy = new $strategyClass();
        $strategy->setProject($this->_project);
        $strategy->loadConfig($this->_domDocument);
        $strategy->copy($this->_source, $this->_destination);
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $source = $taskConf->item(0)->attributes->getNamedItem('source');
        if(!$source)
        {
            throw new Bs_Excepction('No source provided for copy task');
        }
        
        $source = $this->_project->replaceProperties($source->nodeValue);
        
        $dest = $taskConf->item(0)->attributes->getNamedItem('dest');
        if(!$dest)
        {
            throw new Bs_Excepction('No dest provided for copy task');
        }

        $dest = $this->_project->replaceProperties($dest->nodeValue);
        
        $strategy = $taskConf->item(0)->attributes->getNamedItem('strategy');
        if($strategy)
        {
            $this->_strategy = $strategy->nodeValue;
        }
        
        $this->_source = $this->_project->replaceProperties($source);
        $this->_destination = $dest;
        
    }
}