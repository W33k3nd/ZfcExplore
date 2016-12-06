<?php

namespace krCsvTable\PluginManager\Methodes;

abstract class AbstractMethod implements MethodInterface{
	
	/**
	 * Actual row
	 * @var array
	 */
	protected $actualRow;
	
	/**
	 * Actual index
	 */
	protected $index;
	
	/**
	 * 
	 * @param unknown $row
	 */
	public function setActualRow(&$row){
		$this->actualRow = $row;
	}
	
	/**
	 * 
	 * @param int | string $index
	 */
	public function setIndex($index){
		
		$this->index = $index;
	}
}