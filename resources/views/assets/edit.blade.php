@extends('layouts.app', [
    'title' => "แก้ไขข้อมูล: {$asset->name}",
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
            {{ __('แก้ไขข้อมูลครุภัณฑ์') }}: {{ $asset->name }}
        </h2>
    </div>
@endsection

@section('content')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 p-8">
            <form action="{{ route('assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- หมวดหมู่ -->
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-1">
                                หมวดหมู่ครุภัณฑ์ <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $asset->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- รหัสครุภัณฑ์ -->
                        <div>
                            <label for="asset_code" class="block text-sm font-semibold text-slate-700 mb-1">
                                รหัสครุภัณฑ์ (Asset Code) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required
                                class="w-full rounded-lg border-slate-300 font-mono uppercase focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- ชื่อครุภัณฑ์ -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">
                                ชื่อครุภัณฑ์ <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- หมายเลขเครื่อง Serial -->
                        <div>
                            <label for="serial_number" class="block text-sm font-semibold text-slate-700 mb-1">
                                Serial Number / หมายเลขประจำเครื่อง
                            </label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                                class="w-full rounded-lg border-slate-300 font-mono focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- สถานะ -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">
                                สถานะปัจจุบัน <span class="text-rose-500">*</span>
                            </label>
                            @if ($hasActiveLoan)
                                <input type="hidden" name="status" value="borrowed">
                                <p class="rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-800">
                                    ถูกยืมใช้งาน (Borrowed) — เปลี่ยนสถานะได้เมื่อบันทึกรับคืนแล้ว
                                </p>
                            @else
                                <select name="status" id="status" required
                                    class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                    <option value="available" {{ old('status', $asset->status) === 'available' ? 'selected' : '' }}>พร้อมใช้งาน (Available)</option>
                                    <option value="maintenance" {{ old('status', $asset->status) === 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง (Maintenance)</option>
                                    <option value="retired" {{ old('status', $asset->status) === 'retired' ? 'selected' : '' }}>จำหน่ายออก (Retired)</option>
                                </select>
                            @endif
                        </div>

                        <!-- ราคาจัดซื้อ -->
                        <div>
                            <label for="department" class="block text-sm font-semibold text-slate-700 mb-1">หน่วยงาน/แผนก</label>
                            <input type="text" name="department" id="department" value="{{ old('department', $asset->department) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="custodian_user_id" class="block text-sm font-semibold text-slate-700 mb-1">ผู้รับผิดชอบ</label>
                            <select name="custodian_user_id" id="custodian_user_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- ยังไม่กำหนด --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('custodian_user_id', $asset->custodian_user_id) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="building" class="block text-sm font-semibold text-slate-700 mb-1">อาคาร</label>
                            <input type="text" name="building" id="building" value="{{ old('building', $asset->building) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="room" class="block text-sm font-semibold text-slate-700 mb-1">ห้อง/พื้นที่</label>
                            <input type="text" name="room" id="room" value="{{ old('room', $asset->room) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- ราคาจัดซื้อ -->
                        <div>
                            <label for="price" class="block text-sm font-semibold text-slate-700 mb-1">
                                มูลค่าจัดซื้อ (บาท) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', $asset->price) }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- วันที่ตรวจรับ -->
                        <div>
                            <label for="purchase_date" class="block text-sm font-semibold text-slate-700 mb-1">
                                วันที่จัดซื้อ / ตรวจรับ <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- อัปโหลดรูปภาพใหม่ -->
                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-semibold text-slate-700 mb-1">
                                เปลี่ยนรูปถ่ายครุภัณฑ์ (หากไม่ต้องการเปลี่ยน ให้เว้นว่างไว้)
                            </label>
                            @if ($asset->image)
                                <div class="mb-3 flex items-center space-x-3">
                                    <img src="{{ asset('storage/' . $asset->image) }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                    <span class="text-xs text-slate-500">มีรูปภาพปัจจุบันอยู่ในระบบ</span>
                                </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/*"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <!-- รายละเอียด -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">
                                รายละเอียด / คุณลักษณะจำเพาะ
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">{{ old('description', $asset->description) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('assets.show', $asset->id) }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 shadow-sm transition">
                            อัปเดตข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
