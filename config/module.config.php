<?php

return array(
	'service_manager' => array(
	    'aliases' => array(
	        'ZfcExplore' => 'ZfcExplore\ExploreManager'
        ),
		'factories' => array(
			'ZfcExplore\ExploreManager' => \ZfcExplore\Service\ExploreManagerService::class
		)
	)
);
