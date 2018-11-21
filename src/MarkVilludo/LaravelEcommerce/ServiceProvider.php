<?php 

namespace MarkVilludo\LaravelEcommerce;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    
        //publish views
       $this->publishes([
           __DIR__.'/../views' => resource_path('/views'),
        ],'views');

        // Publish the migration
        $this->publishes([
           __DIR__.'/../migration' => $this->app->databasePath().'/migrations/',
        ],'views');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
     
    }

}
