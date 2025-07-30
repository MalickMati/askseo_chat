@extends('layouts.auth-layout')

@section('title', 'Signup | ASK SEO')


@section('form')
    <x-login_header message="Create your organization account"></x-login_header>

    <div class="auth-form">
        <form id="signupForm" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <div class="input-wrapper">
                    <input type="text" id="fullName" name="name" class="form-control" placeholder="Your Name"
                        value="{{ old('name') }}" required>
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4m0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="error-message">Please enter your full name</div>
                @error('name')
                    <div class="berror-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Work Email</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="form-control" placeholder="hello@askseo.com"
                        value="{{ old('email') }}" required>
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 4-8 5-8-5V6l8 5 8-5z"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="error-message">Please enter a valid work email</div>
                @error('email')
                    <div class="berror-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Create a password" required>
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2m-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2M9 6v2h6V6c0-1.66-1.34-3-3-3S9 4.34 9 6"
                            fill="currentColor" />
                    </svg>
                    <svg class="password-toggle" id="togglePassword" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 6a9.77 9.77 0 0 1 8.82 5.5A9.77 9.77 0 0 1 12 17a9.77 9.77 0 0 1-8.82-5.5A9.77 9.77 0 0 1 12 6m0-2C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4m0 5a2.5 2.5 0 0 1 0 5 2.5 2.5 0 0 1 0-5m0-2c-2.48 0-4.5 2.02-4.5 4.5S9.52 16 12 16s4.5-2.02 4.5-4.5S14.48 7 12 7"
                            fill="currentColor" />
                    </svg>
                </div>
                <div class="strength-meter" id="strengthMeter" data-strength="0"></div>
                <div class="error-message">Password must be at least 8 characters</div>
                @error('password')
                    <div class="berror-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password_confirmation" id="confirmPassword" class="form-control"
                        placeholder="Confirm your password" required>
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor" />
                    </svg>
                </div>
                <div class="error-message">Passwords don't match</div>
                @error('password')
                    <div class="berror-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="terms-check">
                <input type="checkbox" id="terms" required>
                <label for="terms" class="terms-text">
                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </label>
            </div>

            @if (session('success'))
                <div class="success-alert">{{ session('success') }}</div>
            @endif

            <div id="responseMessage" class="alert"></div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="btn-text">Create Account</span>
                <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 4a8 8 0 0 1 7.89 6.7 1.53 1.53 0 0 0 1.49 1.3 1.5 1.5 0 0 0 1.48-1.75 11 11 0 0 0-21.72 0A1.5 1.5 0 0 0 2.62 12a1.53 1.53 0 0 0 1.49-1.3A8 8 0 0 1 12 4"
                        fill="#fff" />
                </svg>
            </button>
        </form>

        <div class="auth-switch">
            Already have an account? <a href="/auth/login">Sign in</a>
        </div>
    </div>
@endsection

@section('js_file')
    <script>
        const signupUrl = "/signup";
    </script>
    <script src="{{ asset('js/signup.js') }}"></script>
@endsection