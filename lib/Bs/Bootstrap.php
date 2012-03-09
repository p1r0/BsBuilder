<?php

/**
 * Bs_Bootstrap - Main bootstrap class
 * 
 * Bootstraps the application and run basic test and then runs the starting 
 * target
 * 
 * @package BsBuild
 * @subpackage Lib
 * @author Tabaré "pyro" Caorsi <tcaorsi@binarysputnik.com>
 * @copyright 2012 - Binarysputnik
 * @license GNU Lesser General Public License - <http://www.gnu.org/licenses/>.
 * 
 * @todo allow setting custom configurators
 */

class Bs_Bootstrap
{
    protected static $_instance = null;
    /** @var Bs_Console */
    protected $_console;
    
    protected function __construct()
    {
        $this->_console = new Bs_Console();
    }
    
    /**
     * Return instance of Boostrap
     * 
     * @return Bs_Bootstrap
     */
    public static function getInstance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     *
     * @param String $targetName Name of the starting point target, null if default
     * @param String $configFile Path to config file. If null ./build.conf.xml 
     *                           will be used by config loader.
     */
    public function run($targetName = null, $configFile = null)
    {
        $config = Bs_Configurator_Xml::getInstance();
        $config->loadConfigFile($configFile);
        $config->test();
        
        $projectName = $config->getProjectName();
        
        $project = new Bs_Project($config->getDomDocument(), $projectName);
        
        $this->_console->printLn("\nStarted build for project '".$projectName."'...");
        
        $project->run($targetName, $configFile);
    }
    
}