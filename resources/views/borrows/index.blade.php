@extends('layouts.app', [
    'title' => 'รายการคำขอยืม-คืนครุภัณฑ์',
])

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2>ระบบบริหารจัดการการยืม-คืนครุภัณฑ์</h2>

        <!-- ตัวกรองสถานะ -->
        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm text-xs">
            <a href="{{ route('admin.borrows.index') }}" class="px-3 py-1.5 rounded-md font-medium transition {{ !request('status') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-indigo-600' }}">
                ทั้งหมด
            </a>
            <a href="{{ route('admin.borrows.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-md font-medium transition {{ request('status') === 'pending' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-indigo-600' }}">
                รอดำเนินการ
            </a>
            <a href="{{ route('admin.borrows.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-md font-medium transition {{ request('status') === 'approved' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-indigo-600' }}">
                กำลังยืม
            </a>
            <a href="{{ route('admin.borrows.index', ['status' => 'returned']) }}" class="px-3 py-1.5 rounded-md font-medium transition {{ request('status') === 'returned' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:text-indigo-600' }}">
                ส่งคืนแล้ว
            </a>
        </div>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table class="table-data">
                    <thead>
                        <tr>
                            <th>ผู้ยื่นคำขอ</th>
                            <th>ครุภัณฑ์</th>
                            <th>ช่วงเวลายืม</th>
                            <th>หมายเหตุ / เหตุผล</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-right">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $record->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $record->user->email }}</div>
                                </td>
                                <td>
                                    <div class="asset-code-tag">{{ $record->asset->asset_code }}</div>
                                    <a href="{{ route('assets.show', $record->asset_id) }}" class="font-medium text-slate-800 hover:text-indigo-600 transition">
                                        {{ $record->asset->name }}
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $record->borrow_date->format('d/m/Y') }} &rarr; {{ $record->expected_return_date->format('d/m/Y') }}</div>
                                    @if ($record->actual_return_date)
                                        <div class="text-xs text-emerald-600 mt-0.5">คืนเมื่อ: {{ $record->actual_return_date->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="text-xs text-slate-500 max-w-xs truncate">
                                    {{ $record->note ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($record->status === 'pending')
                                        <span class="badge badge-warning">
                                            <span class="badge-dot"></span> รอดำเนินการ
                                        </span>
                                    @elseif ($record->status === 'approved')
                                        <span class="badge badge-info">
                                            <span class="badge-dot"></span> อนุมัติ (กำลังยืม)
                                        </span>
                                    @elseif ($record->status === 'returned')
                                        <span class="badge badge-success">
                                            <span class="badge-dot"></span> ส่งคืนเรียบร้อย
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <span class="badge-dot"></span> ปฏิเสธคำขอ
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right space-x-2">
                                    @can('approve', $record)
                                        <!-- ปุ่มอนุมัติ -->
                                        <form action="{{ route('admin.borrows.approve', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันอนุมัติการยืมครุภัณฑ์ชิ้นนี้?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                อนุมัติ
                                            </button>
                                        </form>
                                    @endcan

                                    @can('reject', $record)
                                        <!-- ปุ่มปฏิเสธ -->
                                        <form action="{{ route('admin.borrows.reject', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันปฏิเสธคำขอนี้?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger-light btn-sm">
                                                ปฏิเสธ
                                            </button>
                                        </form>
                                    @endcan

                                    @can('return', $record)
                                        <!-- ปุ่มรับคืน -->
                                        <form action="{{ route('admin.borrows.return', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('ยืนยันการรับคืนครุภัณฑ์ชิ้นนี้เข้าสู่ระบบ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                บันทึกรับคืน
                                            </button>
                                        </form>
                                    @endcan

                                    @if ($record->status === 'returned' || $record->status === 'rejected')
                                        <span class="text-xs text-slate-400">เสร็จสิ้น</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    ไม่พบรายการคำขอยืม-คืนตามเงื่อนไข
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $records->links() }}
            </div>
        </div>
    </div>
@endsection
