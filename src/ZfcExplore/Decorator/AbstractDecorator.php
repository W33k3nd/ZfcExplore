<?php

namespace ZfcExplore\Decorator; 

abstract class AbstractDecorator{
    
    /**
     * Actual row
     * @var array
     */
    protected $actualRow;
    
    /**
     * Actual index
     */
    protected $index;
    
    /**
     * 
     * @param \ActualRow $actualRow
     */
    public function setActualRow($actualRow){
        $this->actualRow = $actualRow;
    }
    
    /**
     *
     * @param int | string $index
     */
    public function setIndex($index){
    
        $this->index = $index;
    }
    
}