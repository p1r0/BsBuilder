<?php

interface Bs_Filter_Strategy
{
    public function match($subject, $expression);
}
