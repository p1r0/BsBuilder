#!/usr/bin/php
<?php

require realpath(dirname(__FILE__)) . "/lib/Bs/Stats/Scoped/Timer.php";

$timer = new Bs_Stats_Scoped_Timer('Total running time: %s seconds.');

function shutdown()
{
    Bs_Stats_Recorder::printStats();
}

require realpath(dirname(__FILE__)) . "/lib/Bs/Loader.php";

set_include_path(implode(PATH_SEPARATOR, array(
            realpath(dirname(__FILE__) . "/"),
            get_include_path(),
        )));

spl_autoload_register('Bs_Loader::load');

register_shutdown_function('shutdown');

/** @var Bs_Console */
$console = new Bs_Console();
if(file_exists(realpath(dirname(__FILE__)) .'/version.txt'))
{
   define('VERSION', file_get_contents(realpath(dirname(__FILE__)) .'/version.txt'));
}
else
{
    define('VERSION', '');
}
$console->printLn('BSBuilder - Version: '.VERSION);
$console->printLn('====================================');

try
{
    $targetName = null;

    if($argc > 1)
    {
        $targetName = $argv[1];
    }
    /**
     * @todo get configuration file from command line options.
     *       For now build.conf.xml will be asumed.
     */
    Bs_Bootstrap::getInstance()->run($targetName);
}
catch(Bs_Configurator_Exception_FileNotFound $e)
{
    $console->printLn('Configuration file not found.', 'red');
    exit(2);
}


unset($timer);
