<?php

date_default_timezone_set("UTC");
mb_internal_encoding('utf8');
mb_regex_encoding('utf8');
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');SSH3dY1E

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

require 'protected/models/helpers/AppHelper.php';
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'GRAD',

    // preloading 'log' component
    'preload' => array('log'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.models.*',
        'application.actions.*',
        'application.models.behaviours.*',
        'application.models.helpers.*',
        'application.models.lexical.*',
        'application.components.*',
    ),

    'modules' => array(

    ),

//    'onBeginRequest' => array(new AppHelper(), 'logVisitor'),

    // application components
    'components' => array(

        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),

    'params' => array(
        'backupKey' => 'a05293cca4d18b49e0c8824bb1271abd',
    ),
);