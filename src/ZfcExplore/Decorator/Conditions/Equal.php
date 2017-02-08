<?php 

namespace ZfcExplore\Decorator\Conditions;

class Equal extends AbstractCondition{
	
	/**
	 * 
	 * Compare value
	 */
	private $equal;
	
	public function __construct($options){
		
		$this->equal = $options['value'];
	}
	
	/* (non-PHPdoc)
	 * @see \krCsvTable\PluginManager\Conditions\ConditionInterface::isValid()
	 */
	public function isValid() {

		
		if($this->actualRow[$this->index] == $this->equal)
			return true;
		
		return false;
	}

	
	
}

