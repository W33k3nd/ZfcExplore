<?php
/**
 * Created by PhpStorm.
 * User: ralf
 * Date: 02.01.17
 * Time: 11:52
 */
namespace ZfcExplore;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\AbstractTableGateway;
use ZfcExplore\PluginManager\PluginFactory;


class Explore extends AbstractTableGateway{


	//MODES
	CONST INSERTMODE = 'insert';
	CONST UPDATEMODE = 'update';
	CONST DELETEMODE = 'delete';

	//Events
	/**
	 *
	 * @deprecated
	 */
	const FILENOTFOUND = 'FileNotFound';
	const UPDATESUCCESS = 'UpdateSuccess';
	const INSERTSUCCESS = 'InsertSuccess';


	/**
	 * Standard utf-8 encoding
	 * @var string
	 */
	private $encode = "UTF-8";

	/**
	 * @var bool
	 */
	private $isCsvContent = false;

	/**
	 * @var bool
	 */
	private $isDbContent = false;

	/**
	 *
	 * @var array
	 */
	private $csvContent = array();

	/**
	 * @var array
	 */
	private $dbContent = array();

	/**
	 * @var CsvOptions
	 */
	private $option;


    /**
     * Explore constructor.
     * @param AdapterInterface $adapter
     * @param Option $option
     * @throws \Exception
     */
	public function __construct(AdapterInterface $adapter, Option $option){

		$this->option = $option;

		if(!method_exists($this, ($this->option->getMode()))){
			throw new \Exception('Options mode isn\'t callable!');
		}
		elseif ($this->option->getMode() == self::UPDATEMODE && !$this->option->getId()){
			throw new \Exception("Update Mode required id option");
		}

		$this->adapter = $adapter;
		$this->table = $this->option->getTable();
		$this->columns = array_column($this->option->getColumns(), 'name');
		parent::initialize();
	}

	/**
	 * create CsvContent
	 * The csv table content should create only on execute process.
	 */
	private function createCsvContent(){

		if($this->isCsvContent)
			return;

		if(!file_exists($this->option->getPath())){
			$this->featureSet->apply(self::FILENOTFOUND, array());
			return;
		}

		$handle = fopen($this->option->getPath(), 'r');

		while (($row = fgetcsv($handle, 0, $this->option->getDelimiter(), $this->option->getEnclosure()))){

			if(count($row) < $this->option->getCsvQuantity()){
// 				$this->getEventManager()->trigger(self::READFAIL, $this, array('row'=> $row, 'exception'=>new \Exception('Not equal Columns quantity! ('.count($row).'|'.$this->quantity.')')));
				continue;
			}
			//first, check all conditions.
			foreach ($this->option->getConditions() as $index => $condition){
				$condition = PluginFactory::getConditionPlugin()->get($condition[0], $condition[1]);
				$condition->setActualRow($row);
				$condition->setIndex($index);
				if(!$condition->isValid()){
						continue 2;
				}
			}

			$newRow = array();
			foreach ($this->option->getColumns() as $cols){

				if(array_key_exists('method', $cols)){

					if(is_callable($cols['method'])){
						$newRow[$cols['name']] =  $this->convert($cols['method']($row));
					}
					elseif(is_numeric($cols['method']) || is_string($cols['method'])){
						$newRow[$cols['name']] = $cols['method'];
					}
					else{
						$method  = PluginFactory::getMethodPlugin()->get($cols['method'][0], $cols['method'][1]);
						$method->setIndex($cols['index']);
						$method->setActualRow($row);

						$newRow[$cols['name']] = $this->convert($method->getValue());

					}
				}
				elseif(!empty($cols['name']) && array_key_exists($cols['index'], $row)){
					$newRow[$cols['name']] = $this->convert($row[$cols['index']]);
				}

			}
			if(empty($newRow))
				continue;

			$this->csvContent[] = $newRow;
		}

		fclose($handle);
		$this->option->setCsvRowCount(count($this->csvContent));
		$this->isCsvContent = true;

	}

	/**
	 * Create Db Content if necessary
	 */
	private function createDbContent(){
		//@todo: Fehleranfällig
		//$result = $this->select($this->options->getWhere());

		if($this->isDbContent)
			return ;

		$id = array_flip($this->option->getId());
		array_walk($id, function(&$item, $key, $order){
			$item = $order;
		}, 'ASC');

		$select = $this->sql->select()->columns($this->columns)->where($this->option->getWhere())->order($id);
		$result = $this->selectWith($select);
		$this->dbContent = $result->toArray();
		$this->option->setDbRowCount(count($this->dbContent));
		$this->isDbContent = true;
	}

	/**
	 * Execute specific mode for import data
	 */
	public function executeMode(){

		//Create CSV content
		$this->createCsvContent();
		//than create DB Content
		$this->createDbContent();

		$this->featureSet->apply('singular', array());
		switch ($this->getOptions()->getMode()){
			case self::UPDATEMODE:
				$this->updateData();
				break;
			case self::INSERTMODE:
				$this->insertData();
				break;
			case self::DELETEMODE:
				$this->deleteData();
				break;
		}

		if($this->option->getTransclean()){

			$temp = $this->csvContent;
			$this->csvContent = $this->dbContent;
			$this->deleteData();
			$this->csvContent = $temp;
		}
	}
	/**
	 * @see tries to insert each row if they doesn't exists
	 * @return array
	 */
	private function insertData(){

		$ids = $this->option->getId();

		//compare both tables and remove equal rows
		foreach ($this->csvContent as $csvKey => $csvRow){
			$this->featureSet->apply('singular', array());
			foreach($this->dbContent as $dbKey => $dbRow){

				foreach ($ids as $id){

					if($csvRow[$id] != $dbRow[$id])
						continue 2;
				}
				unset($this->csvContent[$csvKey]);
				unset($this->dbContent[$dbKey]);
			}
		}

		foreach ($this->csvContent as $csvKey => $row){
			try{

				$result = $this->insert($row);
				unset($this->csvContent[$csvKey]);
			}
			catch(\Exception $e){
				$this->featureSet->apply('failInsert', array($row, $e));
			}
		}

		$this->featureSet->apply('finish', array());
	}

	/**
	 * @see update each row with the equal id
	 */
	private function updateData(){

		//compare both tables and remove equal rows (UpdateMode)
		$cols = array_diff($this->columns, $this->option->getId());
		$ids = $this->option->getId();
		$notFoundInDb = array();

		foreach ($this->csvContent as $csvKey => $csvRow){
			$this->featureSet->apply('singular', array());
			foreach($this->dbContent as $dbKey => $dbRow){

				foreach ($ids as $id){

					if($csvRow[$id] != $dbRow[$id])
						continue 2;
				}

				$equal = TRUE;
				foreach ($cols as $col){
					if($csvRow[$col] != $dbRow[$col]){
						$equal = FALSE;
						break;
					}
				}
				//Remove founded db row
				unset($this->dbContent[$dbKey]);

				if($equal)
					unset($this->csvContent[$csvKey]);

				continue 2;

			}
			$notFoundInDb[] = $csvRow;
			unset($this->csvContent[$csvKey]);

		}

		$ids = array_flip($ids);
		$cols = array_flip($cols);
		foreach ($this->csvContent as $key => $row){
			try{
				$id = array_intersect_key($row, $ids);
				$col = array_intersect_key($row, $cols);
				$result = $this->update($col, $id);
				unset($this->csvContent[$key]);
			}
			catch (\Exception $e){
				$this->featureSet->apply('failUpdate', array($row, $e));
			}
		}

		foreach ($notFoundInDb as $row){
			$this->csvContent[] = $row;
		}
		$this->insertData();
	}

	/**
	 * TODO: testen
	 */
	private function deleteData(){

		$ids = $this->option->getId();
		$notFoundInDb = array();

		try{
			foreach ($this->csvContent as $key => $row){

				$this->delete($row);
				unset($this->csvContent[$key]);
			}
		}
		catch (\Exception $e){
			$this->featureSet->apply('failDelete', array($row, $e));
		}

// 		$this->featureSet->apply('finish', array());
	}

    /**
     * @return bool
     */
	public function hasReferences(){
	    return (bool) count($this->option->getReferences());
    }

	/**
	 *
	 * @param string $str
	 * @return string
	 */
	private function convert($str){

		if(mb_check_encoding($str, $this->encode))
			return $str;

		return utf8_encode($str);

	}

	/**
	 * @return array
	 */
	public function getCsvContent(){

		$this->createCsvContent();
		return $this->csvContent;
	}

	/**
	 *
	 * @return array
	 */
	public function getDbContent(){

		$this->createDbContent();
		return $this->dbContent;
	}

	/**
	 *
	 * @return Option
	 */
	public function getOptions(){

		return $this->getOption();
	}

    /**
     * @return Option
     */
	public function getOption(){
	    return $this->option;
    }
}
