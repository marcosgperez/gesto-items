<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // AUTH ROUTES
            Route::prefix('v1')
                ->namespace($this->namespace)
                ->group(base_path('routes/userTypes.php'));

            Route::prefix('v1')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(function () {
                    require base_path('routes/users.php');
                    require base_path('routes/sectors.php');
                    require base_path('routes/parts.php');
                    require base_path('routes/items.php');
                    require base_path('routes/histories.php');
                    require base_path('routes/floors.php');
                    require base_path('routes/eventTypes.php');
                    require base_path('routes/events.php');
                });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
