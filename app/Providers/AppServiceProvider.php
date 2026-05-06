<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share alert counts with navigation on every page
        View::composer('layouts.navigation', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                $alertExpired      = Product::whereNotNull('expiry_date')
                                        ->whereDate('expiry_date', '<', today())->get();
                $alertExpiringSoon = Product::whereNotNull('expiry_date')
                                        ->whereDate('expiry_date', '>=', today())
                                        ->whereDate('expiry_date', '<=', today()->addDays(30))->get();
                $alertOutOfStock   = Product::where('stock', 0)->get();
                $alertLowStock     = Product::where('stock', '>', 0)->where('stock', '<=', 10)->get();

                $totalAlerts = $alertExpired->count() + $alertExpiringSoon->count()
                             + $alertOutOfStock->count() + $alertLowStock->count();

                $view->with(compact(
                    'alertExpired', 'alertExpiringSoon',
                    'alertOutOfStock', 'alertLowStock', 'totalAlerts'
                ));
            } else {
                $view->with([
                    'alertExpired'      => collect(),
                    'alertExpiringSoon' => collect(),
                    'alertOutOfStock'   => collect(),
                    'alertLowStock'     => collect(),
                    'totalAlerts'       => 0,
                ]);
            }
        });
    }
}