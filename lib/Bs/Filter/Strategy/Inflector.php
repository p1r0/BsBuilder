<?php

class Bs_Filter_Strategy_Inflector
{
    public function toCammelCase($plain, $taskPrefix = '', $prefix = 'Bs_Filter_Strategy_')
    {
        return $prefix.$taskPrefix.str_replace(' ', '', ucwords(str_replace('_', ' ', $plain)));
    }
}