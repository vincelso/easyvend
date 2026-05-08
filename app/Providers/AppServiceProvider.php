<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.navigation', function ($view) {
            $alertExpired = collect();
            $alertExpiringSoon = collect();
            $alertOutOfStock = collect();
            $alertLowStock = collect();

            if (auth()->check() && auth()->user()->isAdmin()) {
                $today = Carbon::today();
                $soon = Carbon::today()->addDays(7); // adjust days as needed

                $alertExpired = Product::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', $today)
                    ->get();

                $alertExpiringSoon = Product::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '>=', $today)
                    ->whereDate('expiry_date', '<=', $soon)
                    ->get();

                $alertOutOfStock = Product::where('stock', '<=', 0)->get();

                $alertLowStock = Product::where('stock', '>', 0)
                    ->where('stock', '<=', 5) // adjust threshold as needed
                    ->get();
            }

            $totalAlerts = $alertExpired->count()
                + $alertExpiringSoon->count()
                + $alertOutOfStock->count()
                + $alertLowStock->count();

            $view->with(compact(
                'totalAlerts',
                'alertExpired',
                'alertExpiringSoon',
                'alertOutOfStock',
                'alertLowStock'
            ));
        });
    }
}