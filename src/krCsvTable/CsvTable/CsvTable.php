<?php

namespace krCsvTable\CsvTable;

use Zend\Db\TableGateway\AbstractTableGateway;
use krCsvTable\PluginManager\PluginFactory;


class CsvTable extends AbstractTableGateway{
	
	
	//MODES
	CONST INSERTMODE = 'insertCSV';
	CONST UPDATEMODE = 'updateCSV';
	CONST DELETEMODE = 'deleteCSV';
	
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
	private $options;
	
	/**
	 * 
	 * @param Zend\Db\Adapter\AdapterInterface
	 * @param array $option
	 */	
	public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter, CsvOptions $options){
		
		$this->options = $options;
		
		if(!method_exists($this, ($this->options->getMode()))){
			throw new \Exception('Options mode isn\'t callable!');
		}
		elseif ($this->options->getMode() == self::UPDATEMODE && !$this->options->getId()){
			throw new \Exception("Update Mode necessary id option");
		}
		
		$this->adapter = $adapter;
		$this->table = $this->options->getTable();
		$this->columns = array_column($this->options->getColumns(), 'name');
		parent::initialize();
		
	}
	
	/**
	 * create CsvContent
	 * The csv table content should create only on execute process. 
	 */
	private function createCsvContent(){
		
		if($this->isCsvContent)
			return;
		
		if(!file_exists($this->options->getPath())){
			$this->featureSet->apply(self::FILENOTFOUND, array());
			return;
		}
		
		$handle = fopen($this->options->getPath(), 'r');

		while (($row = fgetcsv($handle, 0, $this->options->getDelimiter(), $this->options->getEnclosure()))){
			
			if(count($row) < $this->options->getCsvQuantity()){
// 				$this->getEventManager()->trigger(self::READFAIL, $this, array('row'=> $row, 'exception'=>new \Exception('Not equal Columns quantity! ('.count($row).'|'.$this->quantity.')')));
				continue;
			}
			//first, check all conditions. 
			foreach ($this->options->getConditions() as $index => $condition){
				$condition = PluginFactory::getConditionPlugin()->get($condition[0], $condition[1]);
				$condition->setActualRow($row);
				$condition->setIndex($index);
				if(!$condition->isValid()){
						continue 2;
				}
			}
			
			$newRow = array();
			foreach ($this->options->getColumns() as $cols){
				
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
		$this->options->setCsvRowCount(count($this->csvContent));
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
		
		$id = array_flip($this->options->getId());
		array_walk($id, function(&$item, $key, $order){
			$item = $order;
		}, 'ASC');
		
		$select = $this->sql->select()->columns($this->columns)->where($this->options->getWhere())->order($id);
		$result = $this->selectWith($select);
		$this->dbContent = $result->toArray();
		$this->options->setDbRowCount(count($this->dbContent));
		$this->isDbContent = true;
	}
	
	/**
	 * Execute specific mode for csv Content
	 */
	public function executeMode(){
		
		//Create CSV content
		$this->createCsvContent();
		//than create DB Content
		$this->createDbContent();

		$this->featureSet->apply('singular', array());
		switch ($this->getOptions()->getMode()){
			case CsvTable::UPDATEMODE:
				$this->updateCSV();
				break;
			case CsvTable::INSERTMODE:
				$this->insertCsv();
				break;
			case CsvTable::DELETEMODE:
				$this->deleteCsv();
				break;
		}
		
		if($this->options->getTransclean()){
			
			$temp = $this->csvContent;
			$this->csvContent = $this->dbContent;
			$this->deleteCSV();
			$this->csvContent = $temp;
		}
	}
	/**
	 * @see tries to insert each row if they doesn't exists
	 * @return array
	 */
	private function insertCSV(){
		
		$ids = $this->options->getId();
		
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
	private function updateCSV(){
		
		//compare both tables and remove equal rows (UpdateMode)
		$cols = array_diff($this->columns, $this->options->getId());
		$ids = $this->options->getId();
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
		$this->insertCSV();
	}
	
	/**
	 * TODO: testen
	 */
	private function deleteCSV(){
		
		$ids = $this->options->getId();
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
	 * @return CsvOptions
	 */
	public function getOptions(){
		
		return $this->options;
	}
}