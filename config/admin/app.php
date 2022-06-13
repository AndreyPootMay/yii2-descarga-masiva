<?php

use Da\Config\Configuration;

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

    'id' => 'admin-app',

    'basePath' => Configuration::app()->getBasePath(),

    'vendorPath' => Configuration::app()->getVendorPath(),

    'runtimePath' => Configuration::app()->getRuntimePath(),

    'language' => Configuration::env()->get('APP_LANGUAGE'),

    'bootstrap' => ['log'],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'timeZone' => Configuration::env()->get('APP_TIMEZONE'),

    'controllerNamespace' => 'app\controllers\admin',
];
