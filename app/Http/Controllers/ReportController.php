<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30');
        $data   = $this->getReportData($request);

        return view('reports.index', array_merge($data, ['period' => $period]));
    }

    public function export(Request $request)
    {
        $period = $request->get('period', '30');
        $type   = $request->get('type', 'sales'); // sales, stock, users
        $data   = $this->getReportData($request);

        $pdf = Pdf::loadView('reports.pdf', array_merge($data, [
            'period'      => $period,
            'type'        => $type,
            'generatedAt' => now()->format('M d, Y h:i A'),
            'generatedBy' => auth()->user()->name,
        ]))->setPaper('a4', 'portrait');

        $filename = "easyvend-{$type}-report-" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    private function getReportData(Request $request): array
    {
        $period    = $request->get('period', '30');
        $startDate = now()->subDays((int) $period)->startOfDay();
        $user      = auth()->user();

        // Base transaction query (cashier sees only their own)
        $txQuery = Transaction::where('created_at', '>=', $startDate);
        if ($user->isCashier()) {
            $txQuery->where('user_id', $user->id);
        }

        $totalRevenue = (clone $txQuery)->sum('total');
        $totalOrders  = (clone $txQuery)->count();
        $avgOrder     = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top products
        $topProductsQuery = DB::table('transactions')
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->where('transactions.created_at', '>=', $startDate);
        if ($user->isCashier()) {
            $topProductsQuery->where('transactions.user_id', $user->id);
        }
        $topProducts = $topProductsQuery
            ->select('products.name',
                DB::raw('SUM(transactions.qty) as total_qty'),
                DB::raw('SUM(transactions.total) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)->get();

        // Payment breakdown
        $paymentQuery = Transaction::where('created_at', '>=', $startDate);
        if ($user->isCashier()) {
            $paymentQuery->where('user_id', $user->id);
        }
        $paymentBreakdown = $paymentQuery
            ->select('payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')->get();

        // Daily sales
        $dailyQuery = Transaction::where('created_at', '>=', $startDate);
        if ($user->isCashier()) {
            $dailyQuery->where('user_id', $user->id);
        }
        $dailySales = $dailyQuery
            ->select(DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as orders'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')->get();

        // Cashier stats (admin only)
        $cashierStats = collect();
        if ($user->isAdmin()) {
            $cashierStats = DB::table('transactions')
                ->join('users', 'transactions.user_id', '=', 'users.id')
                ->where('transactions.created_at', '>=', $startDate)
                ->select('users.name',
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(transactions.total) as total_revenue'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_revenue')->get();
        }

        // Stock data
        $stockSummary    = Product::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock'),
            DB::raw('SUM(CASE WHEN stock > 0 AND stock <= 10 THEN 1 ELSE 0 END) as low_stock'),
            DB::raw('SUM(CASE WHEN stock > 10 THEN 1 ELSE 0 END) as healthy')
        )->first();
        $lowStockItems   = Product::where('stock', '>', 0)->where('stock', '<=', 10)->orderBy('stock')->get();
        $outOfStockItems = Product::where('stock', 0)->get();

        // Users (admin only)
        $userStats = collect();
        if ($user->isAdmin()) {
            $userStats = User::withCount('transactions')->get();
        }

        return compact(
            'totalRevenue', 'totalOrders', 'avgOrder',
            'topProducts', 'paymentBreakdown', 'dailySales',
            'cashierStats', 'stockSummary', 'lowStockItems',
            'outOfStockItems', 'userStats', 'startDate'
        );
    }
}