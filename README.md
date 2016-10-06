# Route Binder
Laravel route binding, done right.

[![Build Status](https://travis-ci.org/LaravelBA/route-binder.svg?branch=master)](https://travis-ci.org/LaravelBA/route-binder)

## Laravel 4 or 5?
The _master_ branch holds code compatible with Laravel 5. Releases for Laravel 5 start from the 3.0 tag.

For the Laravel 4 compatible release, go to [the laravel4 branch](https://github.com/LaravelBA/route-binder/tree/laravel4).

## The problem
Projects start simple: a few routes, maybe some resource controllers, and maybe some parameter binding here and there.
But soon, the `routes.php` file starts to pile up, spawning hundreds of lines, with complex nested groups and filters
or even (god forbid) having calls to `App::make`. Even more cumbersome, having to scroll all those lines searching for
that odd route name that you clearly forget because, who remembers those anyway?

This package helps you with (at least) three things:

1. It makes your routes part of your *Application* by [letting you use DI through the IoC container](#ioc)
2. It lets you split up routes in multiple files (classes) without the need for old-fashioned `includes` or `requires`
3. As you'll be creating classes, you have an opportunity to declare some string constants and hold references to those nasty route names

## The solution
This package is just two contracts, a config file and a `ServiceProvider`.

As usual, include the `ServiceProvider` in your `config/app.php` file like so:

```php
'providers' => [
    // ...
    LaravelBA\RouteBinder\RouteBinderServiceProvider::class,
    // ...
]
```

Then, publish the package's configuration:

```bash
php artisan vendor:publish --provider="LaravelBA\RouteBinder\RouteBinderServiceProvider"
```

Afterwards, you'll need to create some classes that implement either the `LaravelBA\RouteBinder\Routes` interface, the `LaravelBA\RouteBinder\Bindings` interface or both.
Don't panic! You'll see it's a piece of cake:
 
```php
namespace App\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Router;
use LaravelBA\RouteBinder\Bindings;
use LaravelBA\RouteBinder\Routes;

class FooRoutes implements Routes, Bindings
{
    /**
     * This is what I meant with #3 up there.
     * Completely optional, but highly recommended.
     */
    const INDEX = 'foo.index';

    /**
     * This one is required if you implement the Bindings interface
     */
    public function addBindings(Router $router)
    {
        $router->bind('user_id', function(){
            // Fetch your User object here!
        });
    }

    /**
     * This one is required if you implement the Routes interface
     */
    public function addRoutes(Registrar $router)
    {
        $router->get('foo', ['as' => self::INDEX, 'uses' => function(){
            return view('hello');
        }]);
    }
}
```

And add them to the published config file (you find it now in `config/routes.php`):

```php
return [
    'binders' => [
        App\Http\Routes\FooRoutes::class,
        App\Http\Routes\BarRoutes::class,
        App\Http\Routes\BazRoutes::class,
        App\Http\Routes\AwesomeRoutes::class,
    ]
];
```

And you're done! Now all your routes are nicely organized, and if things get out of hand, you can always split 'em up more!

## <a name="ioc"></a> The IoC Container
I love Laravel's Route model binding functionality. I must confess though, I don't use Eloquent, so I always go for the `Route::bind()` option.

But this feature, as powerful as it may be, is pretty nasty on your architecture. Having calls to the DB on the `routes.php` file
is awful, and going `App::make(SomeRepository::class)` does not look that much better either.
 
With this little package, your `Bindings` objects can depend on any `Service` or `Repository` layer of your application.
Now, you could even test those bindings by mocking the dependencies and expecting a call to whatever `Repository::find()` method 
you use on route resolution!
 
This may look like _waaaaaay_ too complicated a scenario right now, but trust me, you'll love it.
