<?php
namespace ZfcExplore;

class ActualRow{
    
    /**
     * Current column data from index.
     * @var array
     */
    private $intersect = [];
    
    /**
     * Map column name with index
     * @var array
     */
    private $map = [];
    
    /**
     * Actual rows with index
     * @var array
     */
    private $currentRow = [];
    
    /**
     * 
     * @param array $input
     */
    public function setActualRow($row){

        foreach ($this->map as $column => $index){
        
            if(isset($row[$index])){
                $this->intersect[$column] = $row[$index];
            }
        }
        $this->currentRow = $row;
    }
    
    /**
     * The first parameter discribes the table column name and the second the index of the file
     * @param string $name
     * @param int|null $index
     */
    public function addColumn($name, $index){
        $this->map[$name] = $index;
        
        if($index !== NULL){
            $this->setColumn($name, NULL);
        }
    }
    
    /**
     * Prepared data
     * @return array
     */
    public function getColumnData(){
        return $this->intersect;
    }
    
    /**
     * Raw data
     * @return array
     */
    public function getIndexData(){
        return $this->currentRow;
    }
    
    /**
     * Return intersect Columns, which only has a index.
     * @return array
     */
    public function getIntersectColumns(){
        return array_keys($this->intersect);
    }
    
    /**
     * This map shows the name index relation. 
     * @return array
     */
    public function getMap(){
        return $this->map;
    }
    
    /**
     * 
     * @param string $column
     * @throws \Exception
     * @return multitype: scalar
     */
    public function getColumn($column){
        
        if(!(isset($this->intersect[$column]) || array_key_exists($column, $this->intersect))){
            throw new \Exception(sprintf('No name [%s] in intersect data.', $column));
        }
        return $this->intersect[$column];
    }
    
    /**
     * 
     * @param string $column
     * @param multitype $value
     */
    public function setColumn($column, $value){
        $this->intersect[$column] = $value;
    }
    
    /**
     * 
     * @param int $index
     * @throws \Exception
     * @return multitype: scalar
     */
    public function getIndex($index){
        
        if(!(isset($this->currentRow[$index]) || array_key_exists($index, $this->currentRow))){
            throw new \Exception(sprintf('No index [%s] in current data.', $index));
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