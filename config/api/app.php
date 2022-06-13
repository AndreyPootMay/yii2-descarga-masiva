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

    'id' => 'api',

    'name' => 'API',

    'basePath' => Configuration::app()->getBasePath(),

    'vendorPath' => Configuration::app()->getVendorPath(),

    'runtimePath' => Configuration::app()->getRuntimePath(),

    'language' => Configuration::env()->get('APP_LANGUAGE'),

    'bootstrap' => [
        'log',
        [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
                'application/xml' => \yii\web\Response::FORMAT_XML,
            ],
        ],
    ],
];
