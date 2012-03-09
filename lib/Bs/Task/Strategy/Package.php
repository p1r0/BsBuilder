<?php

interface Bs_Task_Strategy_Package
{
    public function loadConfig(DOMDocument $config);
    public function package($name, $destination);
    public function setProject(Bs_Project $project);
}
