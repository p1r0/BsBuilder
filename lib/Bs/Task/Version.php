<?php

class Bs_Task_Version extends Bs_Task
{

    protected $_type = '';
    protected $_currentString = '';
    protected $_major = 0;
    protected $_minor = 0;
    protected $_build = 0;
    protected $_propertyExport = '';
    protected $_versionFile = '';

    public function run()
    {
        $this->_loadVersionFile();
        
        if($this->_type == 'major')
        {
            $this->_major++;
        }
        else if($this->_type == 'minor')
        {
            $this->_minor++;
        }
        
        $this->_build++;
        
        $this->_saveVersionFile();
        
        if($this->_propertyExport != '')
        {
            $this->_project->setProperty($this->_propertyExport, $this->_currentString);
        }
    }

    public function parseConfig()
    {
        $xpath = new DOMXPath($this->_domDocument);
        $taskConf = $xpath->query("/" . $this->_taskName);
        if ($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for ' . $this->_taskName);
        }

        $type = $taskConf->item(0)->attributes->getNamedItem('type');
        if (!$type)
        {
            throw new Bs_Excepction('Versions must have a type');
        }

        $type = $this->_project->replaceProperties($type->nodeValue);

        $file = $taskConf->item(0)->attributes->getNamedItem('file');
        if (!$file)
        {
            throw new Bs_Excepction('Versions must have a file');
        }

        $file = $this->_project->replaceProperties($file->nodeValue);
        
        $property = $taskConf->item(0)->attributes->getNamedItem('property');
        if ($property)
        {
            $property = $this->_project->replaceProperties($property->nodeValue);
            $this->_propertyExport = $property;
        }

        $this->_type = $type;
        $this->_versionFile = $file;
    }
    
    protected function _loadVersionFile()
    {
        if(!file_exists($this->_versionFile))
        {
            return;
        }
        
        $this->_currentString = file_get_contents($this->_versionFile);
        $vals = explode('.', $this->_currentString);
            
        $this->_major = intval($vals[0]);
        $this->_minor = intval($vals[1]);
        $this->_build = intval($vals[2]);
    }
    
    protected function _saveVersionFile()
    {
        $str = $this->_major.'.'.$this->_minor.'.'.$this->_build;
        file_put_contents($this->_versionFile, $str);
        $this->_currentString = $str;
    }

}