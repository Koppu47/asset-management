@extends('layouts.guest')

@section('title', 'เข้าสู่ระบบ')

@section('content')
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mb-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 hover:text-slate-900 underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @else
                <div></div>
            @endif

            <button type="submit" class="btn btn-primary">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
@endsection
