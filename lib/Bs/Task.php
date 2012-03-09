<?php

abstract class Bs_Task
{
    /** @var DOMDocument */
    protected $_domDocument = null;
    
    protected $_taskName = '';
    
    /** @var Bs_Project */
    protected $_project = null;
    
    /**
     * 
     * @param DOMDocument $targetDom A DOMDocument representing this task's 
     *                               portion of the config file.
     */
    public function __construct(DOMDocument $targetDom, $taskName, 
                                Bs_Project $project)
    {
        $this->_domDocument = $targetDom;
        $this->_taskName = $taskName;
        $this->_project = $project;
    }
    
    /**
     * Testing function that dumps this target xml portion
     */
    public function dump()
    {
        $this->_domDocument->formatOutput = true;
        echo $this->_domDocument->saveXML();
    }
    
    public abstract function run();
    public abstract function parseConfig();
}