<?php

namespace Flooris\ShopwareApi;

use Vin\ShopwareSdk\Data\Defaults as ShopwareSdkDefaults;
use Vin\ShopwareSdk\Data\AccessToken as ShopwareSdkAccessToken;
use Vin\ShopwareSdk\Client\AdminAuthenticator;
use Vin\ShopwareSdk\Data\Context as ShopwareSdkContext;
use Illuminate\Support\Facades\Cache;

class ShopwareInstance
{
    public string $cacheKeyAccessToken;

    public function __construct(
        public string              $name,
        private AdminAuthenticator $adminClient,
    )
    {
        $this->cacheKeyAccessToken = "SHOPWARE_ACCESS_TOKEN_{$this->name}";
    }

    public function getContext(string $languageId = ShopwareSdkDefaults::LANGUAGE_SYSTEM): ShopwareSdkContext
    {
        return new ShopwareSdkContext(
            apiEndpoint: $this->getHostname(),
            accessToken: $this->getAccessToken(),
            languageId: $languageId
        );
    }

    private function getHostname(): string
    {
        return $this->adminClient->getEndpoint();
    }

    public function getAccessToken(): ShopwareSdkAccessToken
    {
        $cacheKey = $this->cacheKeyAccessToken;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $accessToken = $this->adminClient->fetchAccessToken();

        $timeToLiveSecondsSubtract = 60;
        $timeToLiveSeconds         = $accessToken->expiresIn - $timeToLiveSecondsSubtract;

        Cache::set($cacheKey, $accessToken, $timeToLiveSeconds);

        return $accessToken;
    }

    public function isAccessTokenExpired(): bool
    {
        return ! Cache::has($this->cacheKeyAccessToken);
    }
}
