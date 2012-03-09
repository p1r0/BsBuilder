<?php

class Bs_Loader
{
    public static function load($classname)
    {
        $base = realpath(dirname(__FILE__));
        @include_once($base."/../".  str_replace('_', DIRECTORY_SEPARATOR, $classname).".php");
    }
}
?>
