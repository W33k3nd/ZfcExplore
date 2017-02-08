<?php

namespace ZfcExplore\Decorator;

use ZfcExplore\Options;
class DecoratorManager{
    /**
     * 
     * @var ConditionPluginManager
     */
    private $conditionManager;
    
    /**
     * 
     * @var MethodPluginManager
     */
    private $methodManager;
    
    /**
     * 
     * @var array
     */
    private $indexPluginMapper = array();
    
    
    public function __construct(Options $options){
        
        $options->getConditions();
    }
    
    /**
     * @return bool
     */
    public function isConditionValid(){
        return true;
    }
}