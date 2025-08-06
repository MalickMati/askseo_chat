document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const loginForm = document.getElementById('loginForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle password visibility
    togglePassword.addEventListener('click', function (e) {
        e.preventDefault();
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('active');

        // Change icon
        if (type === 'password') {
            this.innerHTML = `<path d="M12 6C15.79 6 19.17 8.13 20.82 11.5C19.17 14.87 15.79 17 12 17C8.21 17 4.83 14.87 3.18 11.5C4.83 8.13 8.21 6 12 6ZM12 4C7 4 2.73 7.11 1 11.5C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 11.5C21.27 7.11 17 4 12 4ZM12 9C13.38 9 14.5 10.12 14.5 11.5C14.5 12.88 13.38 14 12 14C10.62 14 9.5 12.88 9.5 11.5C9.5 10.12 10.62 9 12 9ZM12 7C9.52 7 7.5 9.02 7.5 11.5C7.5 13.98 9.52 16 12 16C14.48 16 16.5 13.98 16.5 11.5C16.5 9.02 14.48 7 12 7Z" fill="currentColor"/>`;
        } else {
            this.innerHTML = `<path d="M12 6C15.79 6 19.17 8.13 20.82 11.5C19.17 14.87 15.79 17 12 17C8.21 17 4.83 14.87 3.18 11.5C4.83 8.13 8.21 6 12 6ZM12 4C7 4 2.73 7.11 1 11.5C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 11.5C21.27 7.11 17 4 12 4ZM12 9C13.38 9 14.5 10.12 14.5 11.5C14.5 12.88 13.38 14 12 14C10.62 14 9.5 12.88 9.5 11.5C9.5 10.12 10.62 9 12 9ZM12 7C9.52 7 7.5 9.02 7.5 11.5C7.5 13.98 9.52 16 12 16C14.48 16 16.5 13.98 16.5 11.5C16.5 9.02 14.48 7 12 7Z" fill="currentColor"/>`;
        }
    });

    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.classList.add('light-theme');
    }

    // Form submission
    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        const formData = new FormData(loginForm);
        formData.append('remember', document.getElementById('remember').checked ? 'on' : '');

        try {
            const response = await fetch(loginUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const msgDiv = document.getElementById('responseMessage');

            if (!response.ok) {
                // Try to get JSON, fallback to plain error
                let errorMsg = 'Login failed.';

                try {
                    const errorResult = await response.json();
                    errorMsg = errorResult.message || errorMsg;
                } catch (jsonErr) {
                    console.warn('Non-JSON error response:', jsonErr);
                }

                msgDiv.innerHTML = `<p style="color: red;">${errorMsg}</p>`;
            } else {
                const result = await response.json();

                if (result.success) {
                    msgDiv.innerHTML = `<p style="color: green; text-align: center;">${result.message}</p>`;
                    loginForm.reset();
                    window.location.href = result.redirect;
                } else {
                    const errorMessages = result.errors
                        ? Object.values(result.errors).flat().join('<br>')
                        : result.message;
                    if(result.message === 'CSRF token mismatch.') {
                        window.location.reload();
                    }

                    msgDiv.innerHTML = `<p style="color: red; text-align: center;">${errorMessages}</p>`;
                    if(result.redirect) {
                        submitBtn.innerHTML = 'Redirecting...';
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 2000);
                    }
                }
            }

        } catch (error) {
            console.error(error);
            document.getElementById('responseMessage').innerHTML = `<p style="color: red;">Unexpected error occurred.</p>`;
        } finally {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }

    });
});