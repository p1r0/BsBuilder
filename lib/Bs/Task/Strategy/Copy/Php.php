<?php

class Bs_Task_Strategy_Copy_Php implements Bs_Task_Strategy_Copy
{

    protected $_ignores = array();
    protected $_fileList = array();
    protected $_dirList = array();
    protected $_useMemory = false;
    protected $_memoryFile = '';
    protected $_checksums = array();
    protected $_project = null;

    public function loadConfig(DOMDocument $config)
    {
        $xpath = new DOMXPath($config);
        
        $me = $ignores = $xpath->query("/copy");
        
        $memory = $me->item(0)->attributes->getNamedItem('memory');
        if($memory)
        {
            if($memory->nodeValue == 'on')
            {
                $this->_useMemory = true;
                $memoryFile = $me->item(0)->attributes->getNamedItem('memory_file');
                if($memoryFile)
                {
                    $this->_memoryFile = $this->_project->replaceProperties($memoryFile->nodeValue);
                }
                else
                {
                    throw new Bs_Excepction('No memory file provided');
                }
            }
        }
        
        $ignores = $xpath->query("/copy/ignore");

        if ($ignores->length == 0)
        {
            return;
        }

        foreach ($ignores as $ignore)
        {
            $type = $ignore->attributes->getNamedItem('type');
            if($type)
            {
                $type = $this->_project->replaceProperties($type->nodeValue);
            }
            else
            {
                $type = 'plain';
            }

            $expression = $ignore->attributes->getNamedItem('name')->nodeValue;
            
            $this->_ignores[] = new Bs_Filter($expression, $type);
        }
    }

    public function copy($source, $destination)
    {
        $console = new Bs_Console();
        
        if($this->_useMemory)
        {
            //Load data
            if(file_exists('./.build_data/'.$this->_memoryFile))
            {
                $this->_checksums = unserialize(
                            file_get_contents('./.build_data/'.$this->_memoryFile));
            }
        }
        
        $this->_prepareFileList($source);
        
        if($this->_useMemory)
        {
            @mkdir('./.build_data');
            file_put_contents('./.build_data/'.$this->_memoryFile, 
                              serialize($this->_checksums));
        }
        
        $this->_createDirs($source, $destination);
        
        $totalFiles = count($this->_fileList);
        $console->printLn('Total files to copy: ' . $totalFiles);

        if($totalFiles > 0)
        {
            $bar = new Bs_Console_ProgressBar('[%bar%] %percent%', '=>', ' ', 80,
                            $totalFiles);

            for ($i = 0; $i < $totalFiles; $i++)
            {
                $bar->update($i);
                $this->_copy($this->_fileList[$i], $destination);
            }
            
            $bar->update($i);
        }
        
        $console->printLn('');
    }

    public function setProject(Bs_Project $project)
    {
        $this->_project = $project;
    }
    
    protected function _prepareFileList($source)
    {
        $current = dir($source);
        while ($entry = $current->read())
        {
            if ($entry != "." && $entry != "..")
            {
                if (is_dir($source . DIRECTORY_SEPARATOR . $entry))
                {
                    if (!$this->_isIgnored($source . DIRECTORY_SEPARATOR . $entry))
                    {
                        $this->_dirList[] = $source . DIRECTORY_SEPARATOR . $entry;
                        $this->_prepareFileList($source . DIRECTORY_SEPARATOR . $entry);
                    }
                } else
                {
                    if (!$this->_isIgnored($source . DIRECTORY_SEPARATOR . $entry))
                    {
                        if($this->_useMemory)
                        {
                            $this->_addChecksum($source . DIRECTORY_SEPARATOR . $entry);
                        }
                        $this->_fileList[] = $source . DIRECTORY_SEPARATOR . $entry;
                    }
                }
            }
        }
        $current->close();
    }

    protected function _isIgnored($path)
    {
        foreach($this->_ignores as $filter)
        {
            if($filter->match($path))
            {
                return true;
            }
        }
        
        if($this->_useMemory)
        {
            if(isset($this->_checksums[$path]))
            {
                if($this->_getChecksum($path) == $this->_checksums[$path])
                {
                    return true;
                }
            }
        }

        return false;
    }

    protected function _copy($source, $destination)
    {
        if (substr($source, 0, 2) == '.' . DIRECTORY_SEPARATOR)
        {
            $source = substr($source, 2);
        } else if (substr($source, 0, 3) == '..' . DIRECTORY_SEPARATOR)
        {
            $source = substr($source, 3);
        }

        $destFile = $destination . DIRECTORY_SEPARATOR . $source;
        copy($source, $destFile);
    }

    protected function _createDirs($source, $destination)
    {
        $console = new Bs_Console();
        $totalDirs = count($this->_dirList);
        $console->printLn('Total directories to copy: ' . $totalDirs);

        $bar = new Bs_Console_ProgressBar('[%bar%] %percent%', '=>', ' ', 80,
                        $totalDirs -1);

        for ($i = 0; $i < $totalDirs; $i++)
        {
            $bar->update($i);
            $this->_mkdir($this->_dirList[$i], $destination);
        }
        
        $console->printLn('');
    }
    
    protected function _mkdir($source, $destination)
    {
        if (substr($source, 0, 2) == '.' . DIRECTORY_SEPARATOR)
        {
            $source = substr($source, 2);
        } else if (substr($source, 0, 3) == '..' . DIRECTORY_SEPARATOR)
        {
            $source = substr($source, 3);
        }

        $destFile = $destination . DIRECTORY_SEPARATOR . $source;
        @mkdir($destFile, 0777, true);
    }
    
    protected function _addChecksum($file)
    {
        $this->_checksums[$file] = $this->_getChecksum($file);
    }
    
    protected function _getChecksum($file)
    {
        $cmd = 'md5sum "'.$file.'"';
        return substr(exec($cmd), 0, 32);
    }
}
