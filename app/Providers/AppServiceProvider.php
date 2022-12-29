<?php

namespace App\Providers;

use App\Services\FileStorage;
use App\Services\VideoEventManager;
use App\Transactions\DatabaseTransaction;
use Costa\DomainPackage\UseCase\Interfaces\DatabaseTransactionInterface;
use Costa\DomainPackage\UseCase\Interfaces\FileStorageInterface;
use Illuminate\Support\ServiceProvider;
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            DatabaseTransactionInterface::class,
            DatabaseTransaction::class,
        );

        $this->app->singleton(
            FileStorageInterface::class,
            FileStorage::class,
        );

        $this->app->singleton(
            VideoEventManagerInterface::class,
            VideoEventManager::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
