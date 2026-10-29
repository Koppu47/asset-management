@extends('layouts.app', [
    'title' => 'ประวัติการยืม-คืนครุภัณฑ์ของฉัน',
])

@section('header')
    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
        {{ __('ประวัติการยืม-คืนครุภัณฑ์ของฉัน') }}
    </h2>
@endsection

@section('content')
    @if ($returnReminders->isNotEmpty())
        <div class="alert alert-warning">
            <div>
                <h3 class="font-bold text-amber-950">แจ้งเตือนกำหนดคืนใกล้ถึง</h3>
                @foreach ($returnReminders as $reminder)
                    <p class="mt-1 text-sm text-amber-900">{{ $reminder->asset->name }} · กรุณาส่งคืนภายใน {{ $reminder->expected_return_date->format('d/m/Y') }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table class="table-data">
                    <thead>
                        <tr>
                            <th>รหัส / ครุภัณฑ์</th>
                            <th>หมวดหมู่</th>
                            <th>ระยะเวลาที่ขอยืม</th>
                            <th>วันที่ส่งคืนจริง</th>
                            <th class="text-center">สถานะคำขอ</th>
                            <th class="text-right">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td>
                                    <div class="asset-code-tag">{{ $record->asset->asset_code }}</div>
                                    <a href="{{ route('assets.show', $record->asset_id) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition">
                                        {{ $record->asset->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $record->asset->category->name }}
                                </td>
                                <td>
                                    {{ $record->borrow_date->format('d/m/Y') }} &rarr; {{ $record->expected_return_date->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $record->actual_return_date ? $record->actual_return_date->format('d/m/Y') : '-' }}
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
                                            <span class="badge-dot"></span> ไม่อนุมัติ
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @can('cancel', $record)
                                        <form action="{{ route('borrows.cancel', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('ต้องการยกเลิกคำขอนี้ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-light btn-sm">
                                                ยกเลิกคำขอ
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    คุณยังไม่เคยมีประวัติการยืมครุภัณฑ์ในระบบ
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
