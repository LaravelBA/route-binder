<?php
namespace LaravelBA\RouteBinder;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\ServiceProvider;

final class RouteBinderServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // No need to merge original
    }

    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Contracts\Routing\Registrar $router
     * @return void
     */
    public function boot(Repository $config, Registrar $router)
    {
        $this->publishes([
            dirname(__DIR__) . '/config/routes.php' => $this->app->make('path.config') . '/routes.php',
        ], 'config');

        $this->bootBinders($config, $router);
    }

    /**
     * Register routes on boot
     *
     * @param Repository $config
     * @param Registrar  $router
     * @return void
     */
    protected function bootBinders(Repository $config, Registrar $router)
    {
        foreach ($this->getBinders($config) as $binder) {
            if ($router instanceof \Illuminate\Routing\Router) {
                $binder->addBindings($router);
            }

            if (! $this->app->routesAreCached()) {
                $binder->addRoutes($router);
            }
        }

        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        }
    }

    /**
     * @param Repository $config
     * @return RouteBinder[]
     */
    protected function getBinders(Repository $config)
    {
        foreach ($config->get('routes.binders') as $binder) {
            yield $this->app->make($binder);
        }
    }

    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }
}
