<?php

namespace App\Providers;

use App\Http\Models\AktivitasHarian;
use App\Http\Models\Gudang;
use App\Http\Models\RencanaHarian;
use App\Policies\ApiAktivitasHarianPolicy;
use App\Policies\GudangPolicy;
use App\Policies\RencanaHarianPolicy;
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
        RencanaHarian::class => RencanaHarianPolicy::class,
        AktivitasHarian::class => ApiAktivitasHarianPolicy::class,
        Gudang::class => GudangPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('main-menu', function ($user) {
            return $user->role_id === 1 || $user->role_id === 2 || $user->role_id === 6;
        });
        Gate::define('dashboard', function ($user) {
            return $user->role_id === 1 || $user->role_id === 2;
        });
        Gate::define('layout', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('gudang', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('penerimaan-gp', function ($user) {
            return $user->role_id === 1 || $user->role_id === 6;
        });
        Gate::define('log-aktivitas', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('log-aktivitas-user', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('data-master', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('data-master-user', function ($user) {
            return $user->role_id === 1;
        });
        Gate::define('report', function ($user) {
            return $user->role_id === 1 || $user->role_id === 5 || $user->role_id === 7 || $user->role_id === 3;
        });
    }
}
