@extends('layouts.app', [
    'title' => 'ทะเบียนครุภัณฑ์',
])

@section('header')
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1>ทะเบียนครุภัณฑ์</h1>
        </div>
        @can('create', App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มครุภัณฑ์
            </a>
        @endcan
    </div>
@endsection

@section('content')

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-labelledby="asset-search-heading">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="asset-search-heading" class="text-base font-semibold text-slate-900">ค้นหาครุภัณฑ์</h2>
                        <p class="mt-1 text-sm text-slate-500">ค้นหาจากชื่อ รหัส หรือหมายเลขเครื่อง
                            แล้วเลือกตัวกรองที่ต้องการ</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">ทั้งหมด
                        {{ number_format($assets->total()) }} รายการ</span>
                </div>
            </div>

            <form action="{{ route('assets.index') }}" method="GET" class="grid gap-4 p-5 sm:p-6 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700">คำค้นหา</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="form-input min-h-11 w-full rounded-xl border-slate-300 px-4 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="lg:col-span-3">
                    <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">หมวดหมู่</label>
                    <select name="category_id" id="category_id"
                        class="form-select min-h-11 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">ทุกหมวดหมู่</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}
                                ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">สถานะ</label>
                    <select name="status" id="status"
                        class="form-select min-h-11 w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">ทุกสถานะ</option>
                        <option value="available" @selected(request('status') === 'available')>พร้อมใช้งาน</option>
                        <option value="borrowed" @selected(request('status') === 'borrowed')>กำลังถูกยืม</option>
                        <option value="maintenance" @selected(request('status') === 'maintenance')>ซ่อมบำรุง</option>
                        <option value="retired" @selected(request('status') === 'retired')>จำหน่ายออก</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit" class="btn btn-primary min-h-11 flex-1">ค้นหา</button>
                    <a href="{{ route('assets.index') }}" class="btn btn-outline min-h-11" aria-label="ล้างตัวกรอง">ล้าง</a>
                </div>
            </form>
        </section>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-slate-700">ผลการค้นหา <span
                    class="font-normal text-slate-500">{{ number_format($assets->total()) }} รายการ</span></h2>
            @if (request()->hasAny(['search', 'category_id', 'status']))
                <p class="text-xs text-slate-500">กำลังแสดงผลตามตัวกรองที่เลือก</p>
            @endif
        </div>

        {{-- ส่วนนี้เป็นการแสดงรายการครุภัณฑ์ที่ค้นหาได้ หากมีรายการจะแสดงในรูปแบบกริด หากไม่มีจะแสดงข้อความว่าไม่พบครุภัณฑ์ --}}
        @if ($assets->isNotEmpty())
            <div class="asset-grid grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($assets as $asset)
                    <article
                        class="asset-card group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-lg">
                        <a href="{{ route('assets.show', $asset) }}"
                            class="asset-card-thumb relative block h-44 overflow-hidden bg-slate-100"
                            aria-label="ดูรายละเอียด {{ $asset->name }}">
                            @if ($asset->image)
                                <img src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                            @else
                                <div
                                    class="asset-card-placeholder flex h-full flex-col items-center justify-center gap-2 text-slate-400">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm4 8 3-3 3 3 2-2 3 3M8 9h.01" />
                                    </svg>
                                    <span class="text-xs font-medium">ไม่มีรูปภาพ</span>
                                </div>
                            @endif
                            @if ($asset->category)
                                <span
                                    class="absolute left-3 top-3 max-w-[70%] truncate rounded-lg border border-white/70 bg-white/95 px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm">{{ $asset->category->name }}</span>
                            @endif
                        </a>

                        <div class="asset-card-body flex flex-1 flex-col p-4 sm:p-5">
                            @php
                                $statusStyles = [
                                    'available' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
                                    'borrowed' => 'bg-sky-50 text-sky-800 ring-sky-200',
                                    'maintenance' => 'bg-amber-50 text-amber-800 ring-amber-200',
                                    'retired' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                ];
                                $statusLabels = [
                                    'available' => '● พร้อมใช้งาน',
                                    'borrowed' => '● กำลังถูกยืม',
                                    'maintenance' => '● ซ่อมบำรุง',
                                    'retired' => '● จำหน่ายออก',
                                ];
                            @endphp

                            <div class="flex items-center justify-between gap-2">
                                <p class="font-mono text-xs font-semibold tracking-wide text-indigo-700">
                                    {{ $asset->asset_code }}</p>
                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusStyles[$asset->status] ?? $statusStyles['retired'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"
                                        aria-hidden="true"></span>{{ $statusLabels[$asset->status] ?? $asset->status }}
                                </span>
                            </div>

                            <h3 class="mt-2 line-clamp-2 min-h-12 text-base font-semibold leading-6 text-slate-900">
                                <a href="{{ route('assets.show', $asset) }}"
                                    class="rounded-sm hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $asset->name }}</a>
                            </h3>
                            <p class="mt-1 line-clamp-2 min-h-10 text-xs leading-5 text-slate-500">
                                {{ $asset->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}</p>
                            @auth
                                @if (Auth::user()->isAdmin())
                                    <p class="mt-2 truncate text-xs text-slate-500">
                                        {{ $asset->department ?: 'ยังไม่ระบุหน่วยงาน' }} ·
                                        {{ trim(($asset->building ?? 'ไม่ระบุอาคาร') . ' ' . ($asset->room ?? '')) }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">ผู้รับผิดชอบ:
                                        {{ $asset->custodian?->name ?? 'ยังไม่กำหนด' }}</p>
                                @endif
                            @endauth

                            <dl class="mt-3 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3 text-xs">
                                <div>
                                    <dt class="text-slate-400">มูลค่าจัดซื้อ</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">
                                        {{ number_format((float) $asset->price, 2) }} <span
                                            class="font-normal text-slate-500">บาท</span></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400">วันที่ตรวจรับ</dt>
                                    <dd class="mt-0.5 font-medium text-slate-800">
                                        {{ $asset->purchase_date->format('d/m/Y') }}</dd>
                                </div>
                            </dl>

                            <div
                                class="mt-auto flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3">
                                <a href="{{ route('assets.show', $asset) }}"
                                    class="inline-flex min-h-9 items-center gap-1.5 rounded-lg text-xs font-semibold text-indigo-700 transition hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    รายละเอียดและ QR
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m9 5 7 7-7 7" />
                                    </svg>
                                </a>
                                <div class="flex items-center gap-2">
                                    @can('update', $asset)
                                        <a href="{{ route('assets.edit', $asset) }}"
                                            class="rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900">แก้ไข</a>
                                    @endcan

                                    @can('borrow', $asset)
                                        <a href="{{ route('borrows.create', $asset) }}"
                                            class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">ขอยืม</a>
                                    @elseguest
                                        @if ($asset->isAvailable())
                                            <a href="{{ route('login') }}"
                                                class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">เข้าสู่ระบบเพื่อยืม</a>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pt-2">{{ $assets->links() }}</div>
        @else
            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                            d="m21 21-4.4-4.4m1.4-5.6a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <h2 class="mt-4 text-lg font-semibold text-slate-900">ไม่พบครุภัณฑ์ที่ตรงกับการค้นหา</h2>
                <p class="mt-1 text-sm text-slate-500">ลองเปลี่ยนคำค้นหาหรือปรับตัวกรอง แล้วค้นหาอีกครั้ง</p>
                <a href="{{ route('assets.index') }}"
                    class="mt-5 inline-flex min-h-10 items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">ล้างตัวกรอง</a>
            </section>
        @endif
    </div>
@endsection
