<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BorrowRecordController extends Controller
{
    /**
     * Admin view: Listing of all borrow records.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', BorrowRecord::class);

        $query = BorrowRecord::with(['user', 'asset.category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $records = $query->paginate(10)->withQueryString();

        return view('borrows.index', compact('records'));
    }

    /**
     * User view: Listing of logged-in user's borrow records.
     */
    public function myBorrows()
    {
        $records = BorrowRecord::with(['asset.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $returnReminders = BorrowRecord::with('asset')
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->whereBetween('expected_return_date', [today(), today()->addDays(3)])
            ->orderBy('expected_return_date')
            ->get();

        return view('borrows.my-borrows', compact('records', 'returnReminders'));
    }

    /**
     * Show form to request borrow an asset.
     */
    public function create(Asset $asset)
    {
        Gate::authorize('borrow', $asset);

        if (! $asset->isAvailable()) {
            return redirect()->route('assets.show', $asset->id)
                ->with('error', "ครุภัณฑ์ '{$asset->name}' ไม่พร้อมให้ยืมในขณะนี้");
        }

        if ($asset->borrowRecords()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('assets.show', $asset->id)
                ->with('error', "ครุภัณฑ์ '{$asset->name}' มีคำขอยืมหรือรายการยืมที่ยังไม่ปิดอยู่แล้ว");
        }

        return view('borrows.create', compact('asset'));
    }

    /**
     * Store a newly created borrow request.
     */
    public function store(Request $request, Asset $asset)
    {
        Gate::authorize('borrow', $asset);

        $validated = $request->validate([
            'borrow_date' => 'required|date|after_or_equal:today',
            'expected_return_date' => 'required|date|after_or_equal:borrow_date',
            'note' => 'nullable|string|max:1000',
        ], [
            'borrow_date.required' => 'กรุณาระบุวันที่เริ่มยืม',
            'borrow_date.after_or_equal' => 'วันที่เริ่มยืมต้องเป็นวันนี้หรือในอนาคต',
            'expected_return_date.required' => 'กรุณาระบุกำหนดวันที่ส่งคืน',
            'expected_return_date.after_or_equal' => 'กำหนดวันที่ส่งคืนต้องไม่ก่อนวันที่เริ่มยืม',
        ]);

        $result = DB::transaction(function () use ($asset, $validated) {
            $lockedAsset = Asset::query()->lockForUpdate()->findOrFail($asset->id);

            if (! $lockedAsset->isAvailable()) {
                return 'ครุภัณฑ์นี้ไม่พร้อมให้ยืมในขณะนี้';
            }

            $hasOpenRequest = $lockedAsset->borrowRecords()
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($hasOpenRequest) {
                return 'ครุภัณฑ์นี้มีคำขอยืมหรือรายการยืมที่ยังไม่ปิดอยู่แล้ว';
            }

            $lockedAsset->borrowRecords()->create($validated + [
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            return null;
        });

        if ($result !== null) {
            return redirect()->route('assets.show', $asset->id)->with('error', $result);
        }

        return redirect()->route('borrows.my')
            ->with('success', "ยื่นคำร้องขอยืมครุภัณฑ์ '{$asset->name}' เรียบร้อยแล้ว กรุณารอเจ้าหน้าที่ตรวจสอบ");
    }

    /**
     * Admin approves the borrow request (Updates status & marks asset borrowed).
     */
    public function approve(BorrowRecord $borrowRecord)
    {
        Gate::authorize('approve', $borrowRecord);

        $result = DB::transaction(function () use ($borrowRecord) {
            $lockedRecord = BorrowRecord::query()->lockForUpdate()->findOrFail($borrowRecord->id);

            if ($lockedRecord->status !== 'pending') {
                return ['error' => 'คำขอนี้ไม่ได้อยู่ในสถานะรอการอนุมัติ'];
            }

            $asset = Asset::query()->lockForUpdate()->findOrFail($lockedRecord->asset_id);

            if (! $asset->isAvailable()) {
                return ['error' => 'ครุภัณฑ์นี้ไม่พร้อมให้ยืมแล้ว กรุณาตรวจสอบสถานะก่อนอนุมัติ'];
            }

            $lockedRecord->update(['status' => 'approved']);
            $asset->update(['status' => 'borrowed']);

            return ['success' => "อนุมัติคำขอยืมครุภัณฑ์ '{$asset->name}' ให้กับ {$lockedRecord->user->name} เรียบร้อยแล้ว"];
        });

        return back()->with(isset($result['error']) ? 'error' : 'success', $result['error'] ?? $result['success']);
    }

    /**
     * Admin rejects the borrow request.
     */
    public function reject(Request $request, BorrowRecord $borrowRecord)
    {
        Gate::authorize('reject', $borrowRecord);

        $result = DB::transaction(function () use ($borrowRecord) {
            $lockedRecord = BorrowRecord::query()->lockForUpdate()->findOrFail($borrowRecord->id);

            if ($lockedRecord->status !== 'pending') {
                return false;
            }

            $lockedRecord->update(['status' => 'rejected']);

            return true;
        });

        if (! $result) {
            return back()->with('error', 'คำขอนี้ไม่ได้อยู่ในสถานะรอการอนุมัติ');
        }

        return back()->with('success', "ปฏิเสธคำขอยืมครุภัณฑ์เรียบร้อยแล้ว");
    }

    /**
     * Admin marks asset as returned (Updates status & restores asset to available).
     */
    public function returnAsset(BorrowRecord $borrowRecord)
    {
        Gate::authorize('return', $borrowRecord);

        $result = DB::transaction(function () use ($borrowRecord) {
            $lockedRecord = BorrowRecord::query()->lockForUpdate()->findOrFail($borrowRecord->id);

            if ($lockedRecord->status !== 'approved') {
                return ['error' => 'รายการนี้ไม่ได้อยู่ในสถานะกำลังถูกยืม'];
            }

            $asset = Asset::query()->lockForUpdate()->findOrFail($lockedRecord->asset_id);

            if ($asset->status !== 'borrowed') {
                return ['error' => 'สถานะครุภัณฑ์ไม่ตรงกับรายการยืม กรุณาตรวจสอบข้อมูลก่อนบันทึกรับคืน'];
            }

            $lockedRecord->update([
                'status' => 'returned',
                'actual_return_date' => now()->toDateString(),
            ]);

            $asset->update(['status' => 'available']);

            return ['success' => "บันทึกรับคืนครุภัณฑ์ '{$asset->name}' เรียบร้อยแล้ว"];
        });

        return back()->with(isset($result['error']) ? 'error' : 'success', $result['error'] ?? $result['success']);
    }

    /**
     * User cancels their own pending borrow request.
     */
    public function cancel(BorrowRecord $borrowRecord)
    {
        Gate::authorize('cancel', $borrowRecord);

        $result = DB::transaction(function () use ($borrowRecord) {
            $lockedRecord = BorrowRecord::query()->lockForUpdate()->findOrFail($borrowRecord->id);

            if ($lockedRecord->status !== 'pending') {
                return false;
            }

            $lockedRecord->delete();

            return true;
        });

        if (! $result) {
            return back()->with('error', 'สามารถยกเลิกได้เฉพาะคำขอที่อยู่ในสถานะรอดำเนินการเท่านั้น');
        }

        return redirect()->route('borrows.my')
            ->with('success', 'ยกเลิกคำขอยืมครุภัณฑ์เรียบร้อยแล้ว');
    }
}
