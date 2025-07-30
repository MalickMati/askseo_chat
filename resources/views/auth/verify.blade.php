@extends('layouts.auth-layout')

@section('title', 'OTP Verification | ASK SEO')

@section('form')

    <x-login_header message="Verify OPT sent to your email"></x-login_header>

    <div class="auth-form">
        <div class="form-title">
            <h1>Enter Verification Code</h1>
            <p>We've sent a 6-digit code to your email<br>{{ $email }}</p>
        </div>

        <form id="otpForm" autocomplete="off">
            @csrf
            <div class="otp-container" id="otpContainer">
                <input type="text" class="otp-input" maxlength="1" data-index="0" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" data-index="1" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" data-index="2" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" data-index="3" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" data-index="4" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="otp-input" maxlength="1" data-index="5" inputmode="numeric" pattern="[0-9]*">
            </div>

            <div class="error-message" id="otpError">Please enter a valid 6-digit code</div>

            <div class="resend-otp">
                Didn't receive code? <span class="timer" id="resendTimer">{{ gmdate('i:s', $secondsRemaining) }}</span>
                <a href="/resend/otp" id="resendLink" style="display: none;">Resend Code</a>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="btn-text">Verify & Continue</span>
                <svg class="spinner" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                        fill="white" />
                </svg>
            </button>
        </form>

        <div class="auth-switch">
            Need help? <a href="#">Contact support</a>
        </div>
    </div>

@endsection

@section('js_file')
    <script>
        const verifyUrl = "/verify/otp";
        const timeremaining = "{{ $secondsRemaining }}"
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // DOM Elements
            const otpForm = document.getElementById('otpForm');
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpContainer = document.getElementById('otpContainer');
            const otpError = document.getElementById('otpError');
            const submitBtn = document.getElementById('submitBtn');
            const resendTimer = document.getElementById('resendTimer');
            const resendLink = document.getElementById('resendLink');

            // Timer variables
            let timeLeft = Math.floor(timeremaining);
            let timerInterval;

            // Start the countdown timer
            function startTimer() {
                clearInterval(timerInterval);
                timeLeft = Math.floor(timeremaining);
                resendLink.style.display = 'none';
                resendTimer.style.display = 'inline';

                timerInterval = setInterval(() => {
                    timeLeft--;

                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    resendTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        resendTimer.style.display = 'none';
                        resendLink.style.display = 'inline';
                    }
                }, 1000);
            }

            // Initialize timer
            startTimer();

            // Handle OTP input navigation
            otpInputs.forEach((input, index) => {
                // Focus on first input initially
                if (index === 0) {
                    input.focus();
                }

                // Handle input
                input.addEventListener('input', (e) => {
                    const value = e.target.value;

                    // Only allow numeric input
                    if (/^[0-9]$/.test(value)) {
                        e.target.value = value;

                        // Move to next input if available
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        e.target.value = '';
                    }

                    // Remove error state when typing
                    otpContainer.classList.remove('error');
                    otpError.style.display = 'none';
                });

                // Handle backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text/plain').trim();

                    if (/^[0-9]{6}$/.test(pasteData)) {
                        // Fill all inputs with pasted code
                        for (let i = 0; i < otpInputs.length; i++) {
                            if (i < pasteData.length) {
                                otpInputs[i].value = pasteData[i];
                            }
                        }

                        // Focus on last input
                        otpInputs[otpInputs.length - 1].focus();
                    }
                });

                // Handle focus
                input.addEventListener('focus', () => {
                    input.classList.add('active');
                });

                // Handle blur
                input.addEventListener('blur', () => {
                    input.classList.remove('active');
                });
            });

            // Resend OTP link
            resendLink.addEventListener('click', (e) => {
                e.preventDefault();

                // Simulate resend
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending new code...';

                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Verify & Continue';
                    startTimer();

                    // Show success message (in a real app, you'd show this properly)
                    alert('New verification code sent!');
                }, 1000);
            });

            // Form submission
            otpForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Collect OTP code
                let otpCode = '';
                let isValid = true;

                otpInputs.forEach(input => {
                    otpCode += input.value;
                    if (!input.value) {
                        isValid = false;
                    }
                });

                // Validate OTP
                if (!isValid || otpCode.length !== 6 || !/^[0-9]{6}$/.test(otpCode)) {
                    otpContainer.classList.add('error');
                    otpError.style.display = 'block';
                    return;
                }

                // Show loading state
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;

                // Send OTP via AJAX to Laravel
                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        otp: otpCode
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.classList.remove('loading');

                        if (data.success) {
                            submitBtn.innerHTML = '<svg height="20" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none"><g fill="#eee"><path d="M11.28 5.72a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0l-1.5-1.5a.75.75 0 0 1 1.06-1.06l.97.97 3.47-3.47a.75.75 0 0 1 1.06 0"/><path fill-rule="evenodd" d="M6.402 1.22a2.75 2.75 0 0 1 3.196 0l.69.493c.154.11.332.184.519.215l.865.146a2.75 2.75 0 0 1 2.254 2.254l.146.866c.031.187.105.364.215.518l.493.69a2.75 2.75 0 0 1 0 3.197l-.493.69c-.11.154-.184.33-.215.518l-.146.865a2.75 2.75 0 0 1-2.254 2.254l-.865.146a1.25 1.25 0 0 0-.519.216l-.69.492a2.75 2.75 0 0 1-3.196 0l-.69-.492a1.25 1.25 0 0 0-.519-.216l-.865-.146a2.75 2.75 0 0 1-2.254-2.254l-.146-.865a1.25 1.25 0 0 0-.215-.519l-.493-.69a2.75 2.75 0 0 1 0-3.196l.493-.69a1.25 1.25 0 0 0 .215-.518l.146-.866a2.75 2.75 0 0 1 2.254-2.254l.865-.146a1.25 1.25 0 0 0 .519-.215zm2.324 1.22a1.25 1.25 0 0 0-1.453 0l-.69.493a2.75 2.75 0 0 1-1.14.474l-.865.146a1.25 1.25 0 0 0-1.025 1.025l-.146.865a2.75 2.75 0 0 1-.474 1.141l-.492.69a1.25 1.25 0 0 0 0 1.453l.492.69c.243.339.405.729.474 1.14l.146.865c.089.525.5.936 1.025 1.025l.865.146c.411.07.801.232 1.14.474l.69.493a1.25 1.25 0 0 0 1.454 0l.69-.493a2.75 2.75 0 0 1 1.14-.474l.865-.146a1.25 1.25 0 0 0 1.025-1.024l.146-.866a2.75 2.75 0 0 1 .474-1.14l.492-.69a1.25 1.25 0 0 0 0-1.453l-.492-.69a2.75 2.75 0 0 1-.474-1.14l-.146-.866a1.25 1.25 0 0 0-1.025-1.025l-.865-.146a2.75 2.75 0 0 1-1.14-.474z" clip-rule="evenodd"/></g></svg> Verified';
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000)
                        } else {
                            otpContainer.classList.add('error');
                            otpError.textContent = data.message || 'OTP verification failed.';
                            otpError.style.display = 'block';
                            submitBtn.disabled = false;

                            if (data.redirect) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Redirecting...';
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 2000);
                            }
                        }
                    })
                    .catch(error => {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                        console.error('Verification error:', error);
                        otpError.textContent = 'Something went wrong. Please try again.';
                        otpError.style.display = 'block';
                    });
            });
        });
    </script>
@endsection