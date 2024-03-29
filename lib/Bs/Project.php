<?php

class Bs_Project
{

    /** @var DOMDocument */
    protected $_domDocument = null;
    protected $_name = '';
    protected $_properties = array();

    /**
     * 
     * @param DOMDocument $targetDom A DOMDocument representing the project's 
     *                               config file.
     */
    public function __construct(DOMDocument $targetDom, $projectName)
    {
        $this->_domDocument = $targetDom;
        $this->_name = $projectName;

        $this->_loadProperties();
    }

    public function run($targetName = null, $configFile = null)
    {
        $config = Bs_Configurator_Xml::getInstance();

        //Get starting target
        if ($targetName == null)
        {
            $target = $config->getDefaultTarget($this);
        } else
        {
            if ($targetName == 'help')
            {
                $target = new Bs_Target_Help($this->_domDocument, $this);
            } else
            {
                $target = $config->getTarget($targetName, $this);
            }
        }

        $this->runTarget($target);
    }

    public function runTarget(Bs_Target $target)
    {
        $target->run();
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
    }

    public function getProperty($name)
    {
        if (!isset($this->_properties[$name]))
        {
            throw new Bs_Excepction('Property with name "' . $name . '" is not registered');
        }

        return $this->_properties[$name];
    }

    public function replaceProperties($inputString)
    {
        $placeholders = array();
        $values = array();

        foreach ($this->_properties as $key => $val)
        {
            $placeholders[] = '${' . $key . '}';
            $values[] = $val;
        }

        return str_replace($placeholders, $values, $inputString);
    }

    protected function _loadProperties()
    {
        //Some default properties
        $this->setProperty('date', date('m/d/Y'));
        $this->setProperty('compact_date', date('YmdHis'));
        $this->setProperty('bsbuilder_version', VERSION);

        $xpath = new DOMXPath($this->_domDocument);
        $properties = $xpath->query("/project/property");

        foreach ($properties as $prop)
        {
            $name = $prop->attributes->getNamedItem('name');
            if (!$name)
            {
                throw new Bs_Excepction('Properties must have a name');
            }

            $name = $this->replaceProperties($name->nodeValue);

            $type = $prop->attributes->getNamedItem('type');
            if (!$type)
            {
                $type = 'plain';
            } else
            {
                $type = $this->replaceProperties($type->nodeValue);
            }

            $domDoc = new DOMDocument();
            $domDoc->appendChild($domDoc->importNode($prop, true));

            $inflector = new Bs_Task_Strategy_Inflector();
            $strategyClass = $inflector->toCammelCase($type, 'Property_');
            
            /** @var Bs_Task_Strategy_Property */
            $strategy = new $strategyClass();
            $strategy->loadConfig($domDoc);
            $strategy->setProject($this);

            $value = $strategy->getValue();

            $this->setProperty($name, $value);
        }
    }

}