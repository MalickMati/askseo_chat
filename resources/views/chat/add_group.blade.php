@extends('layouts.chat-settings')

@section('title', 'Create Group | ASKSEO')

@section('css')
    <style>
        .user-status {
            font-size: 0.8rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
        }
    </style>
@endsection

@section('form-section')
<x-chat-settings.settings-header :message="'Create a new group by selecting members in the group'" :heading="'Add New Group'" />
    <form class="group-form" id="groupForm" autocomplete="off">
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 6v4m0 4h.01"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Group Information
            </div>

            <div class="form-group">
                <label for="groupName">Group Name</label>
                <input type="text" id="groupName" class="form-control" placeholder="Enter group name" required>
                <div class="error-message">Please enter a group name</div>
            </div>
        </div>

        <!-- Members Section -->
        <div class="form-section">
            <div class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Select Members
            </div>

            <div class="selected-count" id="selectedCount">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M8.5 11C10.7091 11 12.5 9.20914 12.5 7C12.5 4.79086 10.7091 3 8.5 3C6.29086 3 4.5 4.79086 4.5 7C4.5 9.20914 6.29086 11 8.5 11Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M17 11L19 13L23 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span id="countText">0 members selected</span>
            </div>

            <div class="user-list" id="userList">
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Group</button>
        </div>
    </form>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const groupForm = document.getElementById('groupForm');
            const backButton = document.getElementById('backButton');
            const cancelBtn = document.getElementById('cancelBtn');
            const userList = document.getElementById('userList');
            const selectedCount = document.getElementById('selectedCount');
            const countText = document.getElementById('countText');
            const groupName = document.getElementById('groupName');

            // Sample user data - in a real app, this would come from an API
            const users = @json($users);

            // Render user list
            function renderUserList() {
                userList.innerHTML = '';
                users.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.className = 'user-item';

                    const statusClass = `status-${user.status}`;

                    userItem.innerHTML = `
                                <img src="${user.avatar}" alt="${user.name}" class="user-avatar">
                                <div class="user-info">
                                    <div class="user-name">${user.name}</div>
                                    <div class="user-status">
                                        <span class="status-indicator ${statusClass}"></span>
                                        ${user.status.replace('_', ' ')}
                                    </div>
                                </div>
                                <div class="user-select">
                                    <input type="checkbox" id="user-${user.id}" data-user-id="${user.id}" class="user-checkbox">
                                </div>
                            `;

                    userList.appendChild(userItem);
                });

                // Add event listeners to checkboxes
                document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });
            }

            // Update selected count
            function updateSelectedCount() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                const count = selected.length;
                countText.textContent = `${count} ${count === 1 ? 'member' : 'members'} selected`;

                // Show/hide selected count
                if (count > 0) {
                    selectedCount.style.display = 'flex';
                } else {
                    selectedCount.style.display = 'none';
                }
            }

            // Initially hide selected count
            selectedCount.style.display = 'none';

            // Back button
            backButton.addEventListener('click', function () {
                window.history.back();
            });

            // Cancel button
            cancelBtn.addEventListener('click', function () {
                if (confirm('Discard this new group?')) {
                    window.location.href = '/chat';
                }
            });

            // Form submission
            groupForm.addEventListener('submit', function (e) {
                e.preventDefault();

                // Validate group name
                const nameGroup = groupName.closest('.form-group');
                nameGroup.classList.remove('error', 'success');

                if (!groupName.value.trim()) {
                    nameGroup.classList.add('error');
                    return;
                } else {
                    nameGroup.classList.add('success');
                }

                // Get selected users
                const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
                const selectedUserIds = Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-user-id'));

                if (selectedUserIds.length === 0) {
                    showNotificationToast(2, "Select atleast one member in group!");
                    return;
                }

                // Prepare data to send
                const formData = new FormData();

                formData.append('groupname', groupName.value.trim());
                selectedUserIds.forEach(id => {
                    formData.append('members[]', id);
                });

                fetch('/create/new/group', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotificationToast(1, "Group Created", 3000);
                            setTimeout(() => {
                                window.location.href = '/chat';
                            }, 3000);
                        } else {
                            showNotificationToast(3, data.message, 5000);
                        }
                    })
                    .catch(err => {
                        showNotificationToast(3, "Unable to create the group!", 5000);
                        console.error(err);
                    });
            });

            // Initialize the page
            renderUserList();
        });
    </script>
    @if (session('error'))
        <script>
            showNotificationToast(2, '{{ session('error') }}', 10000);
        </script>
    @endif
@endsection