<?php
namespace DragAndPublish\Ip2locationSync;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;


final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap of package services
     *
     * @return  void
     */
    public function boot(Router $router): void
    {
        $this->bootPublishes();
                 $this->loadRoutes();    }

    /**
     * Register any application services
     *
     * @return  void
     */
    public function register(): void
    {        // package config file
        $this->mergeConfigFrom(__DIR__ . '/../config/ip2location-sync.php', 'ip2location-sync');
    }

    /**
     * Publishes resources on boot
     *
     * @return  void
     */
    private function bootPublishes(): void
    {        // package configs
        $this->publishes([
            __DIR__ . '/../config/ip2location-sync.php' => $this->app->configPath('ip2location-sync.php'),
        ], 'ip2location-sync-config');
         // migrations
        $migrationsPath = __DIR__ . '/../database/migrations/';

        $this->publishes([
            $migrationsPath => database_path('migrations/drag-and-publish/ip2location-sync')
        ], 'ip2location-sync-migrations');

        $this->loadMigrationsFrom($migrationsPath);    }
    /**
     * Load package specific routes
     *
     * @return  void
     */
    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
