<?php
 
namespace ZfcExplore;

use Zend\Db\TableGateway\AbstractTableGateway;
class TablesManager implements \Countable, \IteratorAggregate{
    
    /**
     * 
     */
    private $tables = array();
    
	/**
     * 
     * @param string $name
     * @param AbstractTableGateway $table
     */
    public function addTable($name, AbstractTableGateway $table){
        
        $this->tables[$name] = $table;
    }
    
    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function existTable($name){
        return array_key_exists($name, $this->tables);
    }
    
    /**
     * 
     * @param string $name
     * @throws \LogicException
     * @return AbstractTableGateway
     */
    public function getTable($name){
        
        if(!$this->existTable($name)){
            throw new \LogicException(sprintf('No Table with name "%s"', $name));
        }
        
        return $this->tables[$name];
    }
    
    /**
     * 
     * @param string $name
     */
    public function unsetTable($name){
        
        if($this->existTable($name)){
            unset($this->tables[$name]);
        }
    }
    
	/* (non-PHPdoc)
     * @see Countable::count()
     * @return int
     */
    public function count()
    {
        return count($this->tables);
    }
    
    /* (non-PHPdoc)
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->tables);
    }

}