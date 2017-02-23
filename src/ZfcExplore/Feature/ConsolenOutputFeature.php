<?php

namespace ZfcExplore\Feature;

use Zend\Console\Adapter\AbstractAdapter;
use Zend\Db\TableGateway\Feature\AbstractFeature;
use Zend\Console\Console;
use ZfcExplore\TableMetadata;
use Zend\Console\ColorInterface;
use Zend\Db\TableGateway\AbstractTableGateway;

class ConsolenOutputFeature extends AbstractFeature{
	
	
	/**
	 * 
	 * @var AbstractAdapter
	 */
	private $console;
	/**
	 * 
	 * @var \DateTime
	 */
	private $startTime;
	
	/**
	 * 
	 * @var \DateTime
	 */
	private $zeroTime;
	
	/**
	 * file path
	 */
	private $path;
	
	/**
	 * @var CsvOptions
	 */
	private $options; 
	
	/**
	 * [time]Path(20)[found entrys][advance]
	 */
	private $out = "\r[%s] %s | Entry %6s [%3.1f%%]";
	
	
	public function __construct(){
		$this->console = Console::getInstance();
		$this->zeroTime = new \DateTime();
		$this->zeroTime->setTime(0, 0, 0);
	}
	
	public function FileNotFound(){
		$this->console->writeLine(sprintf("[%s] File not Fount: %s", $this->getTime()->format('H:i:s'), $this->path), ColorInterface::LIGHT_WHITE, ColorInterface::RED);
	}
	
	/**
	 * 
	 */
	public function preInitialize(){
		$this->startTime = time();
	}

	/**
	 * 
	 */
	public function postUpdate(){
		
		$this->consoleView();
	}
	
	/**
	 * 
	 */
	public function postInsert(){
		
		$this->consoleView();
	}
	
	/**
	 * 
	 */
	public function singular(){

		$this->consoleView();
		
	}
	
	public function finish(){
		$this->console->write("\n");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Db\TableGateway\Feature\AbstractFeature::setTableGateway()
	 */
	public function setTableGateway(AbstractTableGateway $tableGateway)
	{
		parent::setTableGateway($tableGateway);
		$this->path  = '...'.substr($tableGateway->getMetadata()->getPath(), -20);
	}
	
	/**
	 * 
	 */
	private function consoleView(){
		
		$cur = count($this->tableGateway->getOreContent());
		$rowCount = $this->tableGateway->getMetadata()->getRowCount();
		$out = sprintf($this->out, $this->getTime()->format('H:i:s'), $this->path, $rowCount, 100/($rowCount?$rowCount:1)*(($rowCount?$rowCount:1)-$cur));
		$this->console->write($out);
	}
	
	/**
	 * @todo: return processtime
	 * @return \DateTime
	 */
	private function getTime(){
		return date_timestamp_set($this->zeroTime, time()-$this->startTime);
	}
	
}