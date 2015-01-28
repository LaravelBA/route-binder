<?php namespace GuiWoda\RouteBinder\Contracts;

use Illuminate\Routing\Router;

interface RouteBinder
{
    /**
     * Bind all needed routes to the router.
     * You may also bind parameters, filters or anything you need to do
     * with the router here.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function bind(Router $router);
}