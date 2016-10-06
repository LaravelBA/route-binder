<?php
namespace LaravelBA\RouteBinder;

use Illuminate\Contracts\Routing\Registrar;

interface Routes
{
    /**
     * Add all needed routes to the router.
     *
     * NOTE: This methods will NOT be called if the routes are cached,
     * so any binding logic should be done in `addBindings` and not here.
     *
     * @param Registrar $router
     *
     * @return void
     */
    public function addRoutes(Registrar $router);
}
