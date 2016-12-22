<?php

namespace ZfcExplore\Table;

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
	protected $csvQuantity = 0;

	/**
	 * @see Entspricht der Anzahl der Namensfelder.
	 * @var unknown
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
	protected $mode = CsvTable::INSERTMODE;

	/**
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
	 * removed the difference between csvContent and dbContent in db table.
	 * @var bool
	 */
	protected $transClean = false;

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

		parent::__construct($options);
		$this->columns = $options['columns'];
		$this->conditions =  array_column($options['columns'], 'condition', 'index');
		$this->csvQuantity = max(array_column($options['columns'], 'index'))+1;
		$this->dbQuantity = count(array_column($options['columns'], 'name'));
	}

	/**
	 * @return the $columns
	 */
	public function getColumns() {
		return $this->columns;
	}

	/**
	 * @return the $conditions
	 */
	public function getConditions() {
		return $this->conditions;
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
	 * @return the $csvQuantity
	 */
	public function getCsvQuantity() {
		return $this->csvQuantity;
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
	 * @param string | array  $id
	 */
	public function setId($id) {

		is_string($id)?$this->id[] = $id:$this->id = $id;
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
		return $this->transClean;
	}

	/**
	 * @param boolean $transClean
	 */
	public function setTransClean($transClean) {
		$this->transClean = $transClean;
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
