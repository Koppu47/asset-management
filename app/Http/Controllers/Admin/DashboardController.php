<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\InventorySurvey;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $assetCounts = Asset::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalAssets = (int) $assetCounts->sum();
        $pendingBorrowCount = BorrowRecord::where('status', 'pending')->count();

        $overdueQuery = BorrowRecord::query()
            ->where('status', 'approved')
            ->whereDate('expected_return_date', '<', today());

        $overdueBorrowCount = (clone $overdueQuery)->count();

        $recentBorrowRequests = BorrowRecord::with(['user', 'asset'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $overdueBorrowRecords = $overdueQuery
            ->with(['user', 'asset'])
            ->orderBy('expected_return_date')
            ->take(5)
            ->get();

        $dueSoonBorrowRecords = BorrowRecord::query()
            ->where('status', 'approved')
            ->whereBetween('expected_return_date', [today(), today()->addDays(3)])
            ->with(['user', 'asset'])
            ->orderBy('expected_return_date')
            ->take(5)
            ->get();

        $topCategories = Category::withCount('assets')
            ->orderByDesc('assets_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $currentSurvey = InventorySurvey::query()
            ->withCount('items')
            ->withCount([
                'items as found_count' => fn ($query) => $query->where('status', 'found'),
                'items as missing_count' => fn ($query) => $query->where('status', 'missing'),
            ])
            ->where('year', now()->year)
            ->first();

        return view('admin.dashboard', [
            'totalAssets' => $totalAssets,
            'availableAssets' => (int) ($assetCounts['available'] ?? 0),
            'borrowedAssets' => (int) ($assetCounts['borrowed'] ?? 0),
            'maintenanceAssets' => (int) ($assetCounts['maintenance'] ?? 0),
            'retiredAssets' => (int) ($assetCounts['retired'] ?? 0),
            'totalCategories' => Category::count(),
            'pendingBorrowCount' => $pendingBorrowCount,
            'overdueBorrowCount' => $overdueBorrowCount,
            'recentBorrowRequests' => $recentBorrowRequests,
            'overdueBorrowRecords' => $overdueBorrowRecords,
            'dueSoonBorrowRecords' => $dueSoonBorrowRecords,
            'topCategories' => $topCategories,
            'currentSurvey' => $currentSurvey,
        ]);
    }
}
