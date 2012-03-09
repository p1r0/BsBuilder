<?php

interface Bs_Task_Strategy_Replace
{
    public function loadConfig(DOMDocument $config);
    public function replace($value, $newvalue, $file);
    public function setProject(Bs_Project $project);
}
