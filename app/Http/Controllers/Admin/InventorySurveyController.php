<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\InventorySurvey;
use App\Models\InventorySurveyItem;
use App\Models\AssetAuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InventorySurveyController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', InventorySurvey::class);

        $surveys = InventorySurvey::query()
            ->with('startedBy')
            ->withCount('items')
            ->withCount([
                'items as found_count' => fn ($query) => $query->where('status', 'found'),
                'items as missing_count' => fn ($query) => $query->where('status', 'missing'),
            ])
            ->orderByDesc('year')
            ->paginate(10);

        return view('admin.inventory-surveys.index', compact('surveys'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', InventorySurvey::class);

        $validated = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:' . now()->year,
                'unique:inventory_surveys,year',
            ],
        ], [
            'year.unique' => 'ปีนี้มีรอบสำรวจแล้ว กรุณาเปิดรอบเดิมเพื่อตรวจสอบความคืบหน้า',
            'year.max' => 'เลือกปีปัจจุบันหรือปีก่อนหน้าเท่านั้น',
        ]);

        $survey = DB::transaction(function () use ($validated, $request) {
            $survey = InventorySurvey::create([
                'year' => $validated['year'],
                'started_by' => $request->user()->id,
            ]);

            $assetIds = Asset::query()->pluck('id');
            $survey->items()->createMany(
                $assetIds->map(fn ($assetId) => ['asset_id' => $assetId])->all()
            );

            return $survey;
        });

        return redirect()->route('admin.inventory-surveys.show', $survey)
            ->with('success', "เริ่มรอบสำรวจปี {$survey->year} แล้ว บันทึกรายการครุภัณฑ์ {$survey->items()->count()} รายการ");
    }

    public function show(Request $request, InventorySurvey $inventorySurvey): View
    {
        Gate::authorize('view', $inventorySurvey);

        $query = $inventorySurvey->items()->with(['asset.category', 'checker', 'assignee'])->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();
            $query->whereHas('asset', function ($assetQuery) use ($search) {
                $assetQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if (in_array($request->query('status'), ['pending', 'found', 'missing'], true)) {
            $query->where('status', $request->query('status'));
        }

        $items = $query->paginate(15)->withQueryString();

        $statusCounts = $inventorySurvey->items()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalItems = (int) $statusCounts->sum();
        $foundCount = (int) ($statusCounts['found'] ?? 0);
        $missingCount = (int) ($statusCounts['missing'] ?? 0);
        $pendingCount = (int) ($statusCounts['pending'] ?? 0);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.inventory-surveys.show', compact(
            'inventorySurvey',
            'items',
            'totalItems',
            'foundCount',
            'missingCount',
            'pendingCount',
            'users',
        ));
    }

    public function updateItem(Request $request, InventorySurvey $inventorySurvey, InventorySurveyItem $item): RedirectResponse
    {
        Gate::authorize('update', $inventorySurvey);

        abort_unless($item->inventory_survey_id === $inventorySurvey->id, 404);

        if ($inventorySurvey->isComplete()) {
            return back()->with('error', 'รอบสำรวจนี้ปิดแล้ว จึงไม่สามารถแก้ไขผลการตรวจได้');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,found,missing'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $isChecked = $validated['status'] !== 'pending';

        $oldStatus = $item->status;
        $item->update([
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'checked_by' => $isChecked ? $request->user()->id : null,
            'checked_at' => $isChecked ? now() : null,
        ]);

        AssetAuditLog::create([
            'asset_id' => $item->asset_id,
            'user_id' => $request->user()->id,
            'action' => 'survey_'.$validated['status'],
            'old_values' => ['survey_status' => $oldStatus],
            'new_values' => ['survey_status' => $validated['status'], 'note' => $validated['note'] ?? null, 'survey_year' => $inventorySurvey->year],
        ]);

        $message = match ($validated['status']) {
            'found' => 'บันทึกผลว่า “พบครุภัณฑ์” แล้ว',
            'missing' => 'บันทึกผลว่า “ไม่พบครุภัณฑ์” แล้ว',
            default => 'เปลี่ยนรายการกลับเป็นรอตรวจแล้ว',
        };

        return back()->with('success', $message);
    }

    public function updateFollowUp(Request $request, InventorySurvey $inventorySurvey, InventorySurveyItem $item): RedirectResponse
    {
        Gate::authorize('update', $inventorySurvey);

        abort_unless($item->inventory_survey_id === $inventorySurvey->id, 404);
        abort_unless($item->status === 'missing', 422, 'ติดตามได้เฉพาะรายการที่ตรวจไม่พบ');

        $validated = $request->validate([
            'resolution_status' => ['required', 'in:open,in_progress,resolved'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'resolution_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['resolved_at'] = $validated['resolution_status'] === 'resolved' ? now() : null;
        $item->update($validated);

        AssetAuditLog::create([
            'asset_id' => $item->asset_id, 'user_id' => $request->user()->id, 'action' => 'survey_followup_'.$validated['resolution_status'],
            'new_values' => ['survey_year' => $inventorySurvey->year, 'assigned_to' => $validated['assigned_to'], 'resolution_note' => $validated['resolution_note']],
        ]);

        return back()->with('success', 'อัปเดตการติดตามรายการตรวจไม่พบแล้ว');
    }

    public function complete(InventorySurvey $inventorySurvey): RedirectResponse
    {
        Gate::authorize('update', $inventorySurvey);

        if ($inventorySurvey->isComplete()) {
            return back()->with('error', 'รอบสำรวจนี้ปิดไปแล้ว');
        }

        if ($inventorySurvey->items()->where('status', 'pending')->exists()) {
            return back()->with('error', 'ยังปิดรอบไม่ได้ กรุณาตรวจผลให้ครบทุกรายการก่อน');
        }

        $inventorySurvey->update(['completed_at' => now()]);

        return back()->with('success', "ปิดรอบสำรวจปี {$inventorySurvey->year} เรียบร้อยแล้ว");
    }
}
