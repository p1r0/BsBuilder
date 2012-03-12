<?php

class Bs_Task_Strategy_Property_Password implements Bs_Task_Strategy_Property
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
        
        echo 'Enter value for '.$name.$def.': ';
        $val = $this->_getPassword(true);
        echo "\n";
        
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
    
    /**
     * Get a password from the shell.
     *
     * This function works on *nix systems only and requires shell_exec and stty.
     *
     * Original code found at: http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli
     * 
     * @param  boolean $stars Wether or not to output stars for given characters
     * @return string
     */
    protected function _getPassword($stars = false)
    {
        // Get current style
        $oldStyle = shell_exec('stty -g');

        if ($stars === false) {
            shell_exec('stty -echo');
            $password = rtrim(fgets(STDIN), "\n");
        } else {
            shell_exec('stty -icanon -echo min 1 time 0');

            $password = '';
            while (true) {
                $char = fgetc(STDIN);

                if ($char === "\n") {
                    break;
                } else if (ord($char) === 127) {
                    if (strlen($password) > 0) {
                        fwrite(STDOUT, "\x08 \x08");
                        $password = substr($password, 0, -1);
                    }
                } else {
                    fwrite(STDOUT, "*");
                    $password .= $char;
                }
            }
        }

        // Reset old style
        shell_exec('stty ' . $oldStyle);

        // Return the password
        return $password;
    }

}