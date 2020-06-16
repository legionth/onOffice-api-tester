<?php

namespace jr\ooapi;
use jr\ooapi\dataObjects\RequestValues;
use jr\ooapi\api\ApiRequest;
use jr\ooapi\api\ApiResponse;

/**
 * Class OnOfficeApiTester
 *
 * main-class / "business logic"
 *
 * @package jr\ooapi
 */

class OnOfficeApiTester
{
    private $credentialStorage = null;
    private $apiRequest = null;

    public function __construct(CredentialStorage $credentialStorage, ApiRequest $apiRequest)
    {
        $this->credentialStorage = $credentialStorage;
        $this->apiRequest = $apiRequest;
    }

    public function send($jsonString, $password): ApiResponse
    {
        $config = new Config();

        $this->credentialStorage->activateEncryption(new EncrypterOpenSSL($password));
        $credentials = $this->credentialStorage->load($config->getCredentialDir());

        $dataFactory = new DataFactory();
        $request = $dataFactory->createRequestFromString($jsonString);
        $requestValues = new RequestValues($credentials, $request, time());

        return $this->apiRequest->send($config->getApiUrl(), $requestValues);
    }
}