<?php
namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use LaravelBA\RouteBinder\Bindings;
use LaravelBA\RouteBinder\RouteBinder;
use LaravelBA\RouteBinder\RouteBinderServiceProvider;
use LaravelBA\RouteBinder\Routes;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

class RouteBinderServiceProviderTest extends MockeryTestCase
{
    /** @var RouteBinderServiceProvider */
    private $sp;

    /** @var Mock|Repository */
    private $configMock;

    /** @var Mock|Router */
    private $routerMock;

    /** @var Mock|RouteBinder */
    private $routeBinderMock;

    /** @var Mock|Application */
    private $appMock;

    /** @var Mock[] $binders */
    private $binders = [];

    protected function setUp()
    {
        $this->binders = [
            'foo' => \Mockery::mock(Bindings::class),
            'bar' => \Mockery::mock(Routes::class),
            'baz' => \Mockery::mock(RouteBinder::class),
        ];

        $this->appMock = \Mockery::mock(Application::class);
        $this->configMock = \Mockery::mock(Repository::class);
        $this->routerMock = \Mockery::mock(Router::class);
        $this->routeBinderMock = \Mockery::mock(RouteBinder::class);

        $this->appMock->shouldReceive('make')->with('path.config')->andReturn('/config');
        $this->appMock->shouldReceive('booted');

        $this->sp = new RouteBinderServiceProvider($this->appMock);
    }

    public function test_it_should_publish_the_config_path()
    {
        $this->appMock->shouldReceive('routesAreCached')->andReturn(true);
        $this->appMock->shouldReceive('call');

        $this->sp->boot($this->routerMock);

        $paths = ServiceProvider::pathsToPublish(RouteBinderServiceProvider::class, 'config');

        $this->assertArrayHasKey(dirname(__DIR__) . '/config/routes.php', $paths);
        $this->assertContains('/config/routes.php', $paths);
    }

    public function test_it_should_call_all_binders()
    {
        $this->configMock->shouldReceive('get')->with('routes.binders', [])->andReturn([
            'foo', 'bar', 'baz',
        ]);

        $this->appMock->shouldReceive('routesAreCached')->andReturn(false);
        $this->expectBinders();

        $this->sp->boot($this->routerMock);
    }

    protected function expectBinders()
    {
        $this->appMock->shouldReceive('call')->andReturnUsing(
            $this->returnUsingAppCall($this->configMock),
            $this->returnUsingAppCall($this->routerMock)
        );

        foreach ($this->binders as $key => $mock) {
            $this->appMock->shouldReceive('make')->with($key)->once()->andReturn($mock);

            if ($mock instanceof Bindings) {
                $mock->shouldReceive('addBindings')->once()->with($this->routerMock);
            }

            if ($mock instanceof Routes) {
                $mock->shouldReceive('addRoutes')->once()->with($this->routerMock);
            }
        }
    }
    /**
     * @param array $params
     *
     * @return \Closure
     */
    protected function returnUsingAppCall(...$params)
    {
        return function ($args) use ($params) {
            list($sp, $method) = $args;

            $this->assertSame($this->sp, $sp);
            $this->assertTrue(method_exists($this->sp, $method));

            $reflection = (new \ReflectionObject($sp))->getMethod($method);
            $reflection->setAccessible(true);

            return $reflection->invoke($sp, ...$params);
        };
    }
}
