@extends('layouts.guest')

@section('title', 'ลงทะเบียนเข้าใช้งาน')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-slate-600 hover:text-slate-900 underline" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="btn btn-primary">
                {{ __('Register') }}
            </button>
        </div>
    </form>
@endsection
