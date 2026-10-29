@extends('layouts.app', [
    'title' => 'จัดการผู้ใช้งาน',
])

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2>จัดการผู้ใช้งาน</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if (Route::has('admin.users.create'))
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    เพิ่มผู้ใช้งานใหม่
                </a>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
                กลับภาพรวมระบบ
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- สรุปสถิติผู้ใช้งาน --}}
        <section aria-label="สรุปสถิติผู้ใช้งาน" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 border-l-4 border-l-indigo-500 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">ผู้ใช้งานทั้งหมด</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['total']) }}</p>
                <p class="mt-1 text-xs text-slate-400">บัญชีที่ลงทะเบียนในระบบ</p>
            </div>

            <div class="rounded-2xl border border-violet-200 border-l-4 border-l-violet-500 bg-violet-50/70 p-5 shadow-sm">
                <p class="text-sm font-medium text-violet-800">ผู้ดูแลระบบ (Admin)</p>
                <p class="mt-2 text-3xl font-bold text-violet-900">{{ number_format($stats['admins']) }}</p>
                <p class="mt-1 text-xs text-violet-700">มีสิทธิ์จัดการครุภัณฑ์และระบบ</p>
            </div>

            <div class="rounded-2xl border border-sky-200 border-l-4 border-l-sky-500 bg-sky-50/70 p-5 shadow-sm">
                <p class="text-sm font-medium text-sky-800">พนักงานทั่วไป (Staff)</p>
                <p class="mt-2 text-3xl font-bold text-sky-900">{{ number_format($stats['users']) }}</p>
                <p class="mt-1 text-xs text-sky-700">สิทธิ์ยืม-คืนและดูแคตตาล็อก</p>
            </div>
        </section>

        {{-- ส่วนค้นหาและกรองข้อมูล --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1 max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ $search }}" placeholder="ค้นหาชื่อ หรืออีเมล..."
                                class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>

                        <div class="w-full sm:w-44">
                            <select name="role" class="w-full py-2 px-3 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                <option value="">ทุกระดับสิทธิ์</option>
                                <option value="admin" @selected($role === 'admin')>ผู้ดูแลระบบ (Admin)</option>
                                <option value="user" @selected($role === 'user')>พนักงาน (User)</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                ค้นหา
                            </button>
                            @if (!empty($search) || !empty($role))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                                    ล้างตัวกรอง
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ตารางแสดงรายชื่อผู้ใช้งาน --}}
        <div class="card overflow-hidden">
            <div class="table-container">
                <table class="table-data min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="px-5 py-3.5 text-left">ผู้ใช้งาน</th>
                            <th class="px-5 py-3.5 text-left">อีเมล</th>
                            <th class="px-5 py-3.5 text-center">ระดับสิทธิ์</th>
                            <th class="px-5 py-3.5 text-center">ประวัติการยืม</th>
                            <th class="px-5 py-3.5 text-left">วันที่สมัคร</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-600 ring-1 ring-slate-200">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 flex items-center gap-2">
                                                {{ $user->name }}
                                                @if (auth()->id() === $user->id)
                                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-200">บัญชีของคุณ</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500 sm:hidden">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $user->email }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if ($user->isAdmin())
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-800 ring-1 ring-violet-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-violet-600"></span>
                                            ผู้ดูแลระบบ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            พนักงานทั่วไป
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    @if ($user->borrow_records_count > 0)
                                        <span class="font-semibold text-slate-800">{{ $user->borrow_records_count }}</span>
                                        <span class="text-xs text-slate-500">ครั้ง</span>
                                    @else
                                        <span class="text-slate-400 text-xs">ยังไม่เคยมียืม</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-500 whitespace-nowrap text-xs">
                                    {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="mt-3 text-base font-medium text-slate-700">ไม่พบรายชื่อผู้ใช้งาน</p>
                                        <p class="mt-1 text-sm text-slate-500">ลองปรับคำค้นหาหรือตัวกรองระดับสิทธิ์ใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
