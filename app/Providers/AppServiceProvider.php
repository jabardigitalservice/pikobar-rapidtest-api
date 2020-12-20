<?php

namespace App\Providers;

use App\Entities\RdtInvitation;
use App\Observers\RdtApplicantObserver;
use App\Observers\RdtEventObserver;
use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Observers\RdtInvitationObserver;
use AsyncAws\Core\AwsClientFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $proxyUrl    = config('proxy.url');
        $proxyScheme = config('proxy.scheme');

        if (!empty($proxyUrl)) {
            URL::forceRootUrl($proxyUrl);
        }

        if (!empty($proxyScheme)) {
            URL::forceScheme($proxyScheme);
        }

        RdtEvent::observe(RdtEventObserver::class);
        RdtApplicant::observe(RdtApplicantObserver::class);
        RdtInvitation::observe(RdtInvitationObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('aws', function ($app) {
            return new AwsClientFactory([
                'region'            => config('aws.region'),
                'accessKeyId'       => config('aws.key'),
                'accessKeySecret'   => config('aws.secret')
            ]);
        });
    }
}
