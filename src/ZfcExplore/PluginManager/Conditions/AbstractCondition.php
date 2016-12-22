<?php

namespace krCsvTable\PluginManager\Conditions;

abstract class AbstractCondition implements ConditionInterface{
	
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
	 * @param array $row
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