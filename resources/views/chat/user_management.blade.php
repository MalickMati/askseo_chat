@extends('layouts.admin')

@section('title', "User Management | ASK SEO")

@section('main_content')
    <x-admin_sidebar :activePage="$activePage" :name="$name" :email="$email" :img="$img"></x-admin_sidebar>
    <x-main_content_user_management></x-main_content_user_management> 
    <x-edit_user_modal></x-edit_user_modal>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>

@endsection


@section('js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // DOM Elements
            const editUserModal = document.getElementById('editUserModal');
            const closeModal = document.getElementById('closeModal');
            const cancelEdit = document.getElementById('cancelEdit');
            const saveChanges = document.getElementById('saveChanges');
            const editUserForm = document.getElementById('editUserForm');
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const toggleSwitches = document.querySelectorAll('.toggle-switch input');
            const searchInput = document.querySelector('.search-bar input');
            const pageButtons = document.querySelectorAll('.page-btn:not(.disabled)');
            const modalProfileImage = document.getElementById('modalProfileImage');
            const modalAvatarPreview = document.getElementById('modalAvatarPreview');
            const modalUploadTrigger = document.getElementById('modalUploadTrigger');
            let isSearching = false;

            // Current user being edited
            let currentEditingUserId = null;

            document.getElementById('userTableBody').addEventListener('click', function (e) {
                if (e.target.closest('.btn-edit')) {
                    const button = e.target.closest('.btn-edit');
                    const userId = button.getAttribute('data-user-id');
                    currentEditingUserId = userId;
                    isSearching = true;

                    const userRow = button.closest('tr');
                    const userName = userRow.querySelector('.user-name').textContent;
                    const userEmail = userRow.querySelector('.user-email').textContent;
                    const userAvatar = userRow.querySelector('.user-avatar').src;
                    const roleElement = userRow.querySelector('.user-status.role-admin, .user-status.role-moderator, .user-status.role-user');
                    const userRole = roleElement ? roleElement.textContent.toLowerCase() : '';

                    // Fill modal fields
                    document.getElementById('modalFullName').value = userName;
                    document.getElementById('modalEmail').value = userEmail;
                    modalAvatarPreview.src = userAvatar;

                    const roleMap = {
                        user: 'general_user',
                        admin: 'admin',
                        moderator: 'moderator'
                    };

                    document.querySelectorAll('input[name="userRole"]').forEach(radio => {
                        radio.checked = radio.value === roleMap[userRole];
                    });

                    // Open modal
                    editUserModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            });


            // Close modal
            function closeEditModal() {
                editUserModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                currentEditingUserId = null;
                isSearching = false;
                editUserForm.reset();
            }

            closeModal.addEventListener('click', closeEditModal);
            cancelEdit.addEventListener('click', closeEditModal);

            // Close modal when clicking outside
            editUserModal.addEventListener('click', function (e) {
                if (e.target === editUserModal) {
                    closeEditModal();
                }
            });

            // Save changes
            saveChanges.addEventListener('click', function () {
                const formData = new FormData();
                const selectedRole = document.querySelector('input[name="userRole"]:checked');
                formData.append('id', currentEditingUserId);
                formData.append('name', modalFullName.value);
                formData.append('email', modalEmail.value);
                if (selectedRole) {
                    formData.append('role', selectedRole.value);
                }
                formData.append('password', modalPassword.value);
                formData.append('password_confirmation', modalConfirmPassword.value);

                if (modalProfileImage.files[0]) {
                    formData.append('image', modalProfileImage.files[0]);
                }

                fetch('/users/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        showNotification(1, data.message || 'User updated.');
                        closeEditModal();
                        fetchUsers();
                    })
                    .catch(error => {
                        console.error('Error updating user:', error);
                        showNotification(3, 'Error updating user');
                    });
            });

            // Avatar upload in modal
            modalUploadTrigger.addEventListener('click', function () {
                modalProfileImage.click();
            });

            modalProfileImage.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        modalAvatarPreview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Toggle switch functionality
            toggleSwitches.forEach(switchEl => {
                switchEl.addEventListener('change', function () {
                    const row = this.closest('tr');
                    const statusBadge = row.querySelector('.user-status.status-active, .user-status.status-inactive');

                    if (this.checked) {
                        statusBadge.textContent = 'Active';
                        statusBadge.classList.remove('status-inactive');
                        statusBadge.classList.add('status-active');
                    } else {
                        statusBadge.textContent = 'Inactive';
                        statusBadge.classList.remove('status-active');
                        statusBadge.classList.add('status-inactive');
                    }

                    // In a real app, you would make an API call here to update the user status
                    console.log(`User ${row.querySelector('.user-name').textContent} status changed to ${this.checked ? 'active' : 'inactive'}`);
                });
            });

            // Search functionality
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.users-table tbody tr');

                isSearching = searchTerm.length > 0;

                rows.forEach(row => {
                    const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('.user-email')?.textContent.toLowerCase() || '';

                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Pagination buttons
            pageButtons.forEach(button => {
                if (!button.classList.contains('active')) {
                    button.addEventListener('click', function () {
                        // In a real app, you would fetch the next page of results
                        document.querySelector('.page-btn.active').classList.remove('active');
                        this.classList.add('active');
                        console.log(`Loading page ${this.textContent}`);
                    });
                }
            });
            function fetchUsers() {
                fetch('/fetch-users')
                    .then(res => res.json())
                    .then(users => {
                        const tbody = document.getElementById('userTableBody');
                        tbody.innerHTML = '';

                        users.forEach(user => {
                            if (user.status != 'active') {
                                user_status = 'inactive';
                                user_checked = 'unchecked';
                            } else {
                                user_checked = 'checked';
                                user_status = 'active';
                            }
                            const typeMap = {
                                general_user: 'user',
                                admin: 'admin',
                                moderator: 'moderator',
                                super_admin: 'admin',
                            };

                            const labelMap = {
                                general_user: 'User',
                                admin: 'Admin',
                                moderator: 'Moderator',
                                super_admin: 'Super Admin',
                            };

                            const user_type_class = typeMap[user.type] || 'unknown';
                            const user_type_label = labelMap[user.type] || 'Unknown';
                            const imageSrc = user.image ? `${user.image}` : '/assets/images/default.png';
                            const row = document.createElement('tr');
                            row.innerHTML = `<td>
                                <div class="user-info">
                                    <img src="${imageSrc}" alt="User" class="user-avatar">
                                    <div>
                                        <div class="user-name">${user.name}</div>
                                        <div class="user-email">${user.email}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="user-status role-${user_type_class}">${capitalize(user_type_label)}</span></td>
                            <td>
                                <span class="user-status status-${user_status}">${capitalize(user_status)}</span>
                            </td>
                            <!-- <td>2 hours ago</td> -->
                            <td>
                                <div class="action-buttons">
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="user-status-toggle" ${user_checked} data-user-id="${user.id}">
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <button class="btn btn-edit" data-user-id="${user.id}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Edit
                                    </button>
                                    <button class="btn btn-delete" data-user-id="${user.id}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6zm-9 5v6m4-6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>`;

                            tbody.appendChild(row);
                        });
                    })
                    .catch(err => {
                        console.error("Error fetching users:", err);
                    });
            }

            function capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // setInterval(fetchUsers, 3000);
            setInterval(() => {
                if (!isSearching) {
                    fetchUsers();
                }
            }, 2000);
            window.addEventListener('DOMContentLoaded', fetchUsers);
        });
    </script>

    <script>
        // toggle ajax
        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.getElementById('userTableBody');

            tbody.addEventListener('change', function (e) {
                if (e.target.classList.contains('user-status-toggle')) {
                    const toggle = e.target;
                    const userId = toggle.dataset.userId;
                    const newStatus = toggle.checked ? 'active' : 'inactive';

                    fetch('/update-user-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id: userId,
                            status: newStatus
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotification(1, "User Status Changed");
                                // setInterval(() => {
                                //     window.location.reload();
                                // }, 1000);
                            } else {
                                showNotification(3, 'Error changing status: ' + data.message);
                            }
                        })
                        .catch(err => {
                            showNotification(3, 'Error updating user status:', err);
                        });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-delete')) {
                const button = e.target.closest('.btn-delete');
                const userId = button.getAttribute('data-user-id');

                if (confirm("Are you sure you want to delete this user?")) {
                    fetch('/delete-user', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ id: userId })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotification(1, 'User deleted successfully');
                                fetchUsers();
                            } else {
                                showNotification(3, 'Failed to delete user', 4000);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showNotification(3, 'Server error while deleting', 4000);
                        });
                }
            }
        });
    </script>

    <script>
        function showNotification(code = 1, message = "Success", duration = 2000) {
            const toast = document.getElementById('notification-toast');
            const messageSpan = document.getElementById('notification-message');

            // Clear previous classes
            toast.classList.remove('notification-success', 'notification-warning', 'notification-error');

            // Assign new class based on code
            switch (code) {
                case 1:
                    toast.classList.add('notification-success');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.293 4.293L10 13.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l6-6a1 1 0 1 0-1.414-1.414"/></svg>' + message;
                    break;
                case 2:
                    toast.classList.add('notification-warning');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.56 1h.88l6.54 12.26-.44.74H1.44L1 13.26zM8 2.28 2.28 13H13.7zM8.625 12v-1h-1.25v1zm-1.25-2V6h1.25v4z"/></svg>' + message;
                    break;
                case 3:
                    toast.classList.add('notification-error');
                    messageSpan.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 52 52" fill="#fff" xml:space="preserve"><path d="M26 2C12.8 2 2 12.8 2 26s10.8 24 24 24 24-10.8 24-24S39.2 2 26 2M8 26c0-9.9 8.1-18 18-18 3.9 0 7.5 1.2 10.4 3.3L11.3 36.4C9.2 33.5 8 29.9 8 26m18 18c-3.9 0-7.5-1.2-10.4-3.3l25.1-25.1C42.8 18.5 44 22.1 44 26c0 9.9-8.1 18-18 18"/></svg>' + message;
                    break;
                default:
                    toast.classList.add('notification-success');
                    messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.293 4.293L10 13.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l6-6a1 1 0 1 0-1.414-1.414"/></svg>' + message;
                    break;
            }


            toast.classList.add('show');
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.classList.add('hidden'), 400); // Wait for fade out
            }, duration);
        }
    </script>


@endsection