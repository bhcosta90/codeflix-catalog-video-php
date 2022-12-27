<?php

namespace App\Providers;

use App\Factory\{CategoryFactory};
use App\Repositories\Eloquent\{CastMemberRepositoryEloquent, CategoryRepositoryEloquent, GenreRepositoryEloquent};
use App\Transactions\DatabaseTransaction;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface;
use Illuminate\Support\ServiceProvider;
use Costa\DomainPackage\UseCase\Interfaces\DatabaseTransactionInterface;

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

        $this->app->singleton(
            CastMemberRepositoryInterface::class,
            CastMemberRepositoryEloquent::class
        );

        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreRepositoryEloquent::class
        );

        $this->app->singleton(
            CategoryFactoryInterface::class,
            CategoryFactory::class
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
