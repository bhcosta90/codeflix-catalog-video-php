<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-catalog', function () {
            if (!empty(auth()->token())) {
                $token = json_decode(auth()->token(), true);
                $realmAccess = $token['realm_access'];
                $roles = $realmAccess['roles'];
                return (bool) in_array('admin-catalog', $roles);
            }
            return false;
        });
    }
}
