<?php

use Da\Config\Configuration;
use yii\console\controllers\MigrateController;

return [

    /*
    * --------------------------------------------------------------------------
    * Application
    * --------------------------------------------------------------------------
    *
    * Base class for all application classes. Here we configure the attributes
    * that do not hold any object configuration such as "components" or
    * "modules". The configuration of those properties are within submodules of
    * the same name.
    */

    'id' => 'application-id-console',

    'basePath' => Configuration::app()->getBasePath(),

    'vendorPath' => Configuration::app()->getVendorPath(),

    'runtimePath' => Configuration::app()->getRuntimePath(),

    'language' => Configuration::env()->get('APP_LANGUAGE'),

    'bootstrap' => ['log'],

    'controllerNamespace' => 'app\commands',

    'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
            'migrationPath' => [
                '@app/migrations/2021',
                '@app/migrations/2022',
            ],
        ],
    ],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
];
