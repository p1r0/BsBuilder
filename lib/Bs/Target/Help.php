<?php

class Bs_Target_Help extends Bs_Target
{
    protected $_targetsInfo = array();
    
    public function __construct(DOMDocument $targetDom, Bs_Project $project)
    {
        $this->_domDocument = $targetDom;
        $this->_project = $project;
        $this->loadTargetsInfo();
    }
    
    public function dump()
    {
        echo "";
    }
    
    public function run()
    {
        $console = new Bs_Console();
        
        $console->printLn('HelP');
        $console->printLn('==========================================');
        $console->printLn('You can run any of the following targets: ');
        $console->printLn('');
        
        foreach($this->_targetsInfo as $info)
        {
            $console->printLn($info);
        }
        
        $console->printLn('');
    }
    
    protected function loadTasks()
    {
        
    }
    
    protected function loadTargetsInfo()
    {
        $xpath = new DOMXPath($this->_domDocument);
        $targets = $xpath->query("/project/target");

        if ($targets->length == 0)
        {
            return;
        }

        foreach ($targets as $target)
        {
            $name = $target->attributes->getNamedItem('name')->nodeValue;
            $desc = $target->attributes->getNamedItem('description');
            
            if($desc)
            {
                $desc = $name."\t\t".$this->_project->replaceProperties($desc->nodeValue);
            }
            else
            {
                $desc = $name;
            }
            
            $this->_targetsInfo[] = $desc;
        }
    }
}