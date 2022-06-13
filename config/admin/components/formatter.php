<?php

use Da\Config\Configuration;

return [
    'class' => 'yii\i18n\Formatter',
    'dateFormat' => 'php:d/m/Y',
    'datetimeFormat' => 'php:d/m/Y H:i:s',
    'timeFormat' => 'php:H:i:s',
    'decimalSeparator' => '.',
    'thousandSeparator' => ',',
    'defaultTimeZone' => Configuration::env()->get('APP_TIMEZONE'),
];
