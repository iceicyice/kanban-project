<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        Gate::policy(User::class, UserPolicy::class);

    }
}
