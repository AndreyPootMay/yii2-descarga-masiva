<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;

final class DescargaMasivaController extends Controller
{
    /** @inheritdoc */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'except' => [
                'login',
                'refresh-token',
                'options',
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        dd("welcome to the index");
    }
}