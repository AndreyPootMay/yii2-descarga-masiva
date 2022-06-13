<?php

use Da\Config\Configuration;

/*
 * --------------------------------------------------------------------------
 * Register custom Yii aliases
 * --------------------------------------------------------------------------
 *
 * As we have changed the structure. Modify default Yii aliases here.
 */
Yii::setAlias('@domainName', (YII_ENV === 'dev') ? '/yii2-descarga-masiva/public_html' : Configuration::env()->get('PUBLIC_HTML_DIR'));
Yii::setAlias('@tests', Configuration::app()->getBasePath() . DIRECTORY_SEPARATOR . '../tests');
Yii::setAlias('@root', Configuration::app()->getRootPath());
Yii::setAlias('@website', Configuration::app()->getBasePath() . DIRECTORY_SEPARATOR . '../public_html');
Yii::setAlias('@admin', Configuration::app()->getBasePath() . DIRECTORY_SEPARATOR . '../admin');
Yii::setAlias('@api', Configuration::app()->getBasePath() . DIRECTORY_SEPARATOR . '../api');
Yii::setAlias('@docs',  Configuration::app()->getBasePath() . DIRECTORY_SEPARATOR . '../docs');