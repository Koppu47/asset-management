@extends('layouts.guest')

@section('title', 'ลืมรหัสผ่าน (Forgot Password)')

@section('content')
    <div class="mb-4 text-sm text-slate-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-slate-600 hover:text-slate-900 underline" href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>

            <button type="submit" class="btn btn-primary">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>
@endsection
