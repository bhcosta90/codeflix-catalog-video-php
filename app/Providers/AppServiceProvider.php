<?php

namespace App\Providers;

use App\Factory\{CastMemberFactory, CategoryFactory};
use App\Repositories\Eloquent\{CastMemberRepositoryEloquent, CategoryRepositoryEloquent, GenreRepositoryEloquent};
use App\Transactions\DatabaseTransaction;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface as GenreCategoryFactoryInterface;
use Core\Video\Factory\CastMemberFactoryInterface;
use Core\Video\Factory\CategoryFactoryInterface as VideoCategoryFactoryInterface;
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
            GenreCategoryFactoryInterface::class,
            CategoryFactory::class
        );

        $this->app->singleton(
            VideoCategoryFactoryInterface::class,
            CategoryFactory::class
        );

        $this->app->singleton(
            CastMemberFactoryInterface::class,
            CastMemberFactory::class
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
