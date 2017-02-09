<?php

namespace ZfcExplore;

use ZfcExplore\Decorator\Conditions\AbstractCondition;
use ZfcExplore\Decorator\PluginFactory;
use ZfcExplore\Decorator\Methodes\AbstractMethod;

class Col{
    
    /**
     * @var int
     */
    private $index; 
    
    /**
     * Database column name
     * @var string
     */
    private $name;
    
    /**
     * 
     * @var bool
     */
    private $isIndex = true;
    /**
     * 
     * @var bool
     */
    private $isName = true;
    
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
     * @var Explore
     */
    private $explore;
    
    
    /**
     * coming soon
     */
    private $reference;
    
    /**
     * 
     * @param array $options
     */
    public function __construct($options){

        $this->name = (isset($options['name']))?$options['name']:$this->isName = FALSE;
        $this->index = (isset($options['index']))?$options['index']:$this->isIndex = FALSE;
        
        if(!($this->isName || $this->isIndex)){
            throw new \Exception('Name oder Index müssen bekannt sein!');
        }
        
        if(isset($options['condition'])){
            $this->addCondition($options['condition'][0], $options['condition'][1]);
        }
        
        if(isset($options['method'])){
            if(is_callable($options['method'])){
                $this->addMethod('callback', ['Callback'=>$options['method']]);
            } else{
                $this->addMethod($options['method'][0], $options['method'][1]);
            }
        }
    }
    
    /**
     * 
     * @param string $name
     * @param array $options
     */
    public function addCondition($name, $options){
        $condition = PluginFactory::conditionFactory($name, $options);
        $condition->setCol($this);
        $this->attachCondition($condition);
        
    }
    
    /**
     * 
     * @param string $name
     * @param array $options
     */
    public function addMethod($name, $options){
        $method = PluginFactory::methodFactory($name, $options);
        $method->setCol($this);
        $this->attachMethod($method);
    }
    
    /**
     * 
     * @param AbstractCondition $condition
     */
    public function attachCondition(AbstractCondition $condition){
        $this->conditions[] = $condition;
    }
    
    /**
     * 
     * @param AbstractMethod $method
     */
    public function attachMethod(AbstractMethod $method){
        $this->methods[] = $method;
    }
    /**
     * 
     * @param Explore $explore
     */
    public function setExplore(Explore $explore){
        $this->explore = $explore;
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
     */
    public function result(){
        
        $actualRow = $this->getActualRow();
    
        if(empty($this->methods)){
            $actualRow->offsetSet($this->name, $actualRow->offsetGet($this->index));
            
        } else {
            
            foreach ($this->methods as $method){
                
                $value = $method->getValue();
                if($this->isName){
                    $actualRow->offsetSet($this->name, $value);
                }
                if($this->isIndex){
                    $actualRow->offsetSet($this->index, $value);
                }
            }
        }
    }
    /**
     * @return ActualRow
     */
    public function getActualRow(){
        return $this->explore->getActualRow();
    }
    
    /**
     * @return int
     */
    public function getIndex(){
        return $this->index;
    }
}