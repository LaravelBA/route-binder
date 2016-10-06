<?php
namespace LaravelBA\RouteBinder;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class RouteBinderServiceProvider extends RouteServiceProvider
{
    protected $namespace = null;

    /**
     * @var Routes[]|Bindings[]
     */
    protected $binders;

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router = null)
    {
        $this->publishes([
            dirname(__DIR__) . '/config/routes.php' => $this->app->make('path.config') . '/routes.php',
        ], 'config');

        $this->binders = $this->app->call([$this, 'makeBinders']);

        $this->app->call([$this, 'bind']);

        parent::boot($router);
    }

    public function bind(Registrar $router)
    {
        if ($router instanceof \Illuminate\Routing\Router) {
            foreach ($this->getBindings() as $binder) {
                $binder->addBindings($router);
            }
        }
    }
    /**
     * Register routes on boot.
     *
     * @param Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        foreach ($this->getRoutes() as $binder) {
            $binder->addRoutes($router);
        }
    }

    /**
     * @return Bindings[]|Collection
     */
    protected function getBindings()
    {
        return Collection::make($this->binders)->filter(function ($item) {
            return $item instanceof Bindings;
        });
    }

    /**
     * @return Routes[]|Collection
     */
    protected function getRoutes()
    {
        return Collection::make($this->binders)->filter(function ($item) {
            return $item instanceof Routes;
        });
    }

    /**
     * @param Repository $config
     *
     * @return Routes[]|Bindings[]
     */
    public function makeBinders(Repository $config)
    {
        $binders = [];
        foreach ($config->get('routes.binders', []) as $binder) {
            $binders[] = $this->app->make($binder);
        }

        return $binders;
    }
}
