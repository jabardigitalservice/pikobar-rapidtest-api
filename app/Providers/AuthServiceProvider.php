<?php

namespace App\Providers;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\User;
use App\Policies\RdtApplicantPolicy;
use App\Policies\RdtEventPolicy;
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
        RdtApplicant::class => RdtApplicantPolicy::class,
        RdtEvent::class => RdtEventPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('notify-participants', function (User $user) {
            return $user->hasPermission('notify-participants');
        });
    }
}
