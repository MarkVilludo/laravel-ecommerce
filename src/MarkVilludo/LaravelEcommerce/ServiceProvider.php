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
        
        // Publish the migration
        include __DIR__.'/routes/api_ecommerce.php';
        include __DIR__.'/routes/web_ecommerce.php';

        //publish views
        $this->publishes([
           __DIR__.'/views' => resource_path('/views'),
        ],'views');

        // Publish the migration
        $this->publishes([
           __DIR__.'/migrations' => $this->app->databasePath().'/migrations/',
        ],'views');

        //publish also assets in public folder for the css and js plugins
        $this->publishes([
           __DIR__.'/public' => public_path('/assets'),
        ],'assets');
        //end

        //publish also controllers
        $this->publishes([
           __DIR__.'/Controller/' => 'app/Http/Controllers',
        ],'controllers');
        //end

        //publish also models
        $this->publishes([
           __DIR__.'/Models/' => 'app/Models',
        ],'models');
        //end

        //seeder
        $this->publishes([
           __DIR__.'/seeder' => $this->app->databasePath().'/seeds/',
        ],'views');

        //seeder
        $this->publishes([
           __DIR__.'/Resources' => 'app/Http/Resources',
        ],'resources');

        //helpers
        $this->publishes([
           __DIR__.'/helpers' => 'app/Helpers',
        ],'helpers');
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
