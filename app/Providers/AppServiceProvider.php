<?php

namespace App\Providers;

use App\Repositories\Eloquent\CategoryRepositoryEloquent;
use App\Transactions\DatabaseTransaction;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Shared\UseCase\Interfaces\DatabaseTransactionInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryRepositoryEloquent::class
        );

        $this->app->bind(
            DatabaseTransactionInterface::class,
            DatabaseTransaction::class,
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
