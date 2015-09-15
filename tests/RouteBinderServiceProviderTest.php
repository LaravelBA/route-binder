<?php
namespace Tests;

use LaravelBA\RouteBinder\RouteBinder;
use LaravelBA\RouteBinder\RouteBinderServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\Registrar as Router;
use Mockery\Mock;

class RouteBinderServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @type RouteBinderServiceProvider */
    private $sp;
    /** @type Mock|Repository */
    private $configMock;
    /** @type Mock|Router */
    private $routerMock;
    /** @type Mock|RouteBinder */
    private $routeBinderMock;
    /** @type Mock|Container */
    private $appMock;
    /** @type array */
    private $routeBinders = [
        'fooBinder', 'barBinder', 'bazBinder'
    ];

    function setUp()
    {
        $this->appMock         = \Mockery::mock(Container::class);
        $this->configMock      = \Mockery::mock(Repository::class);
        $this->routerMock      = \Mockery::mock(Router::class);
        $this->routeBinderMock = \Mockery::mock(RouteBinder::class);

        $this->appMock
            ->shouldReceive('offsetGet')
            ->with('config')
            ->once()
            ->andReturn($this->configMock);

        $this->appMock
            ->shouldReceive('offsetGet')
            ->with('router')
            ->once()
            ->andReturn($this->routerMock);

        $this->appMock
            ->shouldReceive('make')
            ->with(\Mockery::anyOf('fooBinder', 'barBinder', 'bazBinder'))
            ->andReturn($this->routeBinderMock);

        $this->appMock
            ->shouldReceive('make')
            ->with('path.config')
            ->andReturn('/a/given/path');

        $this->configMock
            ->shouldReceive('get')
            ->with('routes.binders', [])
            ->andReturn($this->routeBinders);

        $this->routeBinderMock
            ->shouldReceive('addBindings')
            ->with($this->routerMock)
            ->times(count($this->routeBinders));

        $this->sp = new RouteBinderServiceProvider($this->appMock);
    }

    /** @test */
    function it_should_make_all_routing_classes_and_call_bind_and_routes_on_them()
    {
        $this->appMock->shouldReceive('routesAreCached')->andReturn(false);

        $this->routeBinderMock
            ->shouldReceive('addRoutes')
            ->with($this->routerMock)
            ->times(count($this->routeBinders));

        $this->sp->boot($this->configMock, $this->routerMock);
    }

    /** @test */
    function it_make_all_routing_classes_but_only_call_bind_on_them()
    {
        $this->appMock->shouldReceive('routesAreCached')->andReturn(true);

        $this->routeBinderMock->shouldNotReceive('addRoutes');
        $this->appMock->shouldReceive('booted')->once();

        $this->sp->boot($this->configMock, $this->routerMock);
    }
}
