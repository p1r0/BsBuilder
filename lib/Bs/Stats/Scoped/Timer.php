<?php

class Bs_Stats_Scoped_Timer
{
    protected $_start_time;
    protected $_message = 'Scoped timer took %s seconds.';
    
    public function __construct($message = null)
    {
        $this->_start_time = microtime(true);
        if($message != null)
        {
            $this->_message = $message;
        }
    }
    
    public function __destruct()
    {
        $end_time = microtime(true);
        $time = sprintf('%01.4f', $end_time - $this->_start_time);
        Bs_Stats_Recorder::pushMessage(sprintf($this->_message, $time));
    }
}

?>
