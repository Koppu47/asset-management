@extends('layouts.app', [
    'title' => 'แดชบอร์ดผู้ดูแลระบบ'
])

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Asset management</p>
            <h2 class="mt-1 text-2xl font-bold leading-tight tracking-tight text-slate-950">ภาพรวมระบบ</h2>
            <p class="mt-1 text-sm text-slate-600">สถานะครุภัณฑ์และงานที่ต้องดำเนินการ · {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('assets.index') }}" class="btn btn-outline btn-sm">ดูแคตตาล็อก</a>
            <a href="{{ route('admin.reports.assets') }}" class="btn btn-outline btn-sm">รายงาน / PDF</a>
            <a href="{{ route('admin.reports.assets.csv') }}" class="btn btn-primary btn-sm">ดาวน์โหลด CSV</a>
        </div>
    </div>
@endsection

@section('content')

    <div class="space-y-8">
        <section aria-label="สรุปจำนวนครุภัณฑ์และงานค้าง">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 border-l-4 border-l-indigo-500 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">ครุภัณฑ์ทั้งหมด</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($totalAssets) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ number_format($totalCategories) }} หมวดหมู่</p>
                </div>

                <div class="rounded-2xl border border-emerald-200 border-l-4 border-l-emerald-500 bg-emerald-50/80 p-5 shadow-sm">
                    <p class="text-sm font-medium text-emerald-800">พร้อมใช้งาน</p>
                    <p class="mt-3 text-3xl font-bold text-emerald-900">{{ number_format($availableAssets) }}</p>
                    <p class="mt-1 text-sm text-emerald-700">รายการ</p>
                </div>

                <div class="rounded-2xl border border-blue-200 border-l-4 border-l-blue-500 bg-blue-50/80 p-5 shadow-sm">
                    <p class="text-sm font-medium text-blue-800">กำลังถูกยืม</p>
                    <p class="mt-3 text-3xl font-bold text-blue-900">{{ number_format($borrowedAssets) }}</p>
                    <p class="mt-1 text-sm text-blue-700">รายการ</p>
                </div>

                <a href="{{ route('admin.borrows.index', ['status' => 'pending']) }}" class="rounded-2xl border border-amber-200 border-l-4 border-l-amber-500 bg-amber-50/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-sm font-medium text-amber-800">รออนุมัติ</p>
                    <p class="mt-3 text-3xl font-bold text-amber-900">{{ number_format($pendingBorrowCount) }}</p>
                    <p class="mt-1 text-sm text-amber-700">คำขอ · คลิกเพื่อจัดการ</p>
                </a>

                <a href="{{ route('admin.borrows.index', ['status' => 'approved']) }}" class="rounded-2xl border border-rose-200 border-l-4 border-l-rose-500 bg-rose-50/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-sm font-medium text-rose-800">เกินกำหนดคืน</p>
                    <p class="mt-3 text-3xl font-bold text-rose-900">{{ number_format($overdueBorrowCount) }}</p>
                    <p class="mt-1 text-sm text-rose-700">รายการ · คลิกเพื่อติดตาม</p>
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-indigo-950">สำรวจครุภัณฑ์ประจำปี {{ now()->year }}</h3>
                    @if ($currentSurvey)
                        @php
                            $checkedCount = $currentSurvey->found_count + $currentSurvey->missing_count;
                            $pendingCount = $currentSurvey->items_count - $checkedCount;
                        @endphp
                        <p class="mt-1 text-sm text-indigo-800">
                            ตรวจแล้ว {{ number_format($checkedCount) }} / {{ number_format($currentSurvey->items_count) }} รายการ
                            · พบ {{ number_format($currentSurvey->found_count) }}
                            · ไม่พบ {{ number_format($currentSurvey->missing_count) }}
                            · คงเหลือ {{ number_format($pendingCount) }}
                        </p>
                    @else
                        <p class="mt-1 text-sm text-indigo-800">ยังไม่ได้เริ่มสำรวจปีนี้</p>
                    @endif
                </div>
                <a href="{{ route('admin.inventory-surveys.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
                    {{ $currentSurvey ? 'จัดการผลสำรวจ' : 'เริ่มสำรวจประจำปี' }}
                </a>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">คำขอยืมล่าสุดที่รออนุมัติ</h3>
                        <p class="mt-1 text-sm text-slate-500">แสดงไม่เกิน 5 รายการล่าสุด</p>
                    </div>
                    <a href="{{ route('admin.borrows.index', ['status' => 'pending']) }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">ดูทั้งหมด</a>
                </div>

                @if ($recentBorrowRequests->isEmpty())
                    <div class="mt-5 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        ไม่มีคำขอยืมที่รออนุมัติ
                    </div>
                @else
                    <div class="mt-5 overflow-x-auto">
                        <table class="table-data min-w-full divide-y divide-slate-200 text-left text-sm w-full">
                            <thead class="text-xs uppercase tracking-wide text-slate-500 bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 font-semibold w-1/4">ผู้ขอ</th>
                                    <th class="px-4 py-3 font-semibold w-2/5">ครุภัณฑ์</th>
                                    <th class="px-4 py-3 font-semibold w-1/6">วันที่ยืม</th>
                                    <th class="px-4 py-3 font-semibold w-1/6">กำหนดคืน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach ($recentBorrowRequests as $record)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $record->user->name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="block font-medium">{{ $record->asset->name }}</span>
                                            <span class="text-xs text-slate-500">{{ $record->asset->asset_code }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">{{ $record->borrow_date->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3">{{ $record->expected_return_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">หมวดหมู่ยอดนิยม</h3>
                        <p class="mt-1 text-sm text-slate-500">เรียงตามจำนวนครุภัณฑ์</p>
                    </div>
                    <a href="{{ route('categories.index') }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">จัดการ</a>
                </div>

                @if ($topCategories->isEmpty())
                    <div class="mt-5 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        ยังไม่มีหมวดหมู่
                    </div>
                @else
                    <ul class="mt-5 divide-y divide-slate-100">
                        @foreach ($topCategories as $category)
                            <li class="flex items-center justify-between gap-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate font-medium text-slate-800">{{ $category->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $category->code }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">{{ $category->assets_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-4 border-t border-slate-100 pt-4 text-sm text-slate-600">
                    ซ่อมบำรุง <span class="font-semibold text-amber-700">{{ number_format($maintenanceAssets) }}</span>
                    <span class="mx-2 text-slate-300">·</span>
                    จำหน่ายออก <span class="font-semibold text-slate-700">{{ number_format($retiredAssets) }}</span>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-rose-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">รายการเกินกำหนดคืน</h3>
                    <p class="mt-1 text-sm text-slate-500">ครุภัณฑ์ที่ยังไม่รับคืนและเลยวันที่กำหนด</p>
                </div>
                <a href="{{ route('admin.borrows.index', ['status' => 'approved']) }}" class="shrink-0 text-sm font-semibold text-rose-700 hover:text-rose-900">ติดตามรายการ</a>
            </div>

            @if ($overdueBorrowRecords->isEmpty())
                <div class="mt-5 rounded-xl bg-emerald-50 px-4 py-5 text-sm text-emerald-800">
                    ไม่มีรายการยืมเกินกำหนด
                </div>
            @else
                <div class="mt-5 overflow-x-auto">
                    <table class="table-data min-w-full divide-y divide-slate-200 text-left text-sm w-full">
                        <thead class="text-xs uppercase tracking-wide text-slate-500 bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-1/4">ผู้ยืม</th>
                                <th class="px-4 py-3 font-semibold w-2/5">ครุภัณฑ์</th>
                                <th class="px-4 py-3 font-semibold w-1/6">กำหนดคืน</th>
                                <th class="px-4 py-3 font-semibold w-1/6">เกินกำหนด</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($overdueBorrowRecords as $record)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $record->user->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $record->asset->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $record->asset->asset_code }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $record->expected_return_date->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-rose-700">{{ $record->expected_return_date->diffInDays(today()) }} วัน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div><h3 class="text-lg font-semibold text-slate-900">แจ้งเตือนกำหนดคืนใน 3 วัน</h3><p class="mt-1 text-sm text-slate-500">รายการยืมที่ใกล้ถึงกำหนดคืน แสดงเมื่อเปิดแดชบอร์ด</p></div>
                <a href="{{ route('admin.borrows.index', ['status' => 'approved']) }}" class="text-sm font-semibold text-amber-700">ดูรายการยืม</a>
            </div>
            @forelse ($dueSoonBorrowRecords as $record)
                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm">
                    <span><strong>{{ $record->asset->name }}</strong> · ผู้ยืม {{ $record->user->name }}</span>
                    <span class="font-semibold text-amber-900">กำหนดคืน {{ $record->expected_return_date->format('d/m/Y') }}</span>
                </div>
            @empty
                <p class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">ไม่มีรายการที่ใกล้ถึงกำหนดคืน</p>
            @endforelse
        </section>
    </div>
@endsection
