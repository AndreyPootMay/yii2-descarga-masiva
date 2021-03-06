<?php

use Da\Config\Configuration;
use yii\web\Application;

/*
 * --------------------------------------------------------------------------
 * Create Yii api application
 * --------------------------------------------------------------------------
 *
 * Grab the configuration for the 'api' application and initialize an
 * Application instance with it. Remember that the file 'sidekit.php' has to
 * be *required* prior inserting this script so we can access the environment
 * variables.
 */

$config = Configuration::app()->build('api', Configuration::env()->get('CONFIG_USE_CACHE'));
$app = new Application($config);

/*
 * --------------------------------------------------------------------------
 * Return application
 * --------------------------------------------------------------------------
 *
 * Return instance to calling script, that way we separate initialization
 * processes and liberate the entry script with too much code.
 */
return $app;
