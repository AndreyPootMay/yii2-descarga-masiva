<?php

declare(strict_types=1);

namespace api\modules\v1\helpers;

use Exception;
use PhpCfdi\Rfc\Rfc;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\Fiel;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\FielRequestBuilder;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\Services\Download\DownloadResult;
use PhpCfdi\SatWsDescargaMasiva\Shared\ServiceEndpoints;
use PhpCfdi\SatWsDescargaMasiva\WebClient\GuzzleWebClient;
use Yii;

final class SatWsServiceHelper
{
    public function createService(string $rfc, string $password, bool $retenciones)
    {
        $rfc = Rfc::parse($rfc)->getRfc();

        $contentCer = file_get_contents($this->obtainCertificatePath($rfc));
        $contentKey = file_get_contents($this->obtainPrivateKeyPath($rfc));

        $fiel = $this->createFiel($contentCer, $contentKey, $password);

        $webClient = new GuzzleWebClient();
        $requestBuilder = new FielRequestBuilder($fiel);
        $endpoints = ! $retenciones ? ServiceEndpoints::cfdi() : ServiceEndpoints::retenciones();

        return new Service($requestBuilder, $webClient, null, $endpoints);
    }

    public function createFiel(string $contentCer, string $contentKey, string $password): Fiel
    {
        $fiel = Fiel::create($contentCer, $contentKey, $password);

        if (!$fiel->isValid()) {
            throw new Exception('La FIEL no es valida');
        }

        return $fiel;
    }

    public function checkPath(string $rfc): void
    {
        if (!file_exists(Yii::getAlias('@docs') . '/certificates/' . $rfc)) {
            mkdir(Yii::getAlias('@docs') . '/certificates/' . $rfc);
        }
    }

    public function obtainCertificatePath(string $rfc): string
    {
        return Yii::getAlias('@docs') . '/certificates/' . $rfc . '/' . $rfc . '.cer';
    }

    public function obtainPrivateKeyPath(string $rfc): string
    {
        return Yii::getAlias('@docs') . '/certificates/' . $rfc . '/' . $rfc . '.key';
    }

    public function obtainPackagePath(string $rfc, string $packageId): string
    {
        if ($packageId !== '') {
            $packageId .= '.zip';
        }

        if (!file_exists(Yii::getAlias('@docs/') . $rfc . '/packages/' . $packageId)) {
            mkdir(Yii::getAlias('@docs/') . $rfc . '/packages/' . $packageId);
        }

        return Yii::getAlias('@docs/') . $rfc . '/packages/' . $packageId;
    }

    public function storePackage(string $rfc, string $packageId, DownloadResult $package): void
    {
        $path = $this->obtainPackagePath($rfc, $packageId);

        file_put_contents($path, $package->getPackageContent());
    }
}
