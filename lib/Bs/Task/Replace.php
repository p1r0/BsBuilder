<?php

class Bs_Task_Replace extends Bs_Task
{

    protected $_value = '';
    protected $_newvalue = '';
    protected $_filename = '';
    protected $_strategy = 'simple_replace';

    public function run()
    {
        $inflector = new Bs_Task_Strategy_Inflector();
        $strategyClass = $inflector->toCammelCase($this->_strategy, 'Replace_');
        /** @var Bs_Task_Strategy_Copy */
        $strategy = new $strategyClass();
        $strategy->setProject($this->_project);
        $strategy->loadConfig($this->_domDocument);
        $strategy->replace($this->_value, $this->_newvalue, $this->_filename);
    }

    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument);
        $taskConf = $xpath->query("/" . $this->_taskName);
        if ($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for ' . $this->_taskName);
        }

        $value = $taskConf->item(0)->attributes->getNamedItem('value');
        if (!$value)
        {
            throw new Bs_Excepction('Replace tasks must have a value');
        }

        $value = $this->_project->replaceProperties($value->nodeValue);

        $newvalue = $taskConf->item(0)->attributes->getNamedItem('new_value');
        if (!$newvalue)
        {
            throw new Bs_Excepction('Replace tasks must have a new_value');
        }

        $newvalue = $this->_project->replaceProperties($newvalue->nodeValue);
        
        $file = $taskConf->item(0)->attributes->getNamedItem('file');
        if (!$file)
        {
            throw new Bs_Excepction('Replace tasks must have a file');
        }

        $file = $this->_project->replaceProperties($file->nodeValue);
        
        $strategy = $taskConf->item(0)->attributes->getNamedItem('strategy');
        if($strategy)
        {
            $this->_strategy = $strategy->nodeValue;
        }
        
        $this->_value = $value;
        $this->_newvalue = $newvalue;
        $this->_filename = $file;
    }
}