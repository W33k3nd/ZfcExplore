<?php

namespace ZfcExplore\Decorator;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;
use ZfcExplore\Decorator\Methodes\MethodInterface;
class MethodPluginManager extends AbstractPluginManager{
    //zf3 version
//    protected $aliases = array(
//        'concat' => \ZfcExplore\Decorator\Methodes\Concat::class
//    );

    protected $invokableClasses = array(
        'concat' => \ZfcExplore\Decorator\Methodes\Concat::class,
        'convert' => \ZfcExplore\Decorator\Methodes\Convert::class,
        'callback' => \ZfcExplore\Decorator\Methodes\Callback::class
    );


	protected $factories = array(
		\ZfcExplore\Decorator\Methodes\Concat::class => InvokableFactory::class,
	    \ZfcExplore\Decorator\Methodes\Convert::class => InvokableFactory::class,
	    \ZfcExplore\Decorator\Methodes\Callback::class => InvokableFactory::class
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