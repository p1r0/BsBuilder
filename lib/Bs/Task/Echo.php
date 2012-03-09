<?php

class Bs_Task_Echo extends Bs_Task
{   
    protected $_text = '';
    protected $_color = null;
    
    public function run()
    {
        $console = new Bs_Console();
        
        $console->printLn($this->_text, $this->_color);
    }
    
    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $text = $taskConf->item(0)->attributes->getNamedItem('text');
        if($text)
        {
             $this->_text = $this->_project->replaceProperties($text->nodeValue);
        }
        
        $color = $taskConf->item(0)->attributes->getNamedItem('color');
        if($color)
        {
             $this->_color = $this->_project->replaceProperties($color->nodeValue);
        }
    }
}