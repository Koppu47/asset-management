<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAuditLog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    /**
     * Display a listing of the assets (Public / Catalog & Admin) aka filter.
     */
    public function index(Request $request)
    {
        $query = Asset::with('category')->latest();
        if ($request->user()?->isAdmin()) {
            $query->with('custodian');
        }

        // ค้นหาตามคำค้น (รหัส หรือ ชื่อ)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // กรองตามหมวดหมู่
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $assets = $query->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('assets.index', compact('assets', 'categories',));
    }

    /**
     * Display the specified asset details + QR Code.
     */
    public function show(Asset $asset)
    {
        $asset->load(['category', 'borrowRecords.user']);
        if (auth()->user()?->isAdmin()) {
            $asset->load(['custodian', 'auditLogs.user']);
        }

        // สร้าง QR Code จาก URL หน้านี้ (Third-party library: simplesoftwareio/simple-qrcode)
        $qrCode = QrCode::size(180)
            ->color(30, 41, 59)
            ->margin(1)
            ->generate(route('assets.show', $asset->id));

        return view('assets.show', compact('asset', 'qrCode'));
    }

    /**
     * Printable QR Code sticker for physical labeling.
     */
    public function printQr(Asset $asset)
    {
        $qrCode = QrCode::size(200)
            ->color(0, 0, 0)
            ->margin(1)
            ->generate(route('assets.show', $asset->id));

        return view('assets.print-qr', compact('asset', 'qrCode'));
    }

    /**
     * Show the form for creating a new asset (Admin only).
     */
    public function create()
    {
        Gate::authorize('create', Asset::class);

        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('assets.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created asset in storage (Admin only).
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Asset::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'asset_code' => 'required|string|max:50|unique:assets,asset_code',
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:available,maintenance,retired',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string|max:2000',
            'department' => 'nullable|string|max:150',
            'building' => 'nullable|string|max:150',
            'room' => 'nullable|string|max:100',
            'custodian_user_id' => 'nullable|exists:users,id',
        ], [
            'category_id.required' => 'กรุณาเลือกหมวดหมู่ครุภัณฑ์',
            'asset_code.required' => 'กรุณาระบุรหัสครุภัณฑ์',
            'asset_code.unique' => 'รหัสครุภัณฑ์นี้ถูกใช้งานแล้ว',
            'name.required' => 'กรุณาระบุชื่อครุภัณฑ์',
            'price.required' => 'กรุณาระบุราคาครุภัณฑ์',
            'purchase_date.required' => 'กรุณาระบุวันที่จัดซื้อ/ได้มา',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('assets', 'public');
            $validated['image'] = $path;
        }

        $asset = Asset::create($validated);
        AssetAuditLog::create([
            'asset_id' => $asset->id,
            'user_id' => $request->user()->id,
            'action' => 'created',
            'new_values' => $asset->only(['asset_code', 'name', 'department', 'building', 'room', 'custodian_user_id']),
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "เพิ่มครุภัณฑ์ '{$asset->name}' รหัส {$asset->asset_code} เข้าสู่ระบบสำเร็จแล้ว");
    }

    /**
     * Show the form for editing the specified asset (Admin only).
     */
    public function edit(Asset $asset)
    {
        Gate::authorize('update', $asset);

        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $hasActiveLoan = $asset->borrowRecords()->where('status', 'approved')->exists();

        return view('assets.edit', compact('asset', 'categories', 'hasActiveLoan', 'users'));
    }

    /**
     * Update the specified asset in storage (Admin only).
     */
    public function update(Request $request, Asset $asset)
    {
        Gate::authorize('update', $asset);

        $hasActiveLoan = $asset->borrowRecords()->where('status', 'approved')->exists();
        $allowedStatuses = $hasActiveLoan
            ? 'in:borrowed'
            : 'in:available,maintenance,retired';

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'asset_code' => 'required|string|max:50|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => ['required', $allowedStatuses],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string|max:2000',
            'department' => 'nullable|string|max:150',
            'building' => 'nullable|string|max:150',
            'room' => 'nullable|string|max:100',
            'custodian_user_id' => 'nullable|exists:users,id',
        ], [
            'category_id.required' => 'กรุณาเลือกหมวดหมู่ครุภัณฑ์',
            'asset_code.required' => 'กรุณาระบุรหัสครุภัณฑ์',
            'asset_code.unique' => 'รหัสครุภัณฑ์นี้ถูกใช้งานแล้ว',
            'name.required' => 'กรุณาระบุชื่อครุภัณฑ์',
            'price.required' => 'กรุณาระบุราคาครุภัณฑ์',
            'purchase_date.required' => 'กรุณาระบุวันที่จัดซื้อ/ได้มา',
        ]);

        if ($request->hasFile('image')) {
            // ลบรูปภาพเดิมหากมี
            if ($asset->image && Storage::disk('public')->exists($asset->image)) {
                Storage::disk('public')->delete($asset->image);
            }
            $path = $request->file('image')->store('assets', 'public');
            $validated['image'] = $path;
        }

        $trackedFields = ['category_id', 'asset_code', 'name', 'serial_number', 'price', 'purchase_date', 'status', 'department', 'building', 'room', 'custodian_user_id'];
        $oldValues = $asset->only($trackedFields);
        $asset->update($validated);
        $changedValues = array_intersect_key($asset->getChanges(), array_flip($trackedFields));
        if ($changedValues !== []) {
            AssetAuditLog::create([
                'asset_id' => $asset->id,
                'user_id' => $request->user()->id,
                'action' => 'updated',
                'old_values' => array_intersect_key($oldValues, $changedValues),
                'new_values' => $changedValues,
            ]);
        }

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "อัปเดตข้อมูลครุภัณฑ์ '{$asset->name}' เรียบร้อยแล้ว");
    }

    /**
     * Remove the specified asset from storage (Admin only).
     */
    public function destroy(Asset $asset)
    {
        Gate::authorize('delete', $asset);
        // ตรวจสอบว่าครุภัณฑ์ถูกยืมอยู่หรือไม่
        if ($asset->status === 'borrowed') {
            return redirect()->route('assets.index')
                ->with('error', "ไม่สามารถลบครุภัณฑ์ '{$asset->name}' ได้ เนื่องจากอยู่ในสถานะ 'ถูกยืมใช้งาน'");
        }

        if ($asset->borrowRecords()->exists()) {
            return redirect()->route('assets.index')
                ->with('error', "ไม่สามารถลบครุภัณฑ์ '{$asset->name}' ได้ เนื่องจากมีประวัติการยืม-คืนผูกอยู่");
        }

        if ($asset->inventorySurveyItems()->exists()) {
            return redirect()->route('assets.index')
                ->with('error', "ไม่สามารถลบครุภัณฑ์ '{$asset->name}' ได้ เนื่องจากมีประวัติการสำรวจครุภัณฑ์ผูกอยู่");
        }

        if ($asset->image && Storage::disk('public')->exists($asset->image)) {
            Storage::disk('public')->delete($asset->image);
        }

        $name = $asset->name;
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', "ลบรายการครุภัณฑ์ '{$name}' เรียบร้อยแล้ว");
    }
}
