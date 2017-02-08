<?php

namespace ZfcExplore\Decorator;

use ZfcExplore\Decorator\Conditions\AbstractCondition;
use ZfcExplore\Decorator\Methodes\AbstractMethod;
class PluginFactory{
	
	/**
	 * 
	 * @var \ZfcExplore\Decorator\ConditionPluginManager
	 */
	public static $ConditionPlugin = null;
	
	
	/**
	 * 
	 * @var \ZfcExplore\Decorator\MethodPluginManager
	 */
	public static $MethodPlugin = null; 
	
	/**
	 * 
	 * @return \ZfcExplore\Decorator\ConditionPluginManager
	 */
	public static function getConditionPlugin(){
		
		if(static::$ConditionPlugin === null){
			static::$ConditionPlugin = new ConditionPluginManager();
		}
		
		return static::$ConditionPlugin;
	}
	
	/**
	 * 
	 * @return \ZfcExplore\Decorator\MethodPluginManager
	 */
	public static function getMethodPlugin(){
	
		if(static::$MethodPlugin === null){
			static::$MethodPlugin = new MethodPluginManager();
		}
	
		return static::$MethodPlugin;
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 * @return AbstractCondition
	 */
	public static function methodFactory($name, $options){
	    return static::getMethodPlugin()->get($name, $options);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $options
	 * @return AbstractMethod
	 */
	public static function conditionFactory($name, $options){
	    return static::getConditionPlugin()->get($name, $options);
	}
}