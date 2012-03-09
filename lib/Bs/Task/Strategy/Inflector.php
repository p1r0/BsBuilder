<?php

class Bs_Task_Strategy_Inflector
{
    public function toCammelCase($plain, $taskPrefix = '', $prefix = 'Bs_Task_Strategy_')
    {
        return $prefix.$taskPrefix.str_replace(' ', '', ucwords(str_replace('_', ' ', $plain)));
    }
}