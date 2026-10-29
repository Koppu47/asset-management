@extends('layouts.app', [
    'title' => 'Dashboard',
])

@section('header')
    <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-slate-800 mb-2">ยินดีต้อนรับสู่ระบบจัดการครุภัณฑ์</h3>
            <p class="text-slate-600">{{ __("You're logged in!") }}</p>
        </div>
    </div>
@endsection
