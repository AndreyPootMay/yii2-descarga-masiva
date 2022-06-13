<?php

namespace api\modules\v1;

/**
 * Version 1 of the API code
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * Init the REST API
     * @return void
     */
    public function init()
    {
        parent::init();
    }
}
