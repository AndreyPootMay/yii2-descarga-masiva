<?php

use Da\Config\Configuration;

return [

    /*
     * --------------------------------------------------------------------------
     * Dos Amigos User Module
     * --------------------------------------------------------------------------
     *
     * Implements User Management Module configuration
     */

    'class' => 'Da\User\Module',
    'mailParams' => [
        'fromEmail' => Configuration::env()->get('APP_ADMIN_EMAIL'),
    ],
];
