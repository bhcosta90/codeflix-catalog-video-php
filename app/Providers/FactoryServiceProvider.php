<?php

namespace App\Providers;

use App\Factory\{CastMemberFactory, CategoryFactory};
use Core\Genre\Factory\CategoryFactoryInterface as GenreCategoryFactoryInterface;
use Core\Video\Factory\CastMemberFactoryInterface;
use Core\Video\Factory\CategoryFactoryInterface as VideoCategoryFactoryInterface;

use Illuminate\Support\ServiceProvider;

class FactoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
