<?php namespace GuiWoda\RouteBinder;

use Illuminate\Support\ServiceProvider;

final class RouteBinderServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Nothing here...
    }

    /**
     * Register routes on boot
     */
    public function boot()
    {
        $this->package('guiwoda/route-binder', null, realpath(__DIR__ . '/..'));

        $this->bootRoutes();
    }

    public function bootRoutes()
    {
        /** @type \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        /** @type \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        foreach ($config->get('route-binder::routes') as $binder)
        {
            $this->app->make($binder)->bind($router);
        }
    }
}