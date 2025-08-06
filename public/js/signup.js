document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const signupForm = document.getElementById('signupForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('strengthMeter');
    const submitBtn = document.getElementById('submitBtn');
    const confirmPassword = document.getElementById('confirmPassword');

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

    // Password strength meter
    passwordInput.addEventListener('input', function () {
        const password = this.value;
        let strength = 0;

        // Check password length
        if (password.length >= 8) strength++;

        // Check for mixed case
        if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength++;

        // Check for numbers
        if (password.match(/([0-9])/)) strength++;

        // Check for special chars
        if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength++;

        // Update strength meter
        strengthMeter.setAttribute('data-strength', strength);

        // Validate password
        validatePassword();
    });

    // Confirm password validation
    confirmPassword.addEventListener('input', validatePassword);

    function validatePassword() {
        const password = passwordInput.value;
        const confirm = confirmPassword.value;
        const passwordGroup = passwordInput.closest('.form-group');
        const confirmGroup = confirmPassword.closest('.form-group');

        // Reset states
        passwordGroup.classList.remove('error', 'success');
        confirmGroup.classList.remove('error', 'success');

        // Validate password
        if (password.length > 0) {
            if (password.length < 8) {
                passwordGroup.classList.add('error');
            } else {
                passwordGroup.classList.add('success');
            }
        }

        // Validate password match
        if (confirm.length > 0) {
            if (password !== confirm) {
                confirmGroup.classList.add('error');
            } else if (password.length >= 8) {
                confirmGroup.classList.add('success');
            }
        }
    }

    // Form submission
    signupForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        const formData = new FormData(signupForm);

        try {
            const response = await fetch(signupUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;

            const msgDiv = document.getElementById('responseMessage');
            if (result.success) {
                msgDiv.innerHTML = `<p style="color: green; text-align: centre;">${result.message}</p>`;
                signupForm.reset();
                strengthMeter.setAttribute('data-strength', 0);
                window.location.href = result.redirect;
            } else {
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('<br>');
                    msgDiv.innerHTML = `<p style="color: red; text-align: centre;">${errorMessages}</p>`;
                } else {
                    msgDiv.innerHTML = `<p style="color: red;">${result.message}</p>`;
                }
            }
        } catch (error) {
            console.error(error);
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            document.getElementById('responseMessage').innerHTML = `<p style="color: red;">Request failed.</p>`;
        }
    });

});