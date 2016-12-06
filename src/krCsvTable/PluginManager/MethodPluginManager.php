<?php

namespace krCsvTable\PluginManager;

use Zend\ServiceManager\AbstractPluginManager;
use krCsvTable\PluginManager\Methodes\MethodInterface;
class MethodPluginManager extends AbstractPluginManager{
	
	protected $invokableClasses = array(
		'concat' => 'krCsvTable\PluginManager\Methodes\Concat'
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
		if($plugin instanceof MethodInterface)
			return;
		
		throw new \InvalidArgumentException('Invalid MethodPlugin');
	}

	
}