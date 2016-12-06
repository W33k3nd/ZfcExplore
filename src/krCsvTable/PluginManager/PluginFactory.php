<?php

namespace krCsvTable\PluginManager;

class PluginFactory{
	
	/**
	 * 
	 * @var \krCsvTable\PluginManager\ConditionPluginManager
	 */
	public static $ConditionPlugin = null;
	
	
	/**
	 * 
	 * @var \krCsvTable\PluginManager\MethodPluginManager
	 */
	public static $MethodPlugin = null; 
	
	/**
	 * 
	 * @return \krCsvTable\PluginManager\ConditionPluginManager
	 */
	public static function getConditionPlugin(){
		
		if(static::$ConditionPlugin === null){
			static ::$ConditionPlugin = new ConditionPluginManager();
		}
		
		return static::$ConditionPlugin;
	}
	
	/**
	 * 
	 * @return \krCsvTable\PluginManager\MethodPluginManager
	 */
	public static function getMethodPlugin(){
	
		if(static::$MethodPlugin === null){
			static ::$MethodPlugin = new MethodPluginManager();
		}
	
		return static::$MethodPlugin;
	}
}