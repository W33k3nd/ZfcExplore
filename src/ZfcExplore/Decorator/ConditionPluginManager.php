<?php

namespace ZfcExplore\Decorator;


use Zend\ServiceManager\AbstractPluginManager;
class ConditionPluginManager extends AbstractPluginManager{
	
	
	protected $invokableClasses = array(
		'equal' => 'krCsvTable\PluginManager\Conditions\Equal'
	);
	
	/**
	 * Whether or not to share by default
	 *
	 * @var bool
	 */
	protected $shareByDefault = false;
	
	
	/* (non-PHPdoc)
	 * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
	 */
	public function validatePlugin($plugin) {
		// TODO Auto-generated method stub
		if($plugin instanceof \ZfcExplore\PluginManager\Conditions\ConditionInterface )
				return;
		
		throw new \InvalidArgumentException(sprintf('Plugin of type %s is invalid, must implement ConditionInterface', (is_object($plugin) ? get_class($plugin) : gettype($plugin))));
	}

}