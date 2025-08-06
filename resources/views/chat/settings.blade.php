@extends('layouts.chat-settings')

@section('title', 'User | Settings')

@section('form-section')
    <x-chat-settings.settings-header :message="'Update your personal information and preferences'" :heading="'Profile Settings'" />

    <form class="settings-form" id="settingsForm" autocomplete="off">
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Profile Information
            </div>

            <div class="form-group">
                <label for="profileImage">Profile Picture</label>
                <div class="avatar-upload">
                    <div class="avatar-preview">
                        <img src="{{ asset($img) }}" alt="Current Profile" id="avatarPreview">
                    </div>
                    <div class="avatar-upload-btn" id="uploadTrigger">
                        Change Photo
                        <input type="file" id="profileImage" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" class="form-control" value="{{ $name }}" required>
                <div class="error-message">Please enter your full name</div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" class="form-control" value="{{ $email }}" disabled>
                <div class="error-message">Please enter a valid email address</div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 6v4m0 4h.01"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Online Status
            </div>

            <div class="form-group">
                <div class="status-options">
                    <div class="status-option">
                        <input type="radio" id="statusOnline" name="status" value="online" @if ($status === 'online'){{ 'checked' }}@endif>
                        <label for="statusOnline">
                            <span class="status-indicator status-online"></span>
                            Online
                        </label>
                    </div>
                    <div class="status-option">
                        <input type="radio" id="statusAway" name="status" value="away" @if ($status === 'away'){{ 'checked' }}@endif>
                        <label for="statusAway">
                            <span class="status-indicator status-away"></span>
                            Away
                        </label>
                    </div>
                    <div class="status-option">
                        <input type="radio" id="statusBrb" name="status" value="be_right_back" @if ($status === 'be_right_back'){{ 'checked' }}@endif>
                        <label for="statusBrb">
                            <span class="status-indicator status-brb"></span>
                            Be Right Back
                        </label>
                    </div>
                    <div class="status-option">
                        <input type="radio" id="statusOffline" name="status" value="offline" @if ($status === 'offline'){{ 'checked' }}@endif>
                        <label for="statusOffline">
                            <span class="status-indicator status-offline"></span>
                            Offline
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM12 17C10.9 17 10 16.1 10 15C10 13.9 10.9 13 12 13C13.1 13 14 13.9 14 15C14 16.1 13.1 17 12 17ZM9 6V8H15V6C15 4.34 13.66 3 12 3C10.34 3 9 4.34 9 6Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Password Settings
            </div>

            <div class="form-group">
                <label>Change Password</label>
                <div class="password-fields">
                    <div class="form-group">
                        <input type="password" id="currentPassword" class="form-control" placeholder="Current Password">
                        <div class="error-message">Please enter your current password</div>
                    </div>
                    <div class="form-group">
                        <input type="password" id="newPassword" class="form-control" placeholder="New Password">
                        <div class="error-message">Password must be at least 8 characters</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const settingsForm = document.getElementById('settingsForm');
            const backButton = document.getElementById('backButton');
            const cancelBtn = document.getElementById('cancelBtn');
            const profileImage = document.getElementById('profileImage');
            const avatarPreview = document.getElementById('avatarPreview');
            const uploadTrigger = document.getElementById('uploadTrigger');

            backButton.addEventListener('click', function () {
                window.location.href = '/chat';
            });

            cancelBtn.addEventListener('click', function () {
                if (confirm('Discard all changes?')) {
                    settingsForm.reset();
                    window.location.reload();
                }
            });

            uploadTrigger.addEventListener('click', function () {
                profileImage.click();
            });

            profileImage.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        avatarPreview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            settingsForm.addEventListener('submit', function (e) {
                e.preventDefault();

                let isValid = true;
                const requiredFields = this.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    const group = field.closest('.form-group');
                    group.classList.remove('error', 'success');

                    if (!field.value.trim()) {
                        group.classList.add('error');
                        isValid = false;
                    } else {
                        group.classList.add('success');
                        if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                            group.classList.add('error');
                            isValid = false;
                        }
                    }
                });

                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const currentPasswordGroup = document.getElementById('currentPassword').closest('.form-group');
                const newPasswordGroup = document.getElementById('newPassword').closest('.form-group');

                if (currentPassword || newPassword) {
                    currentPasswordGroup.classList.remove('error', 'success');
                    newPasswordGroup.classList.remove('error', 'success');

                    if (!currentPassword) {
                        currentPasswordGroup.classList.add('error');
                        isValid = false;
                    } else {
                        currentPasswordGroup.classList.add('success');
                    }

                    if (!newPassword || newPassword.length < 8) {
                        newPasswordGroup.classList.add('error');
                        isValid = false;
                    } else {
                        newPasswordGroup.classList.add('success');
                    }
                }

                if (!isValid) return;

                // Prepare data to send
                const formData = new FormData();
                formData.append('name', document.getElementById('fullName').value);
                formData.append('email', document.getElementById('email').value);

                // Passwords
                if (currentPassword && newPassword) {
                    formData.append('current_password', currentPassword);
                    formData.append('new_password', newPassword);
                }

                // Status
                const selectedStatus = document.querySelector('input[name="status"]:checked');
                if (selectedStatus) {
                    formData.append('status', selectedStatus.value);
                }

                // Image
                if (profileImage.files[0]) {
                    formData.append('image', profileImage.files[0]);
                }

                // Send to backend
                fetch('/update-profile', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotificationToast(1, 'Profile updated successfully');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            showNotificationToast(2, data.message, 4000);
                        }
                    })
                    .catch(err => {
                        showNotificationToast(3, 'Error while updating profile', 5000);
                        console.error(err);
                    });
            });
        });
    </script>

   

@endsection