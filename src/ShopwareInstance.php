<?php

namespace Flooris\ShopwareApi;

use Vin\ShopwareSdk\Data\Defaults as ShopwareSdkDefaults;
use Illuminate\Support\Facades\Cache;
use Vin\ShopwareSdk\Data\AccessToken as ShopwareSdkAccessToken;
use Vin\ShopwareSdk\Client\AdminAuthenticator;
use Vin\ShopwareSdk\Data\Context as ShopwareSdkContext;

class ShopwareInstance
{
    public function __construct(
        public string              $name,
        private AdminAuthenticator $adminClient,
    )
    {
        $this->cacheKeyAccessToken = "SHOPWARE_API_TOKEN_{$name}";
    }

    public function getContext(string $languageId = ShopwareSdkDefaults::LANGUAGE_SYSTEM): ShopwareSdkContext
    {
        $accessToken = $this->adminClient->fetchAccessToken();

        return new ShopwareSdkContext(
            apiEndpoint: $this->getHostname(),
            accessToken: $accessToken,
            languageId: $languageId
        );
    }

    private function getHostname(): string
    {
        return $this->adminClient->getEndpoint();
    }

    public function getAccessToken(): ShopwareSdkAccessToken
    {
        $cacheKey = $this->getCacheKeyAccessToken();

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $accessToken = $this->adminClient->fetchAccessToken();

        $timeToLiveSecondsSubstract = 60;
        $timeToLiveSeconds          = $accessToken->expiresIn - $timeToLiveSecondsSubstract;

        Cache::set($cacheKey, $accessToken, $timeToLiveSeconds);
    }

    private function getCacheKeyAccessToken(): string
    {
        return "SHOPWARE_ACCESS_TOKEN_{$this->name}";
    }
}
