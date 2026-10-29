@extends('layouts.guest')

@section('title', 'ยืนยันรหัสผ่าน (Confirm Password)')

@section('content')
    <div class="mb-4 text-sm text-slate-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="btn btn-primary">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
@endsection
