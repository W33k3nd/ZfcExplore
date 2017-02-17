<?php
/**
 * User: ralf
 * Date: 02.01.17
 */
namespace ZfcExplore;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;


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
	 * 
	 * @var ExploreManager;
	 */
	private $exploremanager;
	
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
	 * @var ResultSet
	 */
	private $dbContent = array();

	/**
	 * @var TableMetadata
	 */
	private $metaData;
	
	/**
	 * 
	 * @var ActualRow
	 */
	private $actualRow;

    /**
     * Explore constructor.
     * @param ExploreManager $em
     * @param TableMetadata $option
     * @throws \Exception
     */
	public function __construct(TableMetadata $metadata){

		$this->metaData = $metadata;

		if(!method_exists($this, ($this->metaData->getMode()))){
			throw new \Exception('Options mode isn\'t callable!');
		}
		elseif ($this->metaData->getMode() == self::UPDATEMODE && !$this->metaData->getId()){
			throw new \Exception("Update Mode required id option");
		}

		$this->adapter = $this->metaData->getDbAdapter();
		$this->table = $this->metaData->getTable();
		$this->actualRow = $this->metaData->getActualRow();
		$this->columns = $this->metaData->getColumns();
		    
		parent::initialize();
	}

	/**
	 * create OreContent
	 * The table content should create only on execute process.
	 */
	private function createOreContent(){

		if($this->isOreContent)
			return;

		if(!file_exists($this->metaData->getPath())){
			$this->featureSet->apply(self::FILENOTFOUND, array());
			return;
		}

		$handle = fopen($this->metaData->getPath(), 'r');
        $this->metaData->initializeReferences();
		
		while (($row = fgetcsv($handle, 0, $this->metaData->getDelimiter(), $this->metaData->getEnclosure()))){

			$this->actualRow->setActualRow($row);
			
			if($this->actualRow->count() < $this->metaData->getOreQuantity()){
// 				$this->getEventManager()->trigger(self::READFAIL, $this, array('row'=> $row, 'exception'=>new \Exception('Not equal Columns quantity! ('.count($row).'|'.$this->quantity.')')));
				continue;
			}
			/* @var $object \ZfcExplore\Col */
			foreach ($this->metaData->getColumnsObject() as $object){
			    
			    if(!$object->validCondition()){
			        continue 2;
			    }
			}
			
			foreach ($this->metaData->getColumnsObject() as $object){
			    $object->result();
			}

			$this->oreContent[] = $this->actualRow->getColumnData();
		}
		

		fclose($handle);
		$this->metaData->setOreRowCount(count($this->oreContent));
		$this->isOreContent = true;

	}
	
	/**
	 * Create Db Content
	 */
	private function createDbContent(){
	
	    if($this->isDbContent)
	        return ;
	
	    $id = array_flip($this->metaData->getId());
	    array_walk($id, function(&$item, $key, $order){
	        $item = $order;
	    }, 'ASC');
	
	        $select = $this->sql->select();
	        $select->columns($this->columns);
	
	        if($this->metaData->getWhere()){
	            $select->where($this->metaData->getWhere());
	        }
	        $select->order($id);
	        $this->dbContent = $this->selectWith($select);
	        //TODO: überprüfe ob die db Daten gebuffert werden sollen!
	        $this->dbContent->buffer();
	        $this->metaData->setDbRowCount($this->dbContent->count());
	        $this->isDbContent = $this->dbContent->valid();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Db\TableGateway\AbstractTableGateway::select()
	 * @return ResultSet
	 */
	public function select($where = null){
	    
	    if(!$this->isDone()){
	        
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
	    }
	    return $this->getDbContent();
	}

	/**
	 * Execute specific mode for import data
	 */
	public function executeMode(){

		//Create file content
		$this->createOreContent();
		//than create database content
		$this->createDbContent();

		$this->featureSet->apply('singular', array());
		switch ($this->metaData->getMode()){
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

		$this->dbContent->rewind();
		if($this->metaData->getHeelCear()){

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
	    
	    if($this->metaData->getMode() == self::INSERTMODE &&
	       !$this->done){
	        
	        $this->createOreContent();
	        $this->createDbContent();
    	    $this->insertData();
    	    
    	    if($this->metaData->getHeelCear()){
    	    
    	        $temp = $this->oreContent;
    	        $this->oreContent = $this->dbContent;
    	        $this->deleteData();
    	        $this->oreContent = $temp;
    	    }
	    }
	}
	/**
	 * @see tries to insert each row if they doesn't exists
	 * @return array
	 */
	private function insertData(){

		$ids = $this->metaData->getId();

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
	    
	    if($this->metaData->getMode() == self::UPDATEMODE &&
	        !$this->done){
	         
	        $this->createOreContent();
	        $this->createDbContent();
	        $this->updateData();
	        
	        if($this->metaData->getHeelCear()){
	        
	            $temp = $this->oreContent;
	            $this->oreContent = $this->dbContent;
	            $this->deleteData();
	            $this->oreContent = $temp;
	        }
	    }
	    
	}
	/**
	 * @see update each row with the equal id
	 */
	private function updateData(){

		//compare both tables and remove equal rows (UpdateMode)
		$cols = array_diff($this->columns, $this->metaData->getId());
		$ids = $this->metaData->getId();
		$notFoundInDb = array();

		$data = $this->dbContent->toArray();
		
		foreach ($this->oreContent as $oreKey => $oreRow){
			$this->featureSet->apply('singular', array());
			foreach($data as $dbKey => $dbRow){

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
				unset($data[$dbKey]);

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
	    
	    if($this->metaData->getMode() == self::DELETEMODE &&
	        !$this->done){
	    
	        $this->createOreContent();
	        $this->createDbContent();
	        $this->deleteData();
	    }
	}
	/**
	 * TODO: testen
	 */
	private function deleteData(){

		$ids = $this->metaData->getId();
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

// 	/**
// 	 * @deprecated
// 	 * @param array $columns
// 	 */
// 	public function setColumns($columns){
	    
// 	    foreach ($columns as $column){
// 	        if(!isset($column['name']) || empty($column['name'])){
// 	            continue;
//              }
//              $this->addColumn($column['name'], $column);
// 	    }
	    
// 	}
	
// 	/**
// 	 * @deprecated
// 	 * @param string $name
// 	 * @param array $option
// 	 */
// 	public function addColumn($name = null, $option){
	    
// 	    if($name !== null){
//     	    $option['name'] = $name;
	        
//     	    if(!in_array($name,  $this->columns)){
//     	        $this->columns[] = $name;
//     	        $this->actualRow->setColumns($this->columns);
//     	    }
// 	    }
        
// 	    $col = new Col($option);
// 	    $col->setExplore($this);
	    
// 	    if(isset($option['reference'])){
// 	        $reference = new Reference($this->exploremanager->getTables(), $option['reference']);
// 	    }
	    
//         $this->columnsObjects[] = $col;
// 	}

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
     * @return Option
     */
	public function getMetadata(){
	    return $this->metaData;
    }
    
    /**
     * @return boolean
     */
    public function isDone(){
        return $this->isDbContent && $this->isOreContent;
    }
}
