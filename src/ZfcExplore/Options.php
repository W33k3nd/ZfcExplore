<?php

namespace ZfcExplore;

use Zend\Db\Sql\Where;
use Zend\Stdlib\AbstractOptions;
class Options extends AbstractOptions{

	/**
	 * Turn off strict options mode
	 */
	protected $__strictMode__ = false;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var columns
	 */
	protected $columns = array();

	/**
	 * @var array
	 */
	protected $conditions = array();
	
	/**
	 * var array
	 */
	protected $methods = array();
	
    /**
     * @var array
     */
	protected $references = array();

	/**
	 * @var array
	 */
	protected $id = array();

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @see Entspricht des maximalen Indexwertes. Vergleichswert für jede CSV Spalte; Eine spalte muss mindestens $csvQuantity breit sein.
	 * @var int
	 */
	protected $importQuantity = 0;

	/**
	 * @see Entspricht der Anzahl der Namensfelder.
	 * @var int
	 */
	protected $dbQuantity = 0;

	/**
	 * @var int
	 */
	protected $csvRowCount = 0;

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
	 * @var string | Clousur | Where = null
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
	protected $trans_clean = false;
	

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
	public function __construct($options){

		$this->columns = $options['columns'];
		$this->conditions =  array_column($options['columns'], 'condition', 'index');
		$this->methods = array_column($options['columns'], 'method', 'index');
		$this->importQuantity = max(array_column($options['columns'], 'index'))+1;
		$this->dbQuantity = count(array_column($options['columns'], 'name'));
		$references = array_column($options['columns'], 'reference');
		
		foreach ($references as $reference){
		    $this->references[] = new Reference($reference);
		}
		
		parent::__construct($options);
	}

	/**
	 * @return array
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getMethods(){
	    return $this->methods;
	    
	}

    /**
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
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
	 * @return the $importQuantity
	 */
	public function getCsvQuantity() {
		return $this->importQuantity;
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
	 * @param multitype: $conditions
	 */
	public function setConditions($conditions) {
		$this->conditions = $conditions;
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
	 * @return the $transClean
	 */
	public function getTransClean() {
		return $this->trans_clean;
	}

	/**
	 * @param boolean $transClean
	 */
	public function setTransClean($transClean) {
		$this->trans_clean = $transClean;
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
	 * @param number $csvRowCount
	 */
	public function setCsvRowCount($csvRowCount) {
		$this->csvRowCount = intval($csvRowCount);
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
		return $this->csvRowCount;
	}



}
