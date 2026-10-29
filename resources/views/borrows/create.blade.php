@extends('layouts.app', [
    'title' => "ยื่นคำร้องขอยืมครุภัณฑ์: {$asset->name}",
])

@section('header')
    <div class="flex items-center space-x-3">
        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            ย้อนกลับ
        </a>
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('ยื่นคำร้องขอยืมครุภัณฑ์') }}
        </h2>
    </div>
@endsection

@section('content')

    <div class="max-w-2xl mx-auto">
        <!-- สรุปข้อมูลครุภัณฑ์ที่ต้องการยืม -->
        <div class="alert alert-info mb-6 flex items-center space-x-4">
            @if ($asset->image)
                <img src="{{ asset('storage/' . $asset->image) }}" class="w-20 h-20 rounded-lg object-cover border border-indigo-200">
            @else
                <div class="w-20 h-20 bg-indigo-100 text-indigo-400 rounded-lg flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            <div>
                <span class="asset-code-tag">{{ $asset->asset_code }}</span>
                <h3 class="font-bold text-base text-slate-900">{{ $asset->name }}</h3>
                <p class="text-xs text-slate-500 mt-1">หมวดหมู่: {{ $asset->category->name }} | หมายเลขเครื่อง: {{ $asset->serial_number ?? '-' }}</p>
            </div>
        </div>

        <div class="card p-8">
            <form action="{{ route('borrows.store', $asset->id) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="borrow_date" class="form-label">
                                วันที่ต้องการเริ่มยืม <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="borrow_date" id="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}" required
                                class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="expected_return_date" class="form-label">
                                กำหนดวันที่ส่งคืน <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="expected_return_date" id="expected_return_date" value="{{ old('expected_return_date', date('Y-m-d', strtotime('+3 days'))) }}" required
                                class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="note" class="form-label">
                            วัตถุประสงค์ในการขอยืม / สถานที่ใช้งาน
                        </label>
                        <textarea name="note" id="note" rows="3"
                            placeholder="เช่น ยืมไปใช้งานอบรมสัมมนาประจำปี ณ ห้องประชุม 201..."
                            class="form-textarea">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-outline">
                            ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-success">
                            ยืนยันการยื่นคำร้อง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
