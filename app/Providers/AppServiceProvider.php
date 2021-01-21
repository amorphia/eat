<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        /*
         *  Yelp API Client
         */
        $this->app->singleton('Yelp', function ( $app ) {
            $config = [
                'apiKey' => config( 'services.yelp.api_key' ),
                'apiHost' => config( 'services.yelp.host' ),
            ];
            return \Stevenmaguire\Yelp\ClientFactory::makeWith(
                $config,
                \Stevenmaguire\Yelp\Version::THREE
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
