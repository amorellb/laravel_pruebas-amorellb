<?php

namespace App\Providers;

use App\Models\Agenda;
use App\Models\User;
use App\Policies\AgendaPolicy;
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
        Agenda::class => AgendaPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

//        Definimos la autorización con Gate
//        Gate::define('access', function() {
//            return true;
//        });

//        Gate::define('access', function (User $user) {
//            return $user->role === 'user';
//        });
//
//        Gate::define('create', function (User $user) {
//            return $user->role === 'user';
//        });
//
//        Gate::define('update', function (User $user, Agenda $agenda) {
//            return $user->role === 'user' && $user->id === $agenda->user_id;
//        });
//
//        Gate::define('delete', function (User $user, Agenda $agenda) {
//            return $user->role === 'user' && $user->id === $agenda->user_id;
//        });

//        Gate::define('access', 'App\Policies\AgendaPolicy@view');
//        Gate::define('create', 'App\Policies\AgendaPolicy@create');
//        Gate::define('update', 'App\Policies\AgendaPolicy@update');
//        Gate::define('delete', 'App\Policies\AgendaPolicy@delete');
    }
}
