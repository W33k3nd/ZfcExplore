<?php

namespace ZfcExplore\Decorator; 

use Zend\Stdlib\AbstractOptions;
use ZfcExplore\Col;
use ZfcExplore\ActualRow;
abstract class AbstractDecorator extends AbstractOptions{
    
    /**
     * @var Col
     */
    protected $col;
    
    /**
     * 
     * @param Col $col
     */
    public function setCol(Col $col){
        $this->col = $col;
    }
    
    /**
     * @return int
     */
    public function getIndex(){
        return $this->col->getIndex();
    }
    
    /**
     * @return ActualRow
     */
    public function getActualRow(){
        return $this->col->getActualRow();
    }
}