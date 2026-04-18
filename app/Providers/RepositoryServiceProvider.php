<?php

namespace App\Providers;

use App\Repositories\CltLayerRepository;
use App\Repositories\CltLayupRepository;
use App\Repositories\Interfaces\CltLayerRepositoryInterface;
use App\Repositories\Interfaces\CltLayupRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\SupplierRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(CltLayupRepositoryInterface::class, CltLayupRepository::class);
        $this->app->bind(CltLayerRepositoryInterface::class, CltLayerRepository::class);
    }
}
