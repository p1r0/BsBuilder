<?php

/**
 * Bs_Target - Representation of a target
 * 
 * 
 * @package BsBuild
 * @subpackage Lib
 * @author Tabaré "pyro" Caorsi <tcaorsi@binarysputnik.com>
 * @copyright 2012 - Binarysputnik
 * @license GNU Lesser General Public License - <http://www.gnu.org/licenses/>.
 * 
 * @todo make this NON dependant on xml config type
 * @todo implemente dependecy loading
 */
class Bs_Target 
{
    protected $_dependencies = array();
    /** @var DOMDocument */
    protected $_domDocument = null;
    
    protected $_tasks = array();
    
    /** @var Bs_Project */
    protected $_project = null;
    
    /**
     * 
     * @param DOMDocument $targetDom A DOMDocument representing this target's 
     *                               portion of the config file.
     */
    public function __construct(DOMDocument $targetDom, Bs_Project $project)
    {
        $this->_domDocument = $targetDom;
        $this->_project = $project;
        $this->loadTasks();
    }
    
    /**
     * Testing function that dumps this target xml portion
     */
    public function dump()
    {
        $this->_domDocument->formatOutput = true;
        echo $this->_domDocument->saveXML();
    }
    
    public function run()
    {
        //First we run dependencies
        $this->_runDependencies();
        
        foreach($this->_tasks as $task)
        {
            $task->parseConfig();
            $task->run();
        }
    }
    
    protected function _runDependencies()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $targetConf = $xpath->query("/target");
        
        $depends = $targetConf->item(0)->attributes->getNamedItem('depends');
        if($depends)
        {
            $dependencies = explode(',', $this->_project->replaceProperties($depends->nodeValue));
            foreach($dependencies as $dependency)
            {
                $config = Bs_Configurator_Xml::getInstance();
                $target = $config->getTarget($dependency, $this->_project);
                $this->_project->runTarget($target);
            }
        }
    }
    
    protected function loadTasks()
    {
        $xpath = new DOMXPath($this->_domDocument); 
        $tasks = $xpath->query("/target/tasks/*");
        if($tasks->length == 0)
        {
            return;
        }
        else
        {
            foreach($tasks as $t)
            {
                $taskName = $t->nodeName;
                $domDoc = new DOMDocument();
                $domDoc->appendChild($domDoc->importNode($t, true));
                
                $inflector = new Bs_Task_Inflector();
                $taskClass = $inflector->toCammelCase($taskName);
                
                $this->_tasks[] = new $taskClass($domDoc, $taskName, $this->_project);
            }
        }
    }
}