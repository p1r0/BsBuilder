<?php

class Bs_Filter_Strategy_Plain implements Bs_Filter_Strategy
{
    public function match($subject, $expression)
    {
        return ($subject == $expression);
    }
}