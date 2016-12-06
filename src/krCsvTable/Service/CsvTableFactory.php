<?php

namespace krCsvTable\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
class CsvTableFactory implements FactoryInterface{
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		
		$db = $serviceLocator->get('Zend\Db\Adapter\Adapter');
		$manager = new \krCsvTable\CsvTable\CsvTableManager();
		$manager->setDbAdapter($db);
		return $manager;
	}
}