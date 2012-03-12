<?php

class Bs_Task_GithubUpload extends Bs_Task
{   
    protected $_username = '';
    protected $_password = '';
    protected $_repo = '';
    protected $_file = '';
    
    
    public function run()
    {
        $console = new Bs_Console();
        
        $this->_uploadFile();
    }
    
    public function parseConfig()
    {
        if(!function_exists('curl_init'))
        {
            throw new Bs_Exception('lib cUrl is not installed');
        }

        $xpath = new DOMXPath($this->_domDocument); 
        $taskConf = $xpath->query("/".$this->_taskName);
        if($taskConf->length == 0)
        {
            throw new Bs_Excepction('No task config found for '.$this->_taskName);
        }
        
        $username = $taskConf->item(0)->attributes->getNamedItem('username');
        if($username)
        {
             $this->_username = $this->_project->replaceProperties($username->nodeValue);
        }
        
        $password = $taskConf->item(0)->attributes->getNamedItem('password');
        if($password)
        {
             $this->_password = $this->_project->replaceProperties($password->nodeValue);
        }
        
        $file = $taskConf->item(0)->attributes->getNamedItem('file');
        if($file)
        {
             $this->_file = $this->_project->replaceProperties($file->nodeValue);
        }
        
        $repo = $taskConf->item(0)->attributes->getNamedItem('repository');
        if($repo)
        {
             $this->_repo = $this->_project->replaceProperties($repo->nodeValue);
        }
    }
    
    protected function _uploadFile()
    {
        $console = new Bs_Console();
        $console->printLn('Connecting to github...');
        $ch = curl_init();

        $url = "https://api.github.com/repos/{$this->_username}/{$this->_repo}/downloads";
        
        // Now set some options (most are optional)
        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'bsbuilder');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_USERPWD, $this->_username.':'.$this->_password);
        curl_setopt($ch, CURLOPT_POST, true);
        

        $parameters = array('name'=>  basename($this->_file),
                            'size', filesize($this->_file));
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        
        
        // Download the given URL, and return output
        $json = curl_exec($ch);

        
        $info = json_decode($json);
        
        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 201)
        {
            $console->printLn('ERROR! GitHub said: '.$info->message, 'red');
        }      
        
        curl_close($ch); 
        
        
        //Do the actual upload
        $ch = curl_init();
        
        $url = $info->s3_url;
        
        // Now set some options (most are optional)
        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'bsbuilder');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        

        $parameters = array('key'=> $info->path,
                            'acl'=> $info->acl,
                            'success_action_status'=>'201',
                            'Filename'=>$info->name,
                            'AWSAccessKeyId'=>$info->accesskeyid,
                            'Policy'=>$info->policy,
                            'Signature'=>$info->signature,
                            'Content-Type'=>$info->mime_type,
                            'file'=>'@'.$this->_file);
        
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        
        // Download the given URL, and return output
        $data = curl_exec($ch);

        if(curl_getinfo($ch, CURLINFO_HTTP_CODE) != 201)
        {
            $console->printLn('ERROR! Amazon said: '.$data, 'red');
        }      
        
        curl_close($ch); 
    }
}