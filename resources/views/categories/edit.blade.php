@extends('layouts.app', [
    'title' => "แก้ไข: {$category->name}",
])

@section('header')
    <div class="flex items-center space-x-3">
        <a href="{{ route('categories.index') }}" class="btn btn-outline btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            ย้อนกลับ
        </a>
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('แก้ไขหมวดหมู่ครุภัณฑ์') }}: {{ $category->name }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="form-group">
                        <label for="name" class="form-label">
                            ชื่อหมวดหมู่ <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                            class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="code" class="form-label">
                            รหัสย่อหมวดหมู่ <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code', $category->code) }}" required
                            class="form-input uppercase font-mono">
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            คำอธิบายเพิ่มเติม
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="form-textarea">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline">
                            ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary">
                            อัปเดตข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
