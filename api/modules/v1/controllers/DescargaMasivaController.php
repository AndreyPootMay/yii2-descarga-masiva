<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\helpers\SatWsServiceHelper;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\rest\Controller;
use yii\web\UploadedFile;

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

    public function actionSendCerKey()
    {
        $cer = UploadedFile::getInstanceByName('cer');
        $key = UploadedFile::getInstanceByName('key');

        $cerFileName = $cer->name;
        $keyFileName = $key->name;

        $satWsService = new SatWsServiceHelper();
        try {
            $fiel = $satWsService->createFiel(
                file_get_contents($cer->tempName),
                file_get_contents($key->tempName),
                Yii::$app->request->post('password')
            );
        } catch (\Throwable $exception) {
            return $this->asJson([
                'message' => 'Certificado, llave privada o contraseña inválida',
                'code' => $exception->getMessage(),
            ]);
        }

        $rfc = $fiel->getRfc();
        $satWsService->checkPath($rfc);
        $certificatePath = $satWsService->obtainCertificatePath($rfc);
        $privateKeyPath = $satWsService->obtainPrivateKeyPath($rfc);

        $cer->saveAs($certificatePath);
        $key->saveAs($privateKeyPath);

        return $this->asJson([
            'pathCer' => $certificatePath,
            'pathKey' => $privateKeyPath,
        ]);
    }
}