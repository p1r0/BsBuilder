<?php

class Bs_Filter_Strategy_RegExp implements Bs_Filter_Strategy
{
    public function match($subject, $expression)
    {
        return (preg_match($expression, $subject) > 0);
    }
}