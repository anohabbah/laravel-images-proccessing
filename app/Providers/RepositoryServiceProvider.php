<?php

namespace App\Providers;

use App\Repositories\Contracts\AssetRepositoryContract;
use App\Repositories\Eloquent\AssetRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(AssetRepositoryContract::class, AssetRepository::class);
    }
}
