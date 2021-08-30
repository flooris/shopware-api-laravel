<?php

namespace Flooris\ShopwareApi;

use Illuminate\Support\Collection;
use Vin\ShopwareSdk\Client\AdminAuthenticator;

class ShopwareApi
{
    protected Collection $instances;

    public function __construct()
    {
        $this->instances = collect();
    }

    public function addInstance(string $instanceName, AdminAuthenticator $adminClient): void
    {
        $this->instances->push(new ShopwareInstance($instanceName, $adminClient));
    }

    public function hasInstance(string $instanceName): bool
    {
        (bool)$this->instances->where('name', $instanceName)->first();
    }

    public function getInstance(string $instanceName): ShopwareInstance
    {
        return $this->instances->where('name', $instanceName)->firstOrFail();
    }
}
