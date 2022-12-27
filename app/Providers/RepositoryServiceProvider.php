<?php

namespace App\Providers;

use App\Repositories\Eloquent\{
    CastMemberRepositoryEloquent,
    CategoryRepositoryEloquent,
    GenreRepositoryEloquent,
    VideoRepositoryEloquent
};
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Video\Domain\Repository\VideoRepositoryInterface;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
            VideoRepositoryInterface::class,
            VideoRepositoryEloquent::class
        );

        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreRepositoryEloquent::class
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
