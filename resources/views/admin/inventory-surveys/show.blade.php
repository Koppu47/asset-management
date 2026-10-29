@extends('layouts.app', [
    'title' => "สำรวจครุภัณฑ์ปี {$inventorySurvey->year}",
])

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.inventory-surveys.index') }}" class="btn btn-outline btn-sm">← รอบสำรวจทั้งหมด</a>
            <h2 class="mt-2 text-xl font-semibold leading-tight text-slate-800">สำรวจครุภัณฑ์ปี {{ $inventorySurvey->year }}</h2>
            <p class="mt-1 text-sm text-slate-500">บันทึกผลตรวจสอบการมีอยู่จริงของครุภัณฑ์แต่ละรายการ</p>
        </div>
        @if ($inventorySurvey->isComplete())
            <span class="badge badge-success">ปิดรอบแล้ว {{ $inventorySurvey->completed_at->format('d/m/Y H:i') }}</span>
        @elseif ($pendingCount === 0)
            <form method="POST" action="{{ route('admin.inventory-surveys.complete', $inventorySurvey) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    ปิดรอบสำรวจ
                </button>
            </form>
        @else
            <span class="text-sm font-medium text-amber-700">ตรวจอีก {{ number_format($pendingCount) }} รายการก่อนปิดรอบ</span>
        @endif
        <a href="{{ route('admin.reports.surveys', $inventorySurvey) }}" class="btn btn-outline">รายงานสำรวจ / PDF</a>
    </div>
@endsection

@section('content')

    <div class="space-y-6">
        <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">รายการทั้งหมด</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($totalItems) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                <p class="text-sm text-emerald-800">พบแล้ว</p>
                <p class="mt-1 text-2xl font-bold text-emerald-900">{{ number_format($foundCount) }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <p class="text-sm text-rose-800">ไม่พบ</p>
                <p class="mt-1 text-2xl font-bold text-rose-900">{{ number_format($missingCount) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <p class="text-sm text-amber-800">รอตรวจ</p>
                <p class="mt-1 text-2xl font-bold text-amber-900">{{ number_format($pendingCount) }}</p>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.inventory-surveys.show', $inventorySurvey) }}" class="grid gap-3 sm:grid-cols-[1fr_12rem_auto_auto] sm:items-end">
                <div>
                    <label for="search" class="mb-1 block text-sm font-medium text-slate-700">ค้นหาครุภัณฑ์</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="ชื่อ รหัส หรือ Serial Number"
                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">ผลการตรวจ</label>
                    <select id="status" name="status" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">ทุกสถานะ</option>
                        <option value="pending" @selected(request('status') === 'pending')>รอตรวจ</option>
                        <option value="found" @selected(request('status') === 'found')>พบแล้ว</option>
                        <option value="missing" @selected(request('status') === 'missing')>ไม่พบ</option>
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">ค้นหา</button>
                <a href="{{ route('admin.inventory-surveys.show', $inventorySurvey) }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">ล้างตัวกรอง</a>
            </form>
        </section>

        <section class="space-y-4">
            @forelse ($items as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-slate-900">{{ $item->asset->name }}</h3>
                                @if ($item->status === 'found')
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">พบแล้ว</span>
                                @elseif ($item->status === 'missing')
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-800">ไม่พบ</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">รอตรวจ</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-600">รหัส {{ $item->asset->asset_code }} · {{ $item->asset->category->name }}</p>
                            @if ($item->asset->serial_number)
                                <p class="mt-1 text-xs text-slate-500">Serial Number: {{ $item->asset->serial_number }}</p>
                            @endif
                            <a href="{{ route('assets.show', $item->asset) }}" target="_blank" rel="noopener" class="mt-2 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-800">เปิดรายละเอียดและ QR Code</a>

                            @if ($item->checked_at)
                                <p class="mt-2 text-xs text-slate-500">
                                    ตรวจเมื่อ {{ $item->checked_at->format('d/m/Y H:i') }}
                                    @if ($item->checker)
                                        โดย {{ $item->checker->name }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        @if ($inventorySurvey->isComplete())
                            <p class="max-w-xl whitespace-pre-line text-sm text-slate-600">{{ $item->note ?: 'ไม่มีหมายเหตุ' }}</p>
                        @else
                            <form method="POST" action="{{ route('admin.inventory-surveys.items.update', [$inventorySurvey, $item]) }}" class="grid w-full gap-2 lg:max-w-2xl lg:grid-cols-[10rem_1fr_auto]">
                                @csrf
                                @method('PATCH')
                                <label class="sr-only" for="status-{{ $item->id }}">ผลการตรวจ {{ $item->asset->name }}</label>
                                <select id="status-{{ $item->id }}" name="status" required class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" @selected($item->status === 'pending')>รอตรวจ</option>
                                    <option value="found" @selected($item->status === 'found')>พบครุภัณฑ์</option>
                                    <option value="missing" @selected($item->status === 'missing')>ไม่พบครุภัณฑ์</option>
                                </select>
                                <input name="note" value="{{ $item->note }}" maxlength="1000" placeholder="หมายเหตุหรือสภาพครุภัณฑ์ (ถ้ามี)" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">บันทึกผล</button>
                            </form>
                        @endif
                    </div>
                    @if ($item->status === 'missing')
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4">
                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-rose-900">ติดตามแก้ไขรายการไม่พบ</span>
                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-rose-800">{{ ['open' => 'เปิดงาน', 'in_progress' => 'กำลังติดตาม', 'resolved' => 'แก้ไขแล้ว'][$item->resolution_status] ?? 'เปิดงาน' }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.inventory-surveys.items.follow-up', [$inventorySurvey, $item]) }}" class="grid gap-2 md:grid-cols-[12rem_1fr_1fr_auto]">
                                @csrf @method('PATCH')
                                <select name="resolution_status" class="rounded-lg border-slate-300 text-sm">
                                    <option value="open" @selected($item->resolution_status === 'open')>เปิดงาน</option>
                                    <option value="in_progress" @selected($item->resolution_status === 'in_progress')>กำลังติดตาม</option>
                                    <option value="resolved" @selected($item->resolution_status === 'resolved')>แก้ไขแล้ว</option>
                                </select>
                                <select name="assigned_to" class="rounded-lg border-slate-300 text-sm">
                                    <option value="">ยังไม่มอบหมาย</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected($item->assigned_to == $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <input name="resolution_note" maxlength="1000" value="{{ $item->resolution_note }}" placeholder="บันทึกการติดตาม/ผลแก้ไข" class="rounded-lg border-slate-300 text-sm">
                                <button class="rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800">บันทึกติดตาม</button>
                            </form>
                            @if ($item->resolved_at)<p class="mt-2 text-xs text-emerald-800">แก้ไขแล้วเมื่อ {{ $item->resolved_at->format('d/m/Y H:i') }}</p>@endif
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center shadow-sm">
                    <p class="font-medium text-slate-800">ไม่พบรายการครุภัณฑ์</p>
                    <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนคำค้นหาหรือตัวกรองสถานะ</p>
                </div>
            @endforelse
        </section>

        @if ($items->hasPages())
            <div>{{ $items->links() }}</div>
        @endif
    </div>
@endsection
