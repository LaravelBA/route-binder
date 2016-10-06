<?php
namespace LaravelBA\RouteBinder;

use Illuminate\Routing\Router;

interface Bindings
{
    /**
     * Bind parameters, filters or anything you need to do
     * with the concrete router here.
     *
     * NOTE: If an object that's not an instance (nor an extension) of the concrete
     * \Illuminate\Routing\Router is bound as the \Illuminate\Contracts\Routing\Registrar
     * in the Container, **this method will never be called!**
     *
     * @param Router $router
     *
     * @return void
     */
    public function addBindings(Router $router);
}
