<?php

namespace ZfcExplore;

use Zend\Stdlib\AbstractOptions;
use Zend\Db\Adapter\AdapterInterface;
class TableMetadata extends AbstractOptions{

	/**
	 * Turn off strict options mode
	 */
	protected $__strictMode__ = false;

	/**
	 * 
	 * @var ExploreManager
	 */
	private $em;
	
	/**
	 * @var string
	 */
	protected $table;
	
	/**
	 * @var array <T:Col>
	 */
	protected $columnsObject = array();
	
	/**
	 * @var array
	 */
	protected $id = array();

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @see Entspricht des maximalen Indexwertes. Vergleichswert für jede Reihe. Eine Reihe muss mindestens $oreQuantity Daten enthalten.
	 * @var int
	 */
	protected $oreQuantity = 0;

	/**
	 * @see Entspricht der Anzahl der Namensfelder.
	 * @var int
	 */
	protected $dbQuantity = 0;

	/**
	 * @var int
	 */
	protected $oreRowCount = 0;

	/**
	 * @var int
	 */
	protected $dbRowCount = 0;

	/**
	 * @see which mode should this table executed
	 * @var string INSERTMODE|UPDATEMODE|DELETEMODE
	 */
	protected $mode = Explore::INSERTMODE;

	/**
     * @todo why clousur?
	 * @var string | clousur |  \Zend\Db\Sql\Where | null
	 */
	protected $where;

	/**
	 * default delimiter for csv-datei
	 * @var string
	 */
	protected $delimiter = '|';

	/**
	 * default enclosure
	 */
	protected $enclosure = '\\';

	/**
	 * removed the difference between import data and db data.
	 * @var bool
	 */
	protected $heel_clear = false;
	
	/**
	 * 
	 * @var ActualRow
	 */
	protected $actualRow; 
	

	/**
	 * @return the $table
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * @param string $table
	 */
	public function setTable($table) {
		$this->table = $table;
	}

	/**
	 *
	 * @param array $options
	 */
	public function __construct(ExploreManager $em, $options){

	    $this->em = $em;
		$this->actualRow = new ActualRow();
		
		$columns = $options['columns'];
		foreach ($columns as $column){
		
		    $col = new Col($this, $column);
		    if($col->getName()){
		        $this->actualRow->addColumn($col->getName(), $col->getIndex());
		    }
		    $this->columnsObject[] = $col;
		}
		
		$this->oreQuantity = max($this->actualRow->getMap())+1;
		$this->dbQuantity =  count($this->actualRow->getMap());
		
		parent::__construct($options);
	}

	/**
	 * @return array
	 */
	public function getColumns() {
		return array_keys($this->actualRow->getMap());
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @deprecated
	 * @return the $importQuantity
	 */
	public function getCsvQuantity() {
		return $this->oreQuantity;
	}
	
	/**
	 * @return the $importQuantity
	 */
	public function getOreQuantity() {
	    return $this->oreQuantity;
	}

	/**
	 * @return the $dbQuantity
	 */
	public function getDbQuantity() {
		return $this->dbQuantity;
	}

	/**
	 * @return the $where
	 */
	public function getWhere() {
		return $this->where;
	}

    /**
     * @param string | array $id
     * @throws \Exception
     */
	public function setId($id) {

	    if(is_array($id) || $id instanceof \Traversable){
	        $this->id = $id;
        }
        elseif (is_string($id)){
	        $this->id[] = $id;
        }
        else{
            throw new \Exception('Only instanceof \Traversable or string are allowed for id. '.gettype($id).' type given!');
        }
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return the $mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * @param string $mode
	 */
	public function setMode($mode) {
		$this->mode = $mode;
	}
	
	/**
	 * @return the $heel_clear
	 */
	public function getHeelCear() {
	    return $this->heel_clear;
	}
	
	/**
	 * @param boolean $clear
	 */
	public function setHeelCear($clear) {
	    $this->heel_clear = $clear;
	}

	/**
	 * @param string $where
	 */
	public function setWhere($where) {
		$this->where = $where;
	}
	/**
	 * @return the $delimiter
	 */
	public function getDelimiter() {
		return $this->delimiter;
	}

	/**
	 * @return the $enclosure
	 */
	public function getEnclosure() {
		return $this->enclosure;
	}
	/**
	 * @param number $oreRowCount
	 */
	public function setOreRowCount($count) {
		$this->oreRowCount = intval($count);
	}

	/**
	 * @param number $dbRowCount
	 */
	public function setDbRowCount($dbRowCount) {
		$this->dbRowCount = intval($dbRowCount);
	}

	/**
	 * To avoid division 0
	 * @return number
	 */
	public function getRowCount(){
		return $this->oreRowCount;
	}

	/**
	 * @return AdapterInterface
	 */
    public function getDbAdapter(){
        return $this->em->getDbAdapater();
    }
	
    /**
     * 
     * @return \ZfcExplore\ActualRow
     */
    public function getActualRow(){
        return $this->actualRow;
    }
    
    /**
     * 
     * @return array <T:Col>
     */
    public function getColumnsObject(){
        return $this->columnsObject;
    }
    
    /**
     * in Explore auslagern
     */
    public function initializeReferences(){
        
        /* @var $object \ZfcExplore\Col */
        foreach ($this->columnsObject as $object){
            if(!$object->hasReference()){
                continue;
            }
            
            $result = $this->em->getTable($object->getReference()->getTable())->select();
            $object->getReference()->setReferenceData($result);
        }
    }
    
}
