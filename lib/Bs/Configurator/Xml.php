<?php

/**
 * Bs_Configurator_Xml - XML configuration parset
 * 
 * Parses application config file and has convenience function for other properties
 * 
 * @package BsBuild
 * @subpackage Lib
 * @author Tabaré "pyro" Caorsi <tcaorsi@binarysputnik.com>
 * @copyright 2012 - Binarysputnik
 * @license GNU Lesser General Public License - <http://www.gnu.org/licenses/>.
 * 
 */
class Bs_Configurator_Xml
{
    protected static $_instance = null;
    protected $_domDocument = null;
    
    protected function __construct()
    {
        ;
    }
    
    /**
     *
     * @return Bs_Configurator_Xml
     */
    public static function getInstance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function getDomDocument()
    {
        return $this->_domDocument;
    }
    
    
    /**
     * Loads a configuratio file into memory
     * 
     * @param string $file Config file path. ./build.conf.xml will be used if null
     * @throws Bs_Configurator_Exception_FileNotFound
     */
    public function loadConfigFile($file = null)
    {
        if($file == null)
        {
            $file = './build.conf.xml';
        }
        
        $this->_domDocument = new DOMDocument(); 
        if(!@$this->_domDocument->load($file))
        {
            throw new Bs_Configurator_Exception_FileNotFound();
        }
    }
    
    /**
     * Does some basic testing on the configuration file
     * 
     * @return mixed true on success 
     * @throws Bs_Excepction if root 'project' element is not present
     */
    public function test()
    {
        $xpath = $this->getXpath();
        $project = $xpath->query("/project");
        if($project->length == 0)
        {
            throw new Bs_Excepction('No "project attribute found"');
        }
        else
        {
            return true;
        }
    }
    
    /**
     * Return the project name as set in project element of config file
     * 
     * @return string The project name 
     * @throws Bs_Excepction if no 'name' parameter is presen for project element
     */
    public function getProjectName()
    {
        $xpath = $this->getXpath();
        $project = $xpath->query("/project");
        if($project->length == 0)
        {
            throw new Bs_Excepction('No "project attribute found"');
        }
        else
        {
            return $project->item(0)->attributes->getNamedItem('name')->nodeValue;
        }
    }
    
    /**
     * Return the default target as set in config file
     * 
     * @return Bs_Target 
     * @throws Bs_Excepction if no default target is set
     */
    public function getDefaultTarget(Bs_Project $project)
    {
        $xpath = $this->getXpath();
        $projectName = $xpath->query("/project");
        if($projectName->length == 0)
        {
            throw new Bs_Excepction('No "project attribute found"');
        }
        else
        {
            $targetName = $projectName->item(0)->attributes->getNamedItem('default')->nodeValue;
            return $this->getTarget($targetName, $project);
        }
    }
    
    /**
     * Finds a target in the configuration with a given name and return a
     * DOMDocument representation of it.
     * 
     * @param String $targetName the target name
     * @return Bs_Target 
     */
    public function getTarget($targetName, Bs_Project $project)
    {
        $xpath = $this->getXpath();
        $target = $xpath->query("/project/target[@name='".$targetName."']");
        if($target->length == 0)
        {
            throw new Bs_Excepction('Target '.$targetName.' not found.');
        }
        
        $domDoc = new DOMDocument();
        $domDoc->appendChild($domDoc->importNode($target->item(0), true));

        return new Bs_Target($domDoc, $project);
    }
    
    protected function getXpath()
    {
        return new DOMXPath($this->_domDocument); 
    }
}