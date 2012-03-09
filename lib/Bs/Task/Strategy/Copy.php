<?php

interface Bs_Task_Strategy_Copy
{
    public function loadConfig(DOMDocument $config);
    public function copy($source, $destination);
    public function setProject(Bs_Project $project);
}
