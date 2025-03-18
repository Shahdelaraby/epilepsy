<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;




class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::prefix('api') // تحميل مسارات الـ API
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
