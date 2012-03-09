<?php

class Bs_Task_Package extends Bs_Task
{
    protected $_destination = '';
    protected $_strategy = 'tar_bz2';
    
    public function run()
    {
        $console = new Bs_Console();
        $console->printLn('Packaging using the '.$this->_strategy.' strategy...');
        
        $inflector = new Bs_Task_Strategy_Inflector();
        $strategyClass = $inflector->toCammelCase($this->_strategy, 'Package_');
        /** @var Bs_Task_Strategy_Package */
        $strategy = new $strategyClass();
        $strategy->setProject($this->_project);
        $strategy->loadConfig($this->_domDocument);
        $strategy->package($this->_name, $this->_destination);
        
        $console->printLn('Done!');
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        
        $dest = $taskConf->item(0)->attributes->getNamedItem('dest');
        if(!$dest)
        {
            throw new Bs_Excepction('No dest provided for package task');
        }

        $dest = $dest->nodeValue;
        
        $name = $taskConf->item(0)->attributes->getNamedItem('name');
        if(!$name)
        {
            throw new Bs_Excepction('No dest provided for package task');
        }

        $name = $name->nodeValue;
        
        $strategy = $taskConf->item(0)->attributes->getNamedItem('strategy');
        if($strategy)
        {
            $this->_strategy = $strategy->nodeValue;
        }
        
        $this->_name = $this->_project->replaceProperties($name);
        $this->_destination = $this->_project->replaceProperties($dest);
        
    }
}