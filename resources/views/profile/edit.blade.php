@extends('layouts.app', [
    'title' => 'Profile',
])

@section('header')
    <h2 class="font-semibold text-xl text-slate-800 leading-tight">
        {{ __('Profile') }}
    </h2>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="card">
            <div class="card-body max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="card-body max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
