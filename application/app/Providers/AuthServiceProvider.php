<?php

namespace App\Providers;

use App\Http\Models\AktivitasHarian;
use App\Http\Models\RencanaHarian;
use App\Policies\ApiAktivitasHarianPolicy;
use App\Policies\RencanaHarianPolicy;
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
        AktivitasHarian::class => ApiAktivitasHarianPolicy::class,
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
