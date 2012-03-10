<?php

class Bs_Task_Strategy_Property_Plain implements Bs_Task_Strategy_Property
{
    /** @var Bs_Project */
    protected $_project = null;
    /** @var DOMDocument */
    protected $_domDocument = null;
    
    public function getValue()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $property = $xpath->query("/property");
        
        if($property->length < 1)
        {
            throw new Bs_Excepction('Properties config not found!');
        }
        
        $value = $property->item(0)->attributes->getNamedItem('value');
        if(!$value)
        {
            throw new Bs_Excepction('Properties must have a value');
        }
        
        return $this->_project->replaceProperties($value->nodeValue);
    }

    public function loadConfig(DOMDocument $config)
    {
        $this->_domDocument = $config;
    }

    public function setProject(Bs_Project $project)
    {
        $this->_project = $project;
    }

}