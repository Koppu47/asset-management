@extends('layouts.guest')

@section('title', 'ตั้งรหัสผ่านใหม่ (Reset Password)')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
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

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="btn btn-primary">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
@endsection
