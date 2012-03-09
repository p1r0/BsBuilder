<?php

class Bs_Filter
{
    protected $_strategy = null;
    protected $_expression = '';
    
    public function __construct($expression, $strategy = 'reg_exp')
    {
        $this->_expression = $expression;
        
        $inflector = new Bs_Filter_Strategy_Inflector();
        $strategyClass = $inflector->toCammelCase($strategy);

        /** @var Bs_Filter_Strategy */
        $strategy = new $strategyClass();

        $this->_strategy = $strategy;
    }
    
    public function match($subject)
    {
        return $this->_strategy->match($subject, $this->_expression);
    }
}