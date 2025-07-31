@extends('layouts.admin')

@section('title', "Attendance Management | ASK SEO")
@php
$activePage = 'attendance';
$name = 'CEO';
$img = asset('favicon.ico');
$email = 'ceo@askseo.com';
@endphp

@section('main_content')
    <x-admin_sidebar :activePage="$activePage" :name="$name" :email="$email" :img="$img"></x-admin_sidebar>
    <div class="main-content">
        <div class="attendance-header">
            <h1>Daily Attendance</h1>
            <div class="attendance-controls">
                <div class="date-selector">
                    <button id="prevDay" class="date-nav-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <input type="date" id="attendanceDate" value="{{ date('Y-m-d') }}">
                    <button id="nextDay" class="date-nav-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <button id="markAllPresent" class="btn-action">Mark All Present</button>
                <button id="exportAttendance" class="btn-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <div class="attendance-filters">
            <div class="search-bar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input type="text" placeholder="Search users..." id="attendanceSearch">
            </div>
            <select id="departmentFilter">
                <option value="all">All Departments</option>
                <option value="seo">SEO</option>
                <option value="development">Development</option>
                <option value="design">Design</option>
                <option value="marketing">Marketing</option>
            </select>
            <select id="statusFilter">
                <option value="all">All Statuses</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="leave">On Leave</option>
            </select>
        </div>

        <div class="attendance-summary">
            <div class="summary-card">
                <div class="summary-value" id="totalUsers">0</div>
                <div class="summary-label">Total Users</div>
            </div>
            <div class="summary-card">
                <div class="summary-value present" id="presentCount">0</div>
                <div class="summary-label">Present</div>
            </div>
            <div class="summary-card">
                <div class="summary-value absent" id="absentCount">0</div>
                <div class="summary-label">Absent</div>
            </div>
            <div class="summary-card">
                <div class="summary-value late" id="lateCount">0</div>
                <div class="summary-label">Late</div>
            </div>
            <div class="summary-card">
                <div class="summary-value leave" id="leaveCount">0</div>
                <div class="summary-label">On Leave</div>
            </div>
        </div>

        <div class="table-container">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Department</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                        <th>Hours Worked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button class="page-btn disabled" id="prevPage">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="page-numbers">
                <span class="page-number active">1</span>
                <span class="page-number">2</span>
                <span class="page-number">3</span>
            </div>
            <button class="page-btn" id="nextPage">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Attendance Modal -->
    <div id="attendanceModal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Update Attendance</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-user-info">
                    <img id="modalUserAvatar" src="" alt="User" class="user-avatar">
                    <div>
                        <div id="modalUserName" class="user-name"></div>
                        <div id="modalUserEmail" class="user-email"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="attendanceStatus">Status</label>
                    <select id="attendanceStatus">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="leave">On Leave</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="checkInTime">Check-In Time</label>
                    <input type="time" id="checkInTime">
                </div>
                
                <div class="form-group">
                    <label for="checkOutTime">Check-Out Time</label>
                    <input type="time" id="checkOutTime">
                </div>
                
                <div class="form-group">
                    <label for="attendanceNotes">Notes</label>
                    <textarea id="attendanceNotes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancelAttendance" class="btn-cancel">Cancel</button>
                <button id="saveAttendance" class="btn-save">Save Changes</button>
            </div>
        </div>
    </div>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@section('css')
    <style>
        /* Attendance Specific Styles */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            flex: 1;
            background-color: var(--primary-bg);
            height: 100vh;
            overflow-y: auto;
        }

        .attendance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .attendance-header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .attendance-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-nav-btn {
            background: var(--secondary-bg);
            border: none;
            color: var(--text-primary);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .date-nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        #attendanceDate {
            background: var(--secondary-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
        }

        .btn-action {
            background: var(--accent-1);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-action:hover {
            background: var(--accent-2);
        }

        .attendance-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .attendance-filters select {
            background: var(--secondary-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            min-width: 150px;
        }

        .attendance-summary {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: var(--secondary-bg);
            border-radius: 8px;
            padding: 15px;
            flex: 1;
            text-align: center;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .summary-value.present {
            color: var(--success);
        }

        .summary-value.absent {
            color: var(--error);
        }

        .summary-value.late {
            color: var(--warning);
        }

        .summary-value.leave {
            color: var(--moderator);
        }

        .summary-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th {
            text-align: left;
            padding: 12px 15px;
            background: var(--secondary-bg);
            color: var(--text-secondary);
            font-weight: 500;
        }

        .attendance-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .attendance-table tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 500;
        }

        .user-email {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-present {
            background: rgba(0, 255, 157, 0.1);
            color: var(--success);
        }

        .status-absent {
            background: rgba(255, 56, 96, 0.1);
            color: var(--error);
        }

        .status-late {
            background: rgba(255, 149, 0, 0.1);
            color: var(--warning);
        }

        .status-leave {
            background: rgba(0, 184, 255, 0.1);
            color: var(--moderator);
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 25px;
        }

        .page-btn {
            background: var(--secondary-bg);
            border: none;
            color: var(--text-primary);
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-numbers {
            display: flex;
            gap: 5px;
        }

        .page-number {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            cursor: pointer;
        }

        .page-number.active {
            background: var(--accent-1);
            color: white;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .modal.show {
            opacity: 1;
            pointer-events: all;
        }

        .modal-content {
            background: var(--secondary-bg);
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-modal {
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .modal-body {
            padding: 20px;
        }

        .modal-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group select,
        .form-group input[type="time"],
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }

        .form-group textarea {
            resize: vertical;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-save {
            background: var(--accent-1);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const attendanceDate = document.getElementById('attendanceDate');
            const prevDayBtn = document.getElementById('prevDay');
            const nextDayBtn = document.getElementById('nextDay');
            const markAllPresentBtn = document.getElementById('markAllPresent');
            const exportAttendanceBtn = document.getElementById('exportAttendance');
            const attendanceSearch = document.getElementById('attendanceSearch');
            const departmentFilter = document.getElementById('departmentFilter');
            const statusFilter = document.getElementById('statusFilter');
            const attendanceTableBody = document.getElementById('attendanceTableBody');
            const modal = document.getElementById('attendanceModal');
            const closeModalBtn = document.querySelector('.close-modal');
            const cancelAttendanceBtn = document.getElementById('cancelAttendance');
            const saveAttendanceBtn = document.getElementById('saveAttendance');
            
            // Current selected date
            let currentDate = new Date();
            let currentEditingUserId = null;

            // Initialize the page
            function init() {
                updateDateDisplay();
                fetchAttendanceData();
                setupEventListeners();
            }

            // Set up event listeners
            function setupEventListeners() {
                attendanceDate.addEventListener('change', function() {
                    currentDate = new Date(this.value);
                    fetchAttendanceData();
                });

                prevDayBtn.addEventListener('click', function() {
                    currentDate.setDate(currentDate.getDate() - 1);
                    updateDateDisplay();
                    fetchAttendanceData();
                });

                nextDayBtn.addEventListener('click', function() {
                    currentDate.setDate(currentDate.getDate() + 1);
                    updateDateDisplay();
                    fetchAttendanceData();
                });

                markAllPresentBtn.addEventListener('click', markAllPresent);
                exportAttendanceBtn.addEventListener('click', exportAttendance);
                attendanceSearch.addEventListener('input', filterAttendance);
                departmentFilter.addEventListener('change', filterAttendance);
                statusFilter.addEventListener('change', filterAttendance);
                closeModalBtn.addEventListener('click', closeModal);
                cancelAttendanceBtn.addEventListener('click', closeModal);
                saveAttendanceBtn.addEventListener('click', saveAttendance);
            }

            // Update the date input display
            function updateDateDisplay() {
                const formattedDate = formatDateForInput(currentDate);
                attendanceDate.value = formattedDate;
            }

            // Format date for input[type="date"]
            function formatDateForInput(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Fetch attendance data from server
            function fetchAttendanceData() {
                const dateStr = formatDateForInput(currentDate);
                
                fetch(`/api/attendance?date=${dateStr}`)
                    .then(response => response.json())
                    .then(data => {
                        renderAttendanceTable(data.users);
                        updateSummary(data.summary);
                    })
                    .catch(error => {
                        console.error('Error fetching attendance data:', error);
                        showNotification(3, 'Failed to load attendance data');
                    });
            }

            // Render attendance table
            function renderAttendanceTable(users) {
                attendanceTableBody.innerHTML = '';

                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="user-info">
                                <img src="${user.avatar || '/assets/images/default.png'}" alt="User" class="user-avatar">
                                <div>
                                    <div class="user-name">${user.name}</div>
                                    <div class="user-email">${user.email}</div>
                                </div>
                            </div>
                        </td>
                        <td>${user.department}</td>
                        <td>${user.check_in || '--:--'}</td>
                        <td>${user.check_out || '--:--'}</td>
                        <td><span class="status-badge status-${user.status}">${capitalize(user.status)}</span></td>
                        <td>${user.hours_worked || '--'}</td>
                        <td>
                            <button class="action-btn btn-edit-attendance" data-user-id="${user.id}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    `;
                    attendanceTableBody.appendChild(row);
                });

                // Add event listeners to edit buttons
                document.querySelectorAll('.btn-edit-attendance').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const userId = this.getAttribute('data-user-id');
                        openEditModal(userId);
                    });
                });
            }

            // Update summary cards
            function updateSummary(summary) {
                document.getElementById('totalUsers').textContent = summary.total;
                document.getElementById('presentCount').textContent = summary.present;
                document.getElementById('absentCount').textContent = summary.absent;
                document.getElementById('lateCount').textContent = summary.late;
                document.getElementById('leaveCount').textContent = summary.leave;
            }

            // Filter attendance table
            function filterAttendance() {
                const searchTerm = attendanceSearch.value.toLowerCase();
                const department = departmentFilter.value;
                const status = statusFilter.value;

                const rows = attendanceTableBody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const name = row.querySelector('.user-name').textContent.toLowerCase();
                    const email = row.querySelector('.user-email').textContent.toLowerCase();
                    const userDept = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const userStatus = row.querySelector('.status-badge').className.includes(status) ? status : '';

                    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesDept = department === 'all' || userDept === department.toLowerCase();
                    const matchesStatus = status === 'all' || userStatus === status;

                    if (matchesSearch && matchesDept && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Mark all users as present
            function markAllPresent() {
                if (!confirm('Mark all users as present for this day?')) return;
                
                const dateStr = formatDateForInput(currentDate);
                
                // fetch('/api/attendance/mark-all-present', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                //     },
                //     body: JSON.stringify({ date: dateStr })
                // })
                // .then(response => response.json())
                // .then(data => {
                //     if (data.success) {
                //         showNotification(1, 'All users marked as present');
                //         fetchAttendanceData();
                //     } else {
                //         showNotification(3, data.message || 'Failed to mark all as present');
                //     }
                // })
                // .catch(error => {
                //     console.error('Error marking all as present:', error);
                //     showNotification(3, 'Failed to mark all as present');
                // });
            }

            // Export attendance data
            function exportAttendance() {
                const dateStr = formatDateForInput(currentDate);
                window.location.href = `/api/attendance/export?date=${dateStr}`;
            }

            // Open edit modal for a user
            function openEditModal(userId) {
                currentEditingUserId = userId;
                
                // Fetch user details
                fetch(`/api/users/${userId}`)
                    .then(response => response.json())
                    .then(user => {
                        // Set modal content
                        document.getElementById('modalTitle').textContent = `Update Attendance for ${user.name}`;
                        document.getElementById('modalUserName').textContent = user.name;
                        document.getElementById('modalUserEmail').textContent = user.email;
                        document.getElementById('modalUserAvatar').src = user.avatar || '/assets/images/default.png';
                        
                        // Fetch attendance details for this user and date
                        const dateStr = formatDateForInput(currentDate);
                        fetch(`/api/attendance/${userId}?date=${dateStr}`)
                            .then(response => response.json())
                            .then(attendance => {
                                document.getElementById('attendanceStatus').value = attendance.status || 'present';
                                document.getElementById('checkInTime').value = attendance.check_in || '';
                                document.getElementById('checkOutTime').value = attendance.check_out || '';
                                document.getElementById('attendanceNotes').value = attendance.notes || '';
                                
                                // Show modal
                                modal.classList.remove('hidden');
                                document.body.style.overflow = 'hidden';
                            });
                    })
                    .catch(error => {
                        console.error('Error fetching user details:', error);
                        showNotification(3, 'Failed to load user data');
                    });
            }

            // Close modal
            function closeModal() {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentEditingUserId = null;
            }

            // Save attendance changes
            function saveAttendance() {
                const dateStr = formatDateForInput(currentDate);
                const status = document.getElementById('attendanceStatus').value;
                const checkIn = document.getElementById('checkInTime').value;
                const checkOut = document.getElementById('checkOutTime').value;
                const notes = document.getElementById('attendanceNotes').value;
                
                fetch('/api/attendance/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        user_id: currentEditingUserId,
                        date: dateStr,
                        status: status,
                        check_in: checkIn,
                        check_out: checkOut,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(1, 'Attendance updated successfully');
                        fetchAttendanceData();
                        closeModal();
                    } else {
                        showNotification(3, data.message || 'Failed to update attendance');
                    }
                })
                .catch(error => {
                    console.error('Error updating attendance:', error);
                    showNotification(3, 'Failed to update attendance');
                });
            }

            // Helper function to capitalize strings
            function capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Initialize the page
            init();
        });

        function showNotification(code = 1, message = "Success", duration = 2000) {
            const toast = document.getElementById('notification-toast');
            const messageSpan = document.getElementById('notification-message');

            toast.classList.remove('notification-success', 'notification-warning', 'notification-error');

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
            }

            toast.classList.add('show');
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.classList.add('hidden'), 400);
            }, duration);
        }
    </script>
@endsection