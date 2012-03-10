<?php

interface Bs_Task_Strategy_Property
{
    public function loadConfig(DOMDocument $config);
    public function setProject(Bs_Project $project);
    public function getValue();
}
