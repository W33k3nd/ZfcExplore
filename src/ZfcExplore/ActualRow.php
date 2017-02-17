<?php
namespace ZfcExplore;

class ActualRow{
    
    /**
     * Current column data from index.
     * @var array
     */
    private $columns = [];
    
    /**
     * Map columnname with index
     * @var array
     */
    private $map = [];
    
    /**
     * Actual rows with index
     * @var array
     */
    private $currentRow;
    
    /**
     * 
     * @param array $input
     */
    public function setActualRow($row){
        
        foreach ($this->map as $column => $index){
        
            if(isset($row[$index])){
                $this->columns[$column] = $row[$index];
            }
        }
        $this->currentRow = $row;
    }
    
    /**
     * The first parameter discribes the table column name and the second the index of the file
     * @param string $name
     * @param int $index
     */
    public function addColumn($name, $index){
        $this->map[$name] = $index;
    }
    
    /**
     * @return array
     */
    public function getColumnData(){
        return $this->columns;
    }
    
    /**
     * 
     * @return array
     */
    public function getIndexData(){
        return $this->currentRow;
    }
    
    /**
     * All data
     * @return array
     */
    public function getData(){
        return array_merge($this->currentRow, $this->columns);
    }
    
    /**
     * 
     * @return array
     */
    public function getColumns(){
        return array_keys($this->map);
    }
    
    /**
     * 
     * @param string $column
     * @return multitype: scalar
     */
    public function getColumn($column){
        return $this->columns[$column];
    }
    
    /**
     * 
     * @param string $column
     * @param multitype $value
     */
    public function setColumn($column, $value){
        
        if(array_key_exists($column, $this->columns)){
            $this->columns[$column] = $value;
        }
    }
    
    /**
     * 
     * @param int $index
     * @return multitype: scalar
     */
    public function getIndex($index){
        
        if(!isset($this->currentRow[$index])){
            throw new \Exception(sprintf('No index column [%s]', $index));
        }
        return $this->currentRow[$index];
    }
    
    /**
     * @see Return the number of indices
     * @return int
     */
    public function count(){
        return count($this->currentRow);
    }
}