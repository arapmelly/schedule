<?php

return array(

    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'cru9',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),

    // application components
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=schedule',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'enableProfiling' => true
        ),

        'log' => array(
            'routes' => array(
                array(
                    'class' => 'application.extensions.yii-debug-toolbar.YiiDebugToolbarRoute',
                    'ipFilters' => array('127.0.0.1'),
                ),
            ),
        ),
    ),

    'params' => array(
        'adminEmail' => 'webmaster@example.com',
        'backupPath' => '/tmp/backup.gz',
        'restorePath' => '/tmp/backup.gz',
        'backupServers' => array(
//            array(
//                'username' => 'p68290',
//                'password' => '3Ye7JDQqkE',
//                'host' => 'p68290.ftp.ihc.ru',
//                'backupFolder' => '/data'
//            ),
            array(
                'username' => 'kobylin_ftp',
                'password' => 'qYtWLxVL',
                'host' => 'kobylin.ftp.ukraine.com.ua',
                'backupFolder' => '/grad/backup'
            )
        )
    ),
);