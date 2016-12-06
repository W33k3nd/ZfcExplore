<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
		
	'console' => array(	
	    'router' => array(
	    	'routes' => array(
	    		'csvTable' => array(
	    			'options' => array(
		    			'route' => 'csvTable [insert|update|delete]:mode [--verbose|-v]',
	    				'defaults' => array(
	    					'controller' => 'krCsvTable\CsvTableController',
	    					'action'	 => 'index'
	    				),
	    			)
	    		)
	    	)
	     )
    ),
	'service_manager' => array(
		'factories' => array(
			'CsvTableManager' => 'krCsvTable\Service\CsvTableFactory'
		)
	)
);
