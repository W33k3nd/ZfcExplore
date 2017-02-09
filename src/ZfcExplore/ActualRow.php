<?php
namespace ZfcExplore;

use Zend\Stdlib\ArrayObject;
class ActualRow extends ArrayObject{
    
    private $columns;
    
    /**
     * 
     * @param array $columns
     */
    public function __construct(array $columns){
        $this->columns = $columns;
        parent::__construct();
    }
    /**
     * 
     * @param array $input
     */
    public function setActualRow($row){
        $this->storage = $row;
    }
    
    /**
     * @return array
     */
    public function getColumnsData(){
        
        return array_intersect_key($this->storage, array_flip($this->columns));
    }
}