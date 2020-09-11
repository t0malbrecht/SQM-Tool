<?php

namespace App\Providers;

use App\PersonalData;
use App\Policies\PersonalDataPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        'App\PersonalData' => 'App\Policies\PersonalDataPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewAny', function($user){
           return $user->isStudiendekan();
        });

        Gate::define('createAny', function($user){
            return $user->isStudiendekan();
        });

        Gate::define('deleteAny', function($user){
            return $user->isStudiendekan();
        });

        Gate::define('updateAny', function($user){
            return $user->isStudiendekan();
        });

        //
    }
}
