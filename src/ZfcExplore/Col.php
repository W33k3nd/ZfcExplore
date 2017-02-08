<?php

namespace ZfcExplore;

use ZfcExplore\Decorator\Conditions\AbstractCondition;
use ZfcExplore\Decorator\PluginFactory;
use ZfcExplore\Decorator\Methodes\AbstractMethod;
class Col{
    
    /**
     * @var int
     */
    private $col; 
    
    /**
     * 
     * @var Database column name
     */
    private $name;
    
    /**
     * 
     * @var array
     */
    private $conditions = array();
    
    /**
     * 
     * @var array
     */
    private $methods = array();
    
    /**
     * 
     * @var ActualRow
     */
    private $actualRow;
    
    /**
     * 
     * @param array $options
     */
    public function __construct($options){

        $this->name = isset($options['name'])?:$options['name'];
        $this->col = isset($options['index'])?:$options['index'];
        
        if(!($this->name || $this->col)){
            throw new \Exception('Name oder Index müssen bekannt sein!');
        }
        
        if(isset($options['condition'])){
            $this->addCondition($options['condition'][0], $options['condition'][1]);
        }
        
        if(isset($options['method'])){
            if(is_callable($options['method'])){
                
            } else{
                $this->addMethod($options['method'][0], $options['method'][1]);
            }
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasColumnName(){
        return (bool) $this->name;
    }
    /**
     * 
     * @param string $name
     * @param array $options
     */
    public function addCondition($name, $options){
        $condition = PluginFactory::conditionFactory($name, $options);
        $condition->setActualRow($this->actualRow);
        $this->attachCondition($condition);
        
    }
    
    /**
     * 
     * @param string $name
     * @param array $options
     */
    public function addMethod($name, $options){
        $method = PluginFactory::methodFactory($name, $options);
        $method->setActualRow($this->actualRow);
        $this->attachMethod($method);
    }
    
    /**
     * 
     * @param AbstractCondition $condition
     */
    public function attachCondition(AbstractCondition $condition){
        $condition->setIndex($this->col);
        $this->conditions[] = $condition;
    }
    
    /**
     * 
     * @param AbstractMethod $method
     */
    public function attachMethod(AbstractMethod $method){
        $method->setIndex($this->col);
        $this->methods[] = $method;
    }
    /**
     * 
     * @param \ActualRow $row
     */
    public function setActualRow($row){
        $this->actualRow = $row;
    }
    
    /**
     * 
     * @return boolean
     */
    public function validCondition(){
        
        foreach ($this->conditions as $condition){
            if(!$condition->isValid){
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * @return array
     */
    public function result(){
        
        $arr = array();
        foreach ($this->methods as $method){
            $this->actualRow->offsetSet($this->col, $method->getValue());
        }
        
        return $this->actualRow->offsetGet($this->col);
    }
}