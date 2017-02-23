<?php

namespace ZfcExplore;

use ZfcExplore\Decorator\Conditions\AbstractCondition;
use ZfcExplore\Decorator\PluginFactory;
use ZfcExplore\Decorator\Methodes\AbstractMethod;
use ZfcExplore\Reference\AbstractReference;

class Col{
    
    /**
     * File columnnumber. Starts with 0
     * @var int |null
     */
    private $index; 
    
    /**
     * Database column name
     * @var string | null
     */
    private $name;
    
    /**
     * 
     * @var TableMetadata
     */
    private $metadata;
    
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
     * @var AbstractReference
     */
    private $reference;
    
    /**
     * 
     * @var bool
     */
    private $valid = FALSE;
    
    /**
     * 
     * @param array $options
     */
    public function __construct(TableMetadata $metadata, $options){

        $this->metadata = $metadata;
        $this->name = (isset($options['name']))?$options['name']: NULL;
        $this->index = (isset($options['index']))?$options['index']:NULL;
        
        if(!($this->name || $this->index)){
            throw new \Exception('Name oder Index müssen bekannt sein!');
        }
        
        //@TODO: Validiere| Condition brauchen den index auf jeden Fall
        if(isset($options['condition']) && $this->index !== FALSE){
            $this->addCondition($options['condition']['name'], $options['condition']['options']);
        }
        
        if(isset($options['method'])){
            $options['methods'] = [];
            $options['methods'][] = $options['method'];
            unset($options['method']);
        }
        
        //@todo: Validiere| Methoden brauchen den column (name) auf jeden Fall
        if(isset($options['methods']) && $this->name){
            foreach ($options['methods'] as $method){
                if(is_callable($method)){
                    $this->addMethod('callback', ['callback'=>$method]);
                } else{
                    $options = isset($method['options'])?$method['options']:null;
                    $this->addMethod($method['name'], $options);
                }
            }
        }
        
        if(isset($options['reference']) && $this->name){
            $class = '\ZfcExplore\Reference\IdentReference';
            if(isset($options['reference']['type'])){
                $class = $options['reference']['type'];
                unset($options['reference']['type']);
            }
            //@todo Factory
            $this->reference = new $class($options['reference']);
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
     * @return array
     */
    public function getActualData(){
        $ac =  $this->metadata->getActualRow();
        return array_merge($ac->getIndexData(), $ac->getColumnData());
    }
    
    /**
     * 
     * @param string | NULL $name
     * @return mixed
     */
    public function getActualName($name = NULL){
        
        if($name === Null){
            $name = $this->name;
        }
        return $this->metadata->getActualRow()->getColumn($name);
    }
    
    /**
     * 
     * @param int | NULL $index
     * @return mixed
     */
    public function getActualIndex($index = NULL){
        
        if($index === Null){
            $index = $this->index;
        }
        return $this->metadata->getActualRow()->getIndex($index);
    }
    /**
     * @return int
     */
    public function getIndex(){
        return $this->index;
    }
    
    /**
     * 
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * @return boolean
     */
    public function hasReference(){
        return isset($this->reference);
    }
    
    /**
     * 
     * @return \ZfcExplore\Reference\AbstractReference
     */
    public function getReference(){
        return $this->reference;
    }
    
    
    /**
     * Validate all index conditions.
     * @return boolean
     */
    public function validCondition(){
        
        $this->valid = true;
        foreach ($this->conditions as $condition){
            if(!$condition->isValid){
                $this->valid = false;
                break;
            }
        }
        return $this->valid;
    }
    
    /**
     * 
     */
    public function result(){
        
        if(!$this->valid){
            return;
        }
        $actualRow = $this->metadata->getActualRow();
        foreach ($this->methods as $method){
            $value = $method->getValue();
            $actualRow->setColumn($this->name, $value);

        }
        
        if($this->hasReference()){
            $ref = $this->reference->refer($actualRow);
            if($ref !== false){
                $actualRow->setColumn($this->name, $ref);
            }
            
        }
    }
}