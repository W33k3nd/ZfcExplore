<?php
namespace ZfcExplore\Decorator\Methodes;

class Concat extends AbstractMethod{
	
	/**
	 * 
	 */
	private $indizes = array(); 
	
	/**
	 * 
	 * @var unknown
	 */
	private $placeholder = '';
	
	public function __construct($options){
		
		if(is_array($options['index']))
			$this->indizes = $options['index'];
		else 
			$this->indizes[] = $options['index'];
		
		
		if(array_key_exists('placeholder', $options))
			$this->placeholder = $options['placeholder'];
	}
	
	
	public function getValue(){
		
		$values = array($this->getActualRow()[$this->getIndex()]);

		foreach ($this->indizes as $key)
			$values[] = $this->getActualRow()[$key];
		
		return implode($this->placeholder, $values);
	}
	
}