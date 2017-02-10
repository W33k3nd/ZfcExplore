<?php
/**
 * User: ralf
 * Date: 02.01.17
 */
namespace ZfcExplore;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\AbstractTableGateway;


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
	 * @var bool
	 */
	private $isOreContent = false;

	/**
	 * @var bool
	 */
	private $isDbContent = false;

	/**
	 * @var array
	 */
	private $oreContent = array();

	/**
	 * @var array
	 */
	private $dbContent = array();

	/**
	 * @var Options
	 */
	private $option;
	
	/**
	 * List of all given columns.
	 * @var array<Col>
	 */
	private $columnsObjects = array();
	
	/**
	 * 
	 * @var ActualRow
	 */
	private $actualRow;
	
	/**
	 * Wurde dieser Explore schon ausgeführt?
	 * @var bool
	 */
	private $done = FALSE;


    /**
     * Explore constructor.
     * @param AdapterInterface $adapter
     * @param Options $option
     * @throws \Exception
     */
	public function __construct(AdapterInterface $adapter, Options $option){

		$this->option = $option;

		if(!method_exists($this, ($this->option->getMode()))){
			throw new \Exception('Options mode isn\'t callable!');
		}
		elseif ($this->option->getMode() == self::UPDATEMODE && !$this->option->getId()){
			throw new \Exception("Update Mode required id option");
		}

		$this->adapter = $adapter;
		$this->actualRow = new ActualRow();
		$this->table = $this->option->getTable();
		$this->setColumns($this->option->getColumns());
		    
		parent::initialize();
	}

	/**
	 * create OreContent
	 * The table content should create only on execute process.
	 */
	private function createOreContent(){

		if($this->isOreContent)
			return;

		if(!file_exists($this->option->getPath())){
			$this->featureSet->apply(self::FILENOTFOUND, array());
			return;
		}

		$handle = fopen($this->option->getPath(), 'r');

		while (($row = fgetcsv($handle, 0, $this->option->getDelimiter(), $this->option->getEnclosure()))){

			$this->actualRow->setActualRow($row);
			
			if($this->actualRow->count() < $this->option->getOreQuantity()){
// 				$this->getEventManager()->trigger(self::READFAIL, $this, array('row'=> $row, 'exception'=>new \Exception('Not equal Columns quantity! ('.count($row).'|'.$this->quantity.')')));
				continue;
			}
			
			foreach ($this->columnsObjects as $object){
			    
			    if(!$object->validCondition()){
			        continue 2;
			    }
			}
			
			foreach ($this->columnsObjects as $object){
			    $object->result();
			}

			$this->oreContent[] = $this->actualRow->getColumnsData();
		}

		fclose($handle);
		$this->option->setOreRowCount(count($this->oreContent));
		$this->isOreContent = true;

	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Db\TableGateway\AbstractTableGateway::select()
	 */
	public function select($where = null){
	    
	    $this->createDbContent();
	    return $this->getDbContent();
	}

	/**
	 * Create Db Content
	 */
	private function createDbContent(){

		if($this->isDbContent)
			return ;

		$id = array_flip($this->option->getId());
		array_walk($id, function(&$item, $key, $order){
			$item = $order;
		}, 'ASC');

		$select = $this->sql->select();
		$select->columns($this->columns);
		
		if($this->option->getWhere()){
		    $select->where($this->option->getWhere());
		}
    	$select->order($id);
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
		$this->createOreContent();
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

			$temp = $this->oreContent;
			$this->oreContent = $this->dbContent;
			$this->deleteData();
			$this->oreContent = $temp;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Db\TableGateway\AbstractTableGateway::insert()
	 */
	public function insert($set){
	    
	    if($this->option->getMode() == self::INSERTMODE &&
	       !$this->done){
	        
	        $this->createOreContent();
	        $this->createDbContent();
    	    $this->insertData();
    	    
    	    if($this->option->getHeelCear()){
    	    
    	        $temp = $this->oreContent;
    	        $this->oreContent = $this->dbContent;
    	        $this->deleteData();
    	        $this->oreContent = $temp;
    	    }
    	    
    	    $this->done = true;
	    }
	}
	/**
	 * @see tries to insert each row if they doesn't exists
	 * @return array
	 */
	private function insertData(){

		$ids = $this->option->getId();

		//compare both tables and remove equal rows
		foreach ($this->oreContent as $oreKey => $oreRow){
			$this->featureSet->apply('singular', array());
			foreach($this->dbContent as $dbKey => $dbRow){

				foreach ($ids as $id){

					if($oreRow[$id] != $dbRow[$id])
						continue 2;
				}
				unset($this->oreContent[$oreKey]);
				unset($this->dbContent[$dbKey]);
			}
		}

		foreach ($this->oreContent as $oreKey => $row){
			try{

				$result = parent::insert($row);
				unset($this->oreContent[$oreKey]);
			}
			catch(\Exception $e){
				$this->featureSet->apply('failInsert', array($row, $e));
			}
		}

		$this->featureSet->apply('finish', array());
	}

	/**
	 * (non-PHPdoc)
	 * @see \Zend\Db\TableGateway\AbstractTableGateway::update()
	 */
	public function update($set, $where = null, array $joins = null){
	    
	    if($this->option->getMode() == self::UPDATEMODE &&
	        !$this->done){
	         
	        $this->createOreContent();
	        $this->createDbContent();
	        $this->updateData();
	        
	        if($this->option->getHeelCear()){
	        
	            $temp = $this->oreContent;
	            $this->oreContent = $this->dbContent;
	            $this->deleteData();
	            $this->oreContent = $temp;
	        }
	        
	        $this->done = true;
	    }
	    
	}
	/**
	 * @see update each row with the equal id
	 */
	private function updateData(){

		//compare both tables and remove equal rows (UpdateMode)
		$cols = array_diff($this->columns, $this->option->getId());
		$ids = $this->option->getId();
		$notFoundInDb = array();

		foreach ($this->oreContent as $oreKey => $oreRow){
			$this->featureSet->apply('singular', array());
			foreach($this->dbContent as $dbKey => $dbRow){

				foreach ($ids as $id){

					if($oreRow[$id] != $dbRow[$id])
						continue 2;
				}

				$equal = TRUE;
				foreach ($cols as $col){
					if($oreRow[$col] != $dbRow[$col]){
						$equal = FALSE;
						break;
					}
				}
				//Remove founded db row
				unset($this->dbContent[$dbKey]);

				if($equal)
					unset($this->oreContent[$oreKey]);

				continue 2;

			}
			$notFoundInDb[] = $oreRow;
			unset($this->oreContent[$oreKey]);

		}

		$ids = array_flip($ids);
		$cols = array_flip($cols);
		foreach ($this->oreContent as $key => $row){
			try{
				$id = array_intersect_key($row, $ids);
				$col = array_intersect_key($row, $cols);
				$result = parent::update($col, $id);
				unset($this->oreContent[$key]);
			}
			catch (\Exception $e){
				$this->featureSet->apply('failUpdate', array($row, $e));
			}
		}

		foreach ($notFoundInDb as $row){
			$this->oreContent[] = $row;
		}
		$this->insertData();
	}

	public function delete($where){
	    
	    if($this->option->getMode() == self::DELETEMODE &&
	        !$this->done){
	    
	        $this->createOreContent();
	        $this->createDbContent();
	        $this->deleteData();
	        $this->done = true;
	    }
	}
	/**
	 * TODO: testen
	 */
	private function deleteData(){

		$ids = $this->option->getId();
		$notFoundInDb = array();

		try{
			foreach ($this->oreContent as $key => $row){

				$this->delete($row);
				unset($this->oreContent[$key]);
			}
		}
		catch (\Exception $e){
			$this->featureSet->apply('failDelete', array($row, $e));
		}

// 		$this->featureSet->apply('finish', array());
	}

	/**
	 * 
	 * @param array $columns
	 */
	public function setColumns($columns){
	    
	    foreach ($columns as $column){
	        if(!isset($column['name']) || empty($column['name'])){
	            continue;
             }
             $this->addColumn($column['name'], $column);
	    }
	    
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 */
	public function addColumn($name = null, $option){
	    
	    if($name != null){
    	    $option['name'] = $name;
	        
    	    if(!in_array($name,  $this->columns)){
    	        $this->columns[] = $name;
    	        $this->actualRow->setColumns($this->columns);
    	    }
	    }
        
	    $col = new Col($option);
	    $col->setExplore($this);
        $this->columnsObjects[] = $col;
	}

    /**
     * @return array
     */
    public function getReferences(){
	    return $this->option->getReferences();
    }
    
    /**
     * 
     * @return array
     */
    public function getActualRow(){
        return $this->actualRow;
    }

	/**
	 * @return array
	 */
	public function getOreContent(){

		$this->createOreContent();
		return $this->oreContent;
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
    
    /**
     * @return boolean
     */
    public function isDone(){
        return $this->done;
    }
}
