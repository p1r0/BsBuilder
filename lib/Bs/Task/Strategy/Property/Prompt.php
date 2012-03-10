<?php

class Bs_Task_Strategy_Property_Prompt implements Bs_Task_Strategy_Property
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
            $value = '';
        }
        else
        {
            $value = $this->_project->replaceProperties($value->nodeValue);
        }
        
        $name = $property->item(0)->attributes->getNamedItem('name')->nodeValue;
        $def = $value != '' ? ' ['.$value.']' : '';
        $val = readline('Enter value for '.$name.$def.': ');
        if(trim($val) != '')
        {
            $value = $val;
        }
        
        return $value;
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