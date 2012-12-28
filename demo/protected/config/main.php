<?php

Yii::setPathOfAlias('bootstrap',realpath(dirname(__FILE__).'/../extensions/bootstrap'));
Yii::setPathOfAlias('auth',realpath(dirname(__FILE__).'/../../..'));

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).'/..',
	'name'=>'Yii Blog Demo with Auth',
	'theme'=>'bootstrap',
	'defaultController'=>'post',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		'auth'=>array(
			'class'=>'auth.AuthModule',
			'users'=>array('demo'),
			'userNameColumn'=>'username',
			'forceCopyAssets'=>true,
		),
	),

	// application components
	'components'=>array(
		'authManager'=>array(
			'class'=>'CDbAuthManager',
			'itemTable'=>'tbl_auth_item',
			'itemChildTable'=>'tbl_auth_item_child',
			'assignmentTable'=>'tbl_auth_assignment',
			'behaviors'=>array('auth.components.AuthBehavior'),
		),
		'bootstrap'=>array(
			'class'=>'ext.bootstrap.components.Bootstrap',
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=yii_auth',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'ue-vSHUxS9DOzJA5DML1thsr5SEWqwNkyUQDENKfpCKejUXGv_IoRhXjMuG7w8iW',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'post/<id:\d+>/<title:.*?>'=>'post/view',
				'posts/<tag:.*?>'=>'post/index',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
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
	'params'=>require(dirname(__FILE__).'/params.php'),
);