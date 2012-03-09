<?php

class Bs_Task_Property extends Bs_Task
{

    protected $_name = '';
    protected $_value = '';

    public function run()
    {
        $this->_project->setProperty($this->_name, $this->_value);
    }

    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument);
        $taskConf = $xpath->query("/" . $this->_taskName);
        if ($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for ' . $this->_taskName);
        }

        $name = $taskConf->item(0)->attributes->getNamedItem('name');
        if (!$name)
        {
            throw new Bs_Excepction('Properties must have a name');
        }

        $name = $this->_project->replaceProperties($name->nodeValue);

        $value = $taskConf->item(0)->attributes->getNamedItem('value');
        if (!$value)
        {
            throw new Bs_Excepction('Properties must have a value');
        }

        $value = $this->_project->replaceProperties($value->nodeValue);

        $this->_name = $name;
        $this->_value = $value;
    }

}