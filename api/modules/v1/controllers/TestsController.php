<?php

namespace api\modules\v1\controllers;

use Faker\Factory;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;

final class TestsController extends Controller
{
    /** @inheritdoc */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $faker = Factory::create();

        $people = [];
        for ($i = 0; $i < 10; $i++) {
            $people[] = [
                'fullName' => "{$faker->firstName} {$faker->lastName}",
                'number' => $faker->buildingNumber(),
                'address' => $faker->address(),
                'password' => $faker->password(5, 11)
            ];
        }

        return $people;
    }
}
