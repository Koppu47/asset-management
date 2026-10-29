<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InventorySurvey;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function assetsCsv(): StreamedResponse
    {
        $assets = Asset::with(['category', 'custodian'])->orderBy('asset_code')->get();

        return response()->streamDownload(function () use ($assets) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Asset Code', 'Name', 'Category', 'Status', 'Price', 'Purchase Date', 'Department', 'Building', 'Room', 'Custodian']);
            foreach ($assets as $asset) {
                fputcsv($handle, [$asset->asset_code, $asset->name, $asset->category?->name, $asset->status, $asset->price, $asset->purchase_date?->format('Y-m-d'), $asset->department, $asset->building, $asset->room, $asset->custodian?->name]);
            }
            fclose($handle);
        }, 'asset-report-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function assetsPrint(): View
    {
        $assets = Asset::with(['category', 'custodian'])->orderBy('asset_code')->get();
        return view('admin.reports.assets', compact('assets'));
    }

    public function surveyPrint(InventorySurvey $inventorySurvey): View
    {
        $inventorySurvey->load(['items.asset.category', 'items.assignee', 'startedBy']);
        return view('admin.reports.survey', compact('inventorySurvey'));
    }
}
