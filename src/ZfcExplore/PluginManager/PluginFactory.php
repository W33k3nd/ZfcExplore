<?php

namespace ZfcExplore\PluginManager;

class PluginFactory{
	
	/**
	 * 
	 * @var \ZfcExplore\PluginManager\ConditionPluginManager
	 */
	public static $ConditionPlugin = null;
	
	
	/**
	 * 
	 * @var \ZfcExplore\PluginManager\MethodPluginManager
	 */
	public static $MethodPlugin = null; 
	
	/**
	 * 
	 * @return \ZfcExplore\PluginManager\ConditionPluginManager
	 */
	public static function getConditionPlugin(){
		
		if(static::$ConditionPlugin === null){
			static::$ConditionPlugin = new ConditionPluginManager();
		}
		
		return static::$ConditionPlugin;
	}
	
	/**
	 * 
	 * @return \ZfcExplore\PluginManager\MethodPluginManager
	 */
	public static function getMethodPlugin(){
	
		if(static::$MethodPlugin === null){
			static::$MethodPlugin = new MethodPluginManager();
		}
	
		return static::$MethodPlugin;
	}
}