<?php

class Bs_Console
{
    private $_colors = null;
    
    public function __construct()
    {
        $this->_colors = new Bs_Console_Color();
    }
    
    public function printLn($string, $foreground_color = null, $background_color = null)
    {
        if($foreground_color == null && $background_color == null)
        {
            echo $string."\n";
        }
        else
        {
            echo $this->_colors->getColoredString($string, $foreground_color, $background_color)."\n";
        }
    }
}
?>
