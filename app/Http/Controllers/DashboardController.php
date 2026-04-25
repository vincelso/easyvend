<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $isAdmin = $user->isAdmin();

        if ($isAdmin) {
            $totalSales  = Transaction::sum('total');
            $totalOrders = Transaction::count();
            $totalUsers  = User::count();
            $todaySales  = Transaction::whereDate('created_at', today())->sum('total');
            $recent      = Transaction::with(['product', 'user'])->latest()->take(6)->get();
        } else {
            $totalSales  = Transaction::where('user_id', $user->id)->sum('total');
            $totalOrders = Transaction::where('user_id', $user->id)->count();
            $totalUsers  = null;
            $todaySales  = Transaction::where('user_id', $user->id)
                            ->whereDate('created_at', today())->sum('total');
            $recent      = Transaction::with(['product', 'user'])
                            ->where('user_id', $user->id)->latest()->take(6)->get();
        }

        // Stock alerts
        $lowStockProducts   = Product::where('stock', '>', 0)->where('stock', '<=', 10)->orderBy('stock')->get();
        $outOfStockProducts = Product::where('stock', 0)->get();

        // Expiry alerts (admin only)
        $expiringSoon    = Product::whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '>=', today())
                            ->whereDate('expiry_date', '<=', today()->addDays(30))
                            ->orderBy('expiry_date')->get();
        $expiredProducts = Product::whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<', today())->get();

        return view('dashboard.index', compact(
            'totalSales', 'totalOrders', 'totalUsers',
            'todaySales', 'recent', 'isAdmin',
            'lowStockProducts', 'outOfStockProducts',
            'expiringSoon', 'expiredProducts'
        ));
    }
}