<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$db_config=array(
			"lindneo"=>array(
                        		'connectionString' => 'mysql:host=lindneo.com;dbname=panda',
                        		'emulatePrepare' => true,
                        		'username' => 'db_panda',
                        		'password' => '8rrYzKMWW8aMDudQ',
                        		'charset' => 'utf8',
                		),
			"oscar"=>array(
                        		'connectionString' => 'mysql:host=pufferfish;dbname=panda',
                        		'emulatePrepare' => true,
                        		'username' => 'oscar',
                        		'password' => 'bDczTjwzfYG9XPbE',
                        		'charset' => 'utf8',
                		)
);

$host_config=array(
                        "lindneo"=>array(
                                                'catalog_host'=>'http://catalog.okutus.com',
                                                'kerbela_host'=>'http://kerbela.lindneo.com/',
                                                'panda_host'=>'http://panda.lindneo.com',
                                                'koala_host'=>'http://koala.lindneo.com',
                                                'cloud_host'=>'http://cloud.lindneo.com'
                                        ),
                        "oscar"=>array(
                                                'catalog_host'=>'http://bigcat.okutus.com',
                                                'kerbela_host'=>'http://kerbela.okutus.com/',
                                                'panda_host'=>'http://boxoffice.okutus.com',
                                                'koala_host'=>'http://wow.okutus.com',
                                                'cloud_host'=>'http://cloud.okutus.com'
                                )
                );

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'pn14@LnDnpnd',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','*.*.*.*','::1'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				array('KerberizedService/authenticate','pattern'=>'kerberizedservice/authenticate/','verb'=>'POST'),
				//array('api/transaction', 'pattern'=>'api/transaction/<user_id:\d+>/<type:\w+>/<type_id:\w+>'),
				// array('api/transaction', 'pattern'=>'api/transaction'),
				// array('api/deneme', 'pattern'=>'api/deneme'),
				//'api/transaction/<user_id:\d+>/<type:\w+>/<type_id:\w+>'=>'api/transaction',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		// 'db'=>array(
		// 	'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		// ),
		// uncomment the following to use a MySQL database
		
		'db'=>$db_config[gethostname()],		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'catalog_host'=>$host_config[gethostname()]['catalog_host'],
                'kerbela_host'=>$host_config[gethostname()]['kerbela_host'],
                'panda_host'=>$host_config[gethostname()]['panda_host'],
                'koala_host'=>$host_config[gethostname()]['koala_host'],
                'cloud_host'=>$host_config[gethostname()]['cloud_host'],

	),
);
