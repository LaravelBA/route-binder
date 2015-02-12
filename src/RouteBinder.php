<?php namespace LaravelBA\RouteBinder;

use Illuminate\Contracts\Routing\Registrar;

interface RouteBinder
{
    /**
     * Bind all needed routes to the router.
     * You may also bind parameters, filters or anything you need to do
     * with the router here.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     * @return void
     */
    public function bind(Registrar $router);
}