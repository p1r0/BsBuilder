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

        $type = $taskConf->item(0)->attributes->getNamedItem('type');
        if (!$type)
        {
            $type = 'plain';
        } else
        {
            $type = $this->_project->replaceProperties($type->nodeValue);
        }

        $domDoc = new DOMDocument();
        $domDoc->appendChild($domDoc->importNode($taskConf->item(0), true));

        $inflector = new Bs_Task_Strategy_Inflector();
        $strategyClass = $inflector->toCammelCase($type, 'Property_');

        /** @var Bs_Task_Strategy_Property */
        $strategy = new $strategyClass();
        $strategy->loadConfig($domDoc);
        $strategy->setProject($this->_project);

        $value = $strategy->getValue();

        $this->_name = $name;
        $this->_value = $value;
    }

}