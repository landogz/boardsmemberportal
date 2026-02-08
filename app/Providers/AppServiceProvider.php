<?php

namespace App\Providers;

use App\Models\BannerSlide;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('landing', function ($view) {
            $view->with('bannerSlides', BannerSlide::where('is_active', true)->with(['media', 'mediaTablet', 'mediaMobile'])->orderBy('sort_order')->orderBy('id')->get());
        });
        // Register Blade directive for permission checks
        // Use hasPermission() to ensure admin privilege has full access
        \Blade::if('can', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        \Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        \Blade::if('hasAnyRole', function ($roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
    }
}
