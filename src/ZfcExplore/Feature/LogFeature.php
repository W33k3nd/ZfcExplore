<?php

namespace ZfcExplore\Feature;

use Zend\Db\TableGateway\Feature\AbstractFeature;
use Zend\Log\LoggerInterface;
use Zend\Log\Logger;

class LogFeature extends AbstractFeature{
	
	/**
	 * 
	 * @var Logger
	 */
	private $logger;

	
	public function __construct(LoggerInterface $logger){
		
		$this->logger = $logger;
		
	}
	
	public function failUpdate($row, $e){
		
		$this->logger->err($e->getMessage(), array('table'=>$this->tableGateway->getTable(), 'csv_values'=>$row));
	}
	
	public function failInsert($row, $e){
	
		$this->logger->err($e->getMessage(), array('table'=>$this->tableGateway->getTable(), 'csv_values'=>$row));
	}
	
	public function failDelete($row, $e){
		
		$this->logger->err($e->getMessage(), array('table'=>$this->tableGateway->getTable(), 'csv_values'=>$row));
	}
}