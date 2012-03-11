<?php

class Bs_Task_Strategy_Replace_SimpleReplace implements Bs_Task_Strategy_Replace
{

    protected $_project = null;
    
    public function loadConfig(DOMDocument $config)
    {
        
    }

    public function replace($value, $newvalue, $file, $newfile = null)
    {
        if(!file_exists($file))
        {
            $console = new Bs_Console();
            $console->printLn('WARNING! File "'.$file.'" not found for replace. Nothing done.',
                              'red');
            return;
        }
        
        if($newfile == null)
        {
            $newfile = $file;
        }
        
        file_put_contents($newfile, str_replace($value, $newvalue, file_get_contents($file)));
    }

    public function setProject(Bs_Project $project)
    {
        $this->_project = $project;
    }

    
}
