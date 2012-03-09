<?php

class Bs_Filter_Strategy_Wildcard implements Bs_Filter_Strategy
{
    public function match($subject, $expression)
    {
        //Convert $expression to regexp
        $placeholders = array(
            '\\',   //Not safe in regexps
            '.',    //Not safe in regexps
            '(',    //Not safe in regexps
            ')',    //Not safe in regexps
            '/',    //Not safe in regexps
            '?',    //Any character, only once
            '*',    //Any character, any number of characters
        );
        
        $replacers = array(
            '\\\\',   //Not safe in regexps
            '\\.',    //Not safe in regexps
            '\\(',    //Not safe in regexps
            '\\)',    //Not safe in regexps
            '\/',    //Not safe in regexps
            '(.)',    //Any character, only once
            '(.*)?',    //Any character, any number of characters
        );
        
        $expression = str_replace($placeholders, $replacers, $expression);
        $regexp = '/^'.$expression.'$/i';
        
        return (preg_match($regexp, $subject) > 0);
    }
}