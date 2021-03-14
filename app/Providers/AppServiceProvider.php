<?php

namespace App\Providers;


use App\Services\YelpServiceInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // Yelp API Client
        $this->app->singleton('Yelp', function ( $app ) {

            $config = [
                'apiKey' => config( 'services.yelp.api_key' ),
                'apiHost' => config( 'services.yelp.host' ),
            ];

            return \Stevenmaguire\Yelp\ClientFactory::makeWith( $config, \Stevenmaguire\Yelp\Version::THREE );
        });


        // Yelp Service Interface
        $this->app->bind( YelpServiceInterface::class, function ( $app ) {
            return new \App\Services\YelpService();
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set database string length
        Schema::defaultStringLength(191);


        /**
         *
         *  Register svg blade helper
         *
         */
        Blade::directive('svg', function( $expression ) {
            $expression = str_replace( ["'", '"'], '', $expression );
            $path = "images/svg/$expression.svg";
            return file_get_contents( $path );
            return "<?php echo file_get_contents( images\svg\{$expression}.svg ); ?>";
        });
    }
}
