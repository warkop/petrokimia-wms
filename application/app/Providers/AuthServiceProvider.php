<?php

namespace App\Providers;

use App\Http\Controllers\API\AktivitasController;
use App\Http\Models\AktivitasHarian;
use App\Http\Models\Karu;
use App\Http\Models\RencanaHarian;
use App\Http\Models\Users;
use App\Policies\AktivitasHarianPolicy;
use App\Policies\KaruPolicy;
use App\Policies\RencanaHarianPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // \Auth::viaRequest('access_token', function ($request) {
        //     Users::where('api_token', $request->access_token)->first();
        //     return true;
        // });
    }
}
