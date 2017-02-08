<?php

namespace ZfcExplore\Decorator;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;
use ZfcExplore\PluginManager\Methodes\MethodInterface;
class MethodPluginManager extends AbstractPluginManager{
    //zf3 version
//    protected $aliases = array(
//        'concat' => \ZfcExplore\PluginManager\Methodes\Concat::class
//    );

    protected $invokableClasses = array(
        'concat' => \ZfcExplore\PluginManager\Methodes\Concat::class
    );


	protected $factories = array(
		\ZfcExplore\PluginManager\Methodes\Concat::class => InvokableFactory::class
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