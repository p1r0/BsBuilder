<?php

class Bs_Task_Inflector
{
    public function toCammelCase($plain, $prefix = 'Bs_Task_')
    {
        return $prefix.str_replace(' ', '', ucwords(str_replace('_', ' ', $plain)));
    }
}