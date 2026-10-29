@extends('layouts.app', [
    'title' => 'ลงทะเบียนครุภัณฑ์ใหม่',
])

@section('header')
    <div class="flex items-center space-x-3">
        <a href="{{ route('assets.index') }}" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            ย้อนกลับ
        </a>
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('ลงทะเบียนครุภัณฑ์ใหม่') }}
        </h2>
    </div>
@endsection

@section('content')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 p-8">
            <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- หมวดหมู่ -->
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-1">
                                หมวดหมู่ครุภัณฑ์ <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="">-- กรุณาเลือกหมวดหมู่ --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                            <input type="text" name="asset_code" id="asset_code" value="{{ old('asset_code') }}" required
                                placeholder="เช่น IT-2026-003, FURN-2026-010"
                                class="w-full rounded-lg border-slate-300 font-mono uppercase focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- ชื่อครุภัณฑ์ -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">
                                ชื่อครุภัณฑ์ <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                placeholder="เช่น แท็บเล็ต Apple iPad Air 11 นิ้ว M2"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- หมายเลขเครื่อง Serial -->
                        <div>
                            <label for="serial_number" class="block text-sm font-semibold text-slate-700 mb-1">
                                Serial Number / หมายเลขประจำเครื่อง
                            </label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}"
                                placeholder="ระบุหมายเลขเครื่อง (ถ้ามี)"
                                class="w-full rounded-lg border-slate-300 font-mono focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- สถานะเริ่มต้น -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">
                                สถานะเริ่มต้น <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                                <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>พร้อมใช้งาน (Available)</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>ซ่อมบำรุง (Maintenance)</option>
                                <option value="retired" {{ old('status') === 'retired' ? 'selected' : '' }}>จำหน่ายออก (Retired)</option>
                            </select>
                        </div>

                        <!-- ราคาจัดซื้อ -->
                        <div>
                            <label for="department" class="block text-sm font-semibold text-slate-700 mb-1">หน่วยงาน/แผนก</label>
                            <input type="text" name="department" id="department" value="{{ old('department') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="เช่น ฝ่ายเทคโนโลยีสารสนเทศ">
                        </div>
                        <div>
                            <label for="custodian_user_id" class="block text-sm font-semibold text-slate-700 mb-1">ผู้รับผิดชอบ</label>
                            <select name="custodian_user_id" id="custodian_user_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- ยังไม่กำหนด --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('custodian_user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="building" class="block text-sm font-semibold text-slate-700 mb-1">อาคาร</label>
                            <input type="text" name="building" id="building" value="{{ old('building') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="room" class="block text-sm font-semibold text-slate-700 mb-1">ห้อง/พื้นที่</label>
                            <input type="text" name="room" id="room" value="{{ old('room') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- ราคาจัดซื้อ -->
                        <div>
                            <label for="price" class="block text-sm font-semibold text-slate-700 mb-1">
                                มูลค่าจัดซื้อ (บาท) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" required
                                placeholder="0.00"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- วันที่ตรวจรับ -->
                        <div>
                            <label for="purchase_date" class="block text-sm font-semibold text-slate-700 mb-1">
                                วันที่จัดซื้อ / ตรวจรับ <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">
                        </div>

                        <!-- อัปโหลดรูปภาพ -->
                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-semibold text-slate-700 mb-1">
                                รูปถ่ายครุภัณฑ์ (ไม่บังคับ, ไฟล์ JPG/PNG/WebP ไม่เกิน 2MB)
                            </label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <!-- รายละเอียด -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">
                                รายละเอียด / คุณลักษณะจำเพาะ
                            </label>
                            <textarea name="description" id="description" rows="4"
                                placeholder="ระบุสเปกอุปกรณ์ สภาพ หรือข้อความระวังการใช้งาน..."
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('assets.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 shadow-sm transition">
                            บันทึกข้อมูลครุภัณฑ์
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
