@extends('layouts.app', [
    'title' => 'จัดการหมวดหมู่ครุภัณฑ์',
])

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('จัดการหมวดหมู่ครุภัณฑ์') }}
        </h2>
        @can('create', App\Models\Category::class)
            <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มหมวดหมู่ใหม่
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table class="table-data">
                    <thead>
                        <tr>
                            <th>รหัสย่อ</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>คำอธิบาย</th>
                            <th class="text-center">จำนวนครุภัณฑ์</th>
                            <th class="text-right">การกระทำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $category->code }}
                                    </span>
                                </td>
                                <td class="font-semibold text-slate-900">
                                    {{ $category->name }}
                                </td>
                                <td class="text-slate-500 max-w-xs truncate">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="text-center font-semibold text-slate-700">
                                    {{ $category->assets_count }} รายการ
                                </td>
                                <td class="text-right space-x-2">
                                    @can('update', $category)
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline btn-sm">
                                            แก้ไข
                                        </a>
                                    @endcan
                                    @can('delete', $category)
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('ยืนยันการลบหมวดหมู่ \'{{ $category->name }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-light btn-sm ml-2">
                                                ลบ
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    ยังไม่มีข้อมูลหมวดหมู่ในระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
