<?php

use yii\rest\UrlRule;

return [
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        [
            'class' => 'yii\rest\UrlRule',
            'controller' => [
                'v1/auth',
                'v1/tests',
                'v1/descarga-masiva',
            ],
        ],
        [
            'class' => UrlRule::class,
            'controller' => 'v1/auth',
            'pluralize' => false,
            'extraPatterns' => [
                'POST login' => 'login',
            ]
        ],
        [
            'class' => UrlRule::class,
            'controller' => 'v1/descarga-masiva',
            'pluralize' => false,
            'extraPatterns' => [
                'GET index' => 'index',
            ]
        ]
    ]
];
