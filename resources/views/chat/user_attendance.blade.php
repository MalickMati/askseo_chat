@extends('layouts.admin')

@section('title', "Attendance Management | ASK SEO")
@php
    $activePage = 'attendance';
@endphp

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

        .status-Present {
            background: rgba(0, 255, 157, 0.1);
            color: var(--success);
        }

        .status-Absent {
            background: rgba(255, 56, 96, 0.1);
            color: var(--error);
        }

        .status-Late {
            background: rgba(255, 149, 0, 0.1);
            color: var(--warning);
        }

        .status-Leave {
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

@section('main_content')
    <x-admin_sidebar :activePage="$activePage" :name="$name" :email="$email" :img="$img"></x-admin_sidebar>

    <div class="main-content">
        <div class="attendance-header">
            <h1>Daily Attendance</h1>
            <div class="attendance-controls">
                <div class="date-selector">
                    <button id="prevDay" class="date-nav-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <input type="date" id="attendanceDate" value="{{ date('Y-m-d') }}">
                    <button id="nextDay" class="date-nav-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="attendance-summary">
            <div class="summary-card">
                <div class="summary-value" id="totalUsers">{{ $total_users }}</div>
                <div class="summary-label">Total Users</div>
            </div>
            <div class="summary-card">
                <div class="summary-value present" id="presentCount">{{ $present_users }}</div>
                <div class="summary-label">Present</div>
            </div>
            <div class="summary-card">
                <div class="summary-value absent" id="absentCount">{{ $absent_users }}</div>
                <div class="summary-label">Absent</div>
            </div>
            <div class="summary-card">
                <div class="summary-value late" id="lateCount">{{ $late_users }}</div>
                <div class="summary-label">Late</div>
            </div>
        </div>

        <div class="table-container">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">

                </tbody>
            </table>
        </div>
    </div>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@section('js')
    <script>
        const attendanceTableBody = document.getElementById('attendanceTableBody');
        const dateInput = document.getElementById('attendanceDate');
        const prevDayBtn = document.getElementById('prevDay');
        const nextDayBtn = document.getElementById('nextDay');
        const presentCount = document.getElementById('presentCount');
        const absentCount = document.getElementById('absentCount');
        const lateCount = document.getElementById('lateCount');
        document.addEventListener('DOMContentLoaded', function () {
            get_user_table(dateInput.value);
        });

        dateInput.addEventListener('change', () => {
            get_user_table(dateInput.value);
        });

        prevDayBtn.addEventListener('click', () => {
            let current = new Date(dateInput.value);
            current.setDate(current.getDate() - 1);
            dateInput.valueAsDate = current;
            get_user_table(dateInput.value);
        });

        nextDayBtn.addEventListener('click', () => {
            let current = new Date(dateInput.value);
            current.setDate(current.getDate() + 1);
            dateInput.valueAsDate = current;
            get_user_table(dateInput.value);
        });

        function get_user_table(date = null) {
            showNotification(1, 'Fetching details!');

            const payload = date ? { date } : {};

            fetch('/admin/get-user-attendance', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification(1, data.message);
                        lateCount.innerHTML = data.late_users;
                        presentCount.innerHTML = data.present_users;
                        absentCount.innerHTML = data.absent_users;
                        update_table(data.records);
                    } else {
                        showNotification(2, data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification(3, 'Error while fetching data!', 5000);
                    prevDayBtn.disabled = true;
                    nextDayBtn.disabled = true;
                    dateInput.disabled = true;
                });
        }

        function update_table(records) {
            attendanceTableBody.innerHTML = '';
            records.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                        <td>
                            <div class="user-info">
                                <img src="${record['user_image']}" alt="User" class="user-avatar">
                                <div>
                                    <div class="user-name">${record['username']}</div>
                                    <div class="user-email">${record['useremail']}</div>
                                </div>
                            </div>
                        </td>
                        <td>${record['check_in']}</td>
                        <td>${record['check_out']}</td>
                        <td><span class="status-badge status-${record['status']}">${record['status']}</span></td>
                        <td>${record['hours_worked']}</td>
                    `;
                attendanceTableBody.appendChild(row);
            });
        }

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