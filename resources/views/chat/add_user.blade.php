@extends('layouts.admin')

@section('title', "Add New User | ASK SEO")

@section('main_content')
@php
$activePage = 'adduser';
$name = 'CEO';
$img = 'assets/images/logo.png';
@endphp
    <x-admin_sidebar :activePage="$activePage" :name="$name" :img="$img"></x-admin_sidebar>
    
    <div class="main-content">
        <div class="header">
            <div class="page-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 8H20M22 8H20M20 8V6M20 8V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Add New User
            </div>
        </div>

        <div class="holographic" style="background-color: var(--secondary-bg); border-radius: 12px; padding: 30px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
            <form id="addUserForm" autocomplete="off">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section" style="margin-bottom: 30px;">
                    <div class="section-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Basic Information
                    </div>

                    <div class="form-group">
                        <label for="profileImage">Profile Picture</label>
                        <div class="avatar-upload">
                            <div class="avatar-preview">
                                <img src="https://ui-avatars.com/api/?name=New+User&background=8E84FF&color=fff" alt="Profile Preview" id="avatarPreview">
                            </div>
                            <div class="avatar-upload-btn" id="uploadTrigger">
                                Change Photo
                                <input type="file" id="profileImage" name="avatar" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="name" class="form-control" placeholder="Enter full name" required>
                        <div class="error-message">Please enter a valid name</div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter email address" required>
                        <div class="error-message">Please enter a valid email address</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                        <div class="error-message">Password must be at least 8 characters</div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                        <div class="error-message">Passwords must match</div>
                    </div>
                </div>

                <!-- Role & Status Section -->
                <div class="form-section" style="margin-bottom: 30px;">
                    <div class="section-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 6v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Role & Status
                    </div>

                    <div class="form-group">
                        <label>User Role</label>
                        <div class="role-options">
                            <div class="role-option">
                                <input type="radio" id="roleAdmin" name="role" value="admin" checked>
                                <label for="roleAdmin">
                                    Admin
                                </label>
                            </div>
                            <div class="role-option">
                                <input type="radio" id="roleModerator" name="role" value="moderator">
                                <label for="roleModerator">
                                    Moderator
                                </label>
                            </div>
                            <div class="role-option">
                                <input type="radio" id="roleUser" name="role" value="user">
                                <label for="roleUser">
                                    User
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Account Status</label>
                        <div class="status-options">
                            <div class="status-option">
                                <input type="radio" id="statusActive" name="status" value="active" checked>
                                <label for="statusActive">
                                    <span class="status-indicator status-online"></span>
                                    Active
                                </label>
                            </div>
                            <div class="status-option">
                                <input type="radio" id="statusInactive" name="status" value="inactive">
                                <label for="statusInactive">
                                    <span class="status-indicator status-offline"></span>
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addUserForm = document.getElementById('addUserForm');
            const cancelBtn = document.getElementById('cancelBtn');
            const profileImage = document.getElementById('profileImage');
            const avatarPreview = document.getElementById('avatarPreview');
            const uploadTrigger = document.getElementById('uploadTrigger');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            // Handle avatar upload preview
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

            // Cancel button
            cancelBtn.addEventListener('click', function () {
                if (confirm('Discard this new user?')) {
                    window.location.href = "{{ route('admin.users') }}";
                }
            });

            // Form validation
            addUserForm.addEventListener('submit', function (e) {
                e.preventDefault();

                let isValid = true;
                const requiredFields = this.querySelectorAll('[required]');

                // Reset error states
                requiredFields.forEach(field => {
                    const group = field.closest('.form-group');
                    group.classList.remove('error', 'success');

                    if (!field.value.trim()) {
                        group.classList.add('error');
                        isValid = false;
                    } else {
                        group.classList.add('success');
                        
                        // Special validation for email
                        if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                            group.classList.add('error');
                            isValid = false;
                        }
                    }
                });

                // Password confirmation check
                if (password.value !== passwordConfirmation.value) {
                    passwordConfirmation.closest('.form-group').classList.add('error');
                    passwordConfirmation.closest('.form-group').querySelector('.error-message').textContent = 'Passwords must match';
                    isValid = false;
                }

                // Password length check
                if (password.value.length < 8) {
                    password.closest('.form-group').classList.add('error');
                    isValid = false;
                }

                if (!isValid) return;

                // Prepare form data
                const formData = new FormData(this);

                
            });

            // Notification function
            function showNotification(message, type) {
                const toast = document.getElementById('notification-toast');
                const messageSpan = document.getElementById('notification-message');
                
                toast.className = `notification-toast notification-${type}`;
                messageSpan.textContent = message;
                toast.classList.remove('hidden');
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                    toast.classList.add('hidden');
                }, 3000);
            }
        });
    </script>
@endsection