<?php namespace GuiWoda\RouteBinder;

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
        //
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
            __DIR__.'/config/route-binder.php' => $this->app->make('path.config') . '/route-binder.php',
        ]);

        $this->bootRoutes($config, $router);
    }

    /**
     * Register routes on boot
     *
     * @param Repository $config
     * @param Registrar  $router
     * @return void
     */
    public function bootRoutes(Repository $config, Registrar $router)
    {
        foreach ($config->get('route-binder::routes') as $binder)
        {
            $this->app->make($binder)->bind($router);
        }
    }
}
