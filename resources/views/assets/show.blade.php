@extends('layouts.app', [
    'title' => "ครุภัณฑ์: {$asset->name}",
])

@section('header')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-3">
            <a href="{{ route('assets.index') }}" class="btn btn-outline btn-sm" aria-label="กลับไปแคตตาล็อก">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <span class="asset-code-tag">{{ $asset->asset_code }}</span>
                <h2 class="mt-1 break-words text-xl font-bold leading-tight tracking-tight text-slate-950 sm:text-2xl">
                    {{ $asset->name }}
                </h2>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
            @can('update', $asset)
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-outline btn-sm">
                    แก้ไขข้อมูล
                </a>
            @endcan
            @can('delete', $asset)
                <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันการลบรายการครุภัณฑ์นี้?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-light btn-sm">
                        ลบ
                    </button>
                </form>
            @endcan
        </div>
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
        <!-- ฝั่งซ้าย: ข้อมูลและรูปภาพ (2 คอลัมน์) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <!-- รูปภาพประกอบ -->
                <div class="relative flex h-64 items-center justify-center border-b border-slate-200 bg-slate-100 sm:h-80">
                    @if ($asset->image)
                        <img src="{{ asset('storage/' . $asset->image) }}" alt="{{ $asset->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-slate-300 flex flex-col items-center">
                            <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-slate-400 mt-2">ไม่มีรูปภาพประกอบ</span>
                        </div>
                    @endif
                </div>

                <!-- ข้อมูลคุณสมบัติ -->
                <div class="p-6">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Asset details</p>
                            <p class="mt-1 text-lg font-bold text-slate-950">
                            ข้อมูลครุภัณฑ์
                            @if ($asset->status === 'available')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">
                                    ● พร้อมให้ยืมใช้งาน
                                </span>
                            @elseif ($asset->status === 'borrowed')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-300 shadow-sm">
                                    ● ถูกยืมใช้งานอยู่
                                </span>
                            @elseif ($asset->status === 'maintenance')
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-300 shadow-sm">
                                    ● อยู่ระหว่างซ่อมบำรุง
                                </span>
                            @else
                                <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-slate-200 text-slate-800 shadow-sm">
                                    ● จำหน่ายออกจากระบบ
                                </span>
                            @endif
                            </p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $asset->category->name }}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            <span class="block text-xs font-medium text-slate-400 uppercase">หมวดหมู่</span>
                            <span class="font-semibold text-slate-800">{{ $asset->category->name }} ({{ $asset->category->code }})</span>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            <span class="block text-xs font-medium text-slate-400 uppercase">หมายเลขเครื่อง (Serial No.)</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $asset->serial_number ?? 'ไม่ระบุ' }}</span>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            <span class="block text-xs font-medium text-slate-400 uppercase">มูลค่าจัดซื้อ</span>
                            <span class="font-semibold text-slate-800">{{ number_format($asset->price, 2) }} บาท</span>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            <span class="block text-xs font-medium text-slate-400 uppercase">วันที่ตรวจรับ</span>
                            <span class="font-semibold text-slate-800">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    @auth
                        @if (Auth::user()->isAdmin())
                            <div class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                                <h4 class="font-semibold text-indigo-950">สถานที่และผู้รับผิดชอบ</h4>
                                <p class="mt-2 text-sm text-indigo-900">{{ $asset->department ?: 'ไม่ระบุหน่วยงาน' }} · {{ trim(($asset->building ?? 'ไม่ระบุอาคาร').' '.($asset->room ?? '')) }} · {{ $asset->custodian?->name ?? 'ยังไม่กำหนดผู้รับผิดชอบ' }}</p>
                            </div>
                            <div class="mb-6 rounded-xl border border-slate-200 p-4">
                                <h4 class="font-semibold text-slate-900">ประวัติการเปลี่ยนแปลง</h4>
                                @forelse ($asset->auditLogs->take(8) as $log)
                                    <div class="mt-3 border-t border-slate-100 pt-3 text-sm">
                                        <span class="font-medium">{{ $log->user?->name ?? 'ระบบ' }}</span> · {{ str_replace('_', ' ', $log->action) }}
                                        <span class="text-slate-500">· {{ $log->created_at->format('d/m/Y H:i') }}</span>
                                        @if ($log->new_values)<p class="mt-1 text-xs text-slate-500">{{ collect($log->new_values)->map(fn ($value, $key) => $key.': '.(is_array($value) ? json_encode($value) : $value))->implode(' · ') }}</p>@endif
                                    </div>
                                @empty
                                    <p class="mt-2 text-sm text-slate-500">ยังไม่มีประวัติ</p>
                                @endforelse
                            </div>
                        @endif
                    @endauth

                    <div class="mb-6">
                        <h4 class="text-xs font-semibold uppercase text-slate-400 mb-2">รายละเอียด / ข้อมูลจำเพาะ</h4>
                        <div class="p-4 bg-slate-50 rounded-lg text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $asset->description ?? 'ไม่มีรายละเอียดเพิ่มเติม' }}
                        </div>
                    </div>

                    <!-- ปุ่มส่งคำร้องขอยืม -->
                    @can('borrow', $asset)
                        <div class="pt-4 border-t border-slate-100">
                            <a href="{{ route('borrows.create', $asset->id) }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow transition">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                ยื่นคำร้องขอยืมครุภัณฑ์ชิ้นนี้
                            </a>
                        </div>
                    @elseguest
                        @if ($asset->isAvailable())
                            <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100 flex items-center justify-between">
                                <span class="text-sm text-indigo-900 font-medium">ต้องการยืมอุปกรณ์ชิ้นนี้ใช่หรือไม่?</span>
                                <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                                    เข้าสู่ระบบเพื่อขอยืม
                                </a>
                            </div>
                        @endif
                    @endcan
                </div>
            </div>

            <!-- ตารางประวัติการยืม-คืนของครุภัณฑ์ชิ้นนี้ -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-base text-slate-800">ประวัติการยืม-คืนของครุภัณฑ์ชิ้นนี้</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-data min-w-full divide-y divide-slate-200 text-sm w-full">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-600">
                            <tr>
                                <th class="px-6 py-3 text-left">ผู้ขอยืม</th>
                                <th class="px-6 py-3 text-left">วันที่ยืม - กำหนดคืน</th>
                                <th class="px-6 py-3 text-left">วันที่คืนจริง</th>
                                <th class="px-6 py-3 text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($asset->borrowRecords as $record)
                                <tr>
                                    <td class="px-6 py-3.5 font-medium text-slate-800">
                                        {{ $record->user->name }}
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">
                                        {{ $record->borrow_date->format('d/m/Y') }} &rarr; {{ $record->expected_return_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">
                                        {{ $record->actual_return_date ? $record->actual_return_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        @if($record->status === 'approved')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">กำลังยืม</span>
                                        @elseif($record->status === 'returned')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">คืนแล้ว</span>
                                        @elseif($record->status === 'pending')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">รอดำเนินการ</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">ไม่อนุมัติ</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs">
                                        ยังไม่เคยมีประวัติการยืมครุภัณฑ์ชิ้นนี้
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: QR Code & สติกเกอร์ (1 คอลัมน์) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm text-center">
                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 mb-4">
                    Third-Party Library: Simple QR Code
                </div>
                <h4 class="font-bold text-slate-800 text-base mb-1">QR Code ประจำครุภัณฑ์</h4>
                <p class="text-xs text-slate-500 mb-4">สแกนเพื่อเปิดดูข้อมูลครุภัณฑ์นี้บนมือถือได้ทันที</p>

                <!-- แสดง QR Code จาก Library -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl inline-block shadow-inner mb-4">
                    {!! $qrCode !!}
                </div>

                <div class="font-mono text-sm font-bold text-slate-800 mb-4 tracking-wider">
                    {{ $asset->asset_code }}
                </div>

                <a href="{{ route('assets.qr', $asset->id) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    พิมพ์สติกเกอร์ QR Code
                </a>
            </div>
        </div>
    </div>
@endsection
