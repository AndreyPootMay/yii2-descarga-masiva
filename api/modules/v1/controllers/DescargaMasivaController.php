<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\helpers\SatWsServiceHelper;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\Services\Download\DownloadResult;
use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTimePeriod;
use PhpCfdi\SatWsDescargaMasiva\Shared\DownloadType;
use PhpCfdi\SatWsDescargaMasiva\Shared\RequestType;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\rest\Controller;
use yii\web\UploadedFile;

class DescargaMasivaController extends Controller
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

    public function actionSendCerKey()
    {
        $cer = UploadedFile::getInstanceByName('cer');
        $key = UploadedFile::getInstanceByName('key');

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

    public function actionMakeQuery()
    {
        try {
            $post = Yii::$app->request->post();

            $period = DateTimePeriod::createFromValues(
                $post['period']['start'],
                $post['period']['end']
            );

            $downloadType = $post['downloadType'] === 'issued'
                ? DownloadType::issued() : DownloadType::received();
            $requestType = $post['requestType'] === 'xml'
                ? RequestType::cfdi() : RequestType::metadata();

            $queryParameters = QueryParameters::create(
                $period,
                $downloadType,
                $requestType,
                $post['rfcMatch']
            );
            $satWsServiceHelper = new SatWsServiceHelper();
            $service = $satWsServiceHelper->createService(
                $post['rfc'],
                $post['password'],
                (bool) $post['retenciones']
            );

            $query = $service->query($queryParameters);

            if (!$query->getStatus()->isAccepted()) {
                return $this->asJson($query->getStatus(), 400);
            }

            return $this->asJson([
                $query->getStatus(), 'requestId' => $query->getRequestId()
            ], 200);
        } catch (\Exception $exception) {
            return $this->asJson(['message' => $exception->getMessage()], 422);
        }
    }

    public function actionDownloadPackages()
    {
        $postFields = Yii::$app->request->post();
        $packagesIds = $postFields['packagesIds'];
        $rfc = $postFields['rfc'];
        $satWsServiceHelper = new SatWsServiceHelper();

        $service = $satWsServiceHelper->createService(
            $postFields['rfc'],
            $postFields['password'],
            $postFields['retenciones']
        );


        $messages = [];
        $errorMessages = [];
        foreach ($packagesIds as $packageId) {
            $download = $this->download($service, $packageId);
            if (!$download->getStatus()->isAccepted()) {
                $errorMessages[] = sprintf(
                    'El paquete %s no se ha podido descargar: %s',
                    $packageId,
                    $download->getStatus()->getMessage()
                );
                continue;
            }
            $satWsServiceHelper->storePackage($rfc, $packageId, $download);
            $messages[] = "El paquete {$packageId} se ha almacenado";
        }
        return $this->asJson(['errorMessages' => $errorMessages, 'messages' => $messages]);
    }

    /**
     * @param Service $service
     * @param string $packageId
     *
     * @return DownloadResult
     */
    protected function download(Service $service, string $packageId): DownloadResult
    {
        return $service->download($packageId);
    }
}
