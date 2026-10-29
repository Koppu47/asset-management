@extends('layouts.app', [
    'title' => 'สำรวจครุภัณฑ์ประจำปี',
])

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-800">สำรวจครุภัณฑ์ประจำปี</h2>
            <p class="mt-1 text-sm text-slate-500">ตรวจสอบว่าครุภัณฑ์ในทะเบียนมีอยู่จริง และบันทึกผลเป็นรายปี</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">กลับภาพรวมระบบ</a>
    </div>
@endsection

@section('content')

    <div class="space-y-6">
        <section class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5 sm:p-6">
            <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">เริ่มรอบสำรวจใหม่</h3>
                    <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                        ระบบจะบันทึกรายการครุภัณฑ์ที่มีอยู่ในขณะเริ่มรอบ จากนั้นตรวจทีละรายการว่า “พบ” หรือ “ไม่พบ”
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.inventory-surveys.store') }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    @csrf
                    <div>
                        <label for="year" class="mb-1 block text-sm font-medium text-slate-700">ปีสำรวจ</label>
                        <input id="year" name="year" type="number" min="2000" max="{{ now()->year }}" value="{{ old('year', now()->year) }}" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-32">
                    </div>
                    <button type="submit" class="inline-flex justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                        เริ่มสำรวจ
                    </button>
                </form>
            </div>
            @error('year')
                <p class="mt-3 text-sm font-medium text-rose-700">{{ $message }}</p>
            @enderror
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="font-semibold text-slate-900">ประวัติรอบสำรวจ</h3>
            </div>

            @if ($surveys->isEmpty())
                <div class="px-5 py-12 text-center">
                    <p class="font-medium text-slate-800">ยังไม่มีรอบสำรวจ</p>
                    <p class="mt-1 text-sm text-slate-500">เริ่มรอบใหม่เพื่อบันทึกผลตรวจครุภัณฑ์ประจำปี</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table-data min-w-full divide-y divide-slate-200 text-left text-sm w-full">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3.5 font-semibold w-24">ปีสำรวจ</th>
                                <th class="px-5 py-3.5 font-semibold">ความคืบหน้า</th>
                                <th class="px-5 py-3.5 font-semibold w-1/4">ผู้เริ่มรอบ</th>
                                <th class="px-5 py-3.5 font-semibold w-1/6">สถานะ</th>
                                <th class="px-5 py-3.5 text-right font-semibold w-32">รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($surveys as $survey)
                                @php($checkedCount = $survey->found_count + $survey->missing_count)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $survey->year }}</td>
                                    <td class="min-w-56 px-5 py-4">
                                        <div class="flex items-center justify-between gap-3 text-xs">
                                            <span>{{ $checkedCount }} / {{ $survey->items_count }} รายการ</span>
                                            <span>พบ {{ $survey->found_count }} · ไม่พบ {{ $survey->missing_count }}</span>
                                        </div>
                                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-indigo-600" style="width: {{ $survey->items_count > 0 ? min(100, ($checkedCount / $survey->items_count) * 100) : 100 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">{{ $survey->startedBy?->name ?? 'ไม่ทราบ' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($survey->isComplete())
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">ปิดรอบแล้ว</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">กำลังสำรวจ</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('admin.inventory-surveys.show', $survey) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">เปิดรอบสำรวจ</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-5 py-4">{{ $surveys->links() }}</div>
            @endif
        </section>
    </div>
@endsection
