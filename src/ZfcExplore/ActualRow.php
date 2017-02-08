<?php
namespace ZfcExplore;

use Zend\Stdlib\ArrayObject;
class ActualRow extends ArrayObject{
    
    /**
     * 
     * @param array $input
     */
    public function setActualRow($row){
        $this->storage = $row;
    }
}