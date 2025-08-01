<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <title>@yield('title', 'Settings | ASKSEO')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0f0f1a;
            --secondary-bg: #1a1a2e;
            --accent-1: #8E84FF;
            --accent-2: #6526DE;
            --text-primary: #e2e2e2;
            --text-secondary: #a1a1a1;
            --success: #00ff9d;
            --error: #ff3860;
            --online: #00ff9d;
            --away: #ffbe0b;
            --brb: #f5ff00;
            --offline: #ff3860;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(0, 245, 212, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(155, 93, 229, 0.1) 0%, transparent 50%);
        }

        .settings-container {
            width: 100%;
            max-width: 600px;
            background-color: var(--secondary-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .settings-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 245, 212, 0.1), rgba(155, 93, 229, 0.1));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .settings-container:hover::before {
            opacity: 1;
        }

        .settings-header {
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            position: relative;
        }

        .back-button {
            position: absolute;
            left: 20px;
            top: 30px;
            background: none;
            border: none;
            color: white;
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
        }

        .back-button svg {
            margin-right: 8px;
        }

        .logo {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            background: linear-gradient(90deg, white, #e0e0e0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            gap: 5px;
        }

        .logo-icon {
            margin-right: 10px;
            width: 30px;
            height: 30px;
        }

        .settings-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .settings-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .settings-form,
        .group-form {
            padding: 30px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
        }

        .section-title svg {
            margin-right: 10px;
            color: var(--accent-1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-1);
            box-shadow: 0 0 0 2px rgba(0, 245, 212, 0.2);
        }

        .status-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .status-option {
            flex: 1;
            min-width: 120px;
        }

        .status-option input {
            display: none;
        }

        .status-option label {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .status-option input:checked+label {
            background-color: rgba(0, 245, 212, 0.1);
            border: 1px solid rgba(0, 245, 212, 0.3);
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-online {
            background-color: var(--online);
        }

        .status-away {
            background-color: var(--away);
        }

        .status-brb {
            background-color: var(--brb);
        }

        .status-offline {
            background-color: var(--offline);
        }

        .avatar-upload {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-upload-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            color: var(--text-primary);
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar-upload-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-1);
        }

        .avatar-upload input {
            display: none;
        }

        .user-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-list::-webkit-scrollbar {
            width: 8px;
        }

        .user-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .user-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .user-list::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background-color 0.3s ease;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .user-select {
            margin-left: 15px;
        }

        .user-select input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .selected-count {
            font-size: 0.9rem;
            color: var(--accent-1);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .selected-count svg {
            margin-right: 8px;
        }

        .password-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }

        .form-group.error .error-message {
            display: block;
        }

        .form-group.error .form-control {
            border-color: var(--error);
        }

        .form-group.success .form-control {
            border-color: var(--success);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:disabled {
            background-color: gray;
            color: white;
            cursor: not-allowed;
        }

        .btn:disabled:hover {
            background-color: grey;
            border-color: grey;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            border: none;
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 5px 15px rgba(0, 245, 212, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            margin-right: 15px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .settings-container {
                max-width: 100%;
                border-radius: 12px;
            }

            .settings-header {
                padding: 25px 20px 20px;
            }

            .settings-form {
                padding: 25px 20px;
            }

            .password-fields {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .status-options {
                flex-direction: column;
            }

            .status-option {
                min-width: 100%;
            }
        }

        /* Holographic Effect */
        .holographic {
            position: relative;
            overflow: hidden;
        }

        .holographic::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0) 30%,
                    rgba(255, 255, 255, 0.05) 45%,
                    rgba(255, 255, 255, 0) 60%,
                    rgba(255, 255, 255, 0) 100%);
            transform: rotate(30deg);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .holographic:hover::after {
            animation: hologram 3s linear infinite;
        }

        @keyframes hologram {
            0% {
                transform: translateY(-100%) rotate(30deg);
            }

            100% {
                transform: translateY(100%) rotate(30deg);
            }
        }

        .notification-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100%);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 16px;
            z-index: 9999;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            opacity: 0;
            transition: all 0.4s ease;
            color: #fff;
            user-select: none;
        }

        /* Colors by type */
        .notification-success {
            background-color: #00c896;
        }

        .notification-warning {
            background-color: #ff9500;
        }

        .notification-error {
            background-color: #ff3860;
        }

        .notification-toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .notification-toast.hidden {
            pointer-events: none;
        }

        #notification-message {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .inspinner {
            display: inline-block;
        }

        .inspinner svg.spin {
            animation: spin 1s linear infinite;
        }

        .outspinner {
            display: inline-block;
        }

        .outspinner svg.spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* User Table Styles */
        .table-container {
            max-height: calc(40px * 10 + 60px);
            overflow-y: auto;
            overflow-x: hidden;
            width: 100%;
        }

        /* Prevent text from wrapping in cells */
        .users-table th,
        .users-table td {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Ensure table takes full width and enables horizontal scrolling if needed */
        .users-table {
            width: max-content;
            min-width: 100%;
            background-color: var(--secondary-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border-collapse: separate;
            border-spacing: 0;
        }

        .users-table thead th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .users-table tbody tr {
            transition: all 0.3s ease;
        }

        .users-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .users-table td {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        ::-webkit-scrollbar {
            height: 10px;
            width: 10px;
            transition: all 0.3s ease-in-out;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--accent-1), var(--accent-2));
            border-radius: 10px;
            border: 2px solid var(--secondary-bg);
            transition: all 0.3s ease;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
            cursor: grab;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--accent-2), var(--accent-1));
            transform: scale(1.1);
            box-shadow: 0 0 8px var(--accent-1), 0 0 12px var(--accent-2);
        }
        @media(max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
        }
    </style>
    @yield('css')
</head>

<body>
    <div class="settings-container holographic">
        @yield('form-section')
        <div id="notification-toast" class="notification-toast hidden">
            <span id="notification-message"></span>
        </div>
    </div>

    @yield('js')
    <script>
        function showNotificationToast(code = 1, message = "Success", duration = 2000) {
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
                    messageSpan.innerHTML = '<svg width="20" height="20" viewBox="0 0 52 52" fill="#fff" xml:space="preserve"><path d="M26 2C12.8 2 2 12.8 2 26s10.8 24 24 24 24-10.8 24-24S39.2 2 26 2M8 26c0-9.9 8.1-18 18-18 3.9 0 7.5 1.2 10.4 3.3L11.3 36.4C9.2 33.5 8 29.9 8 26m18 18c-3.9 0-7.5-1.2-10.4-3.3l25.1-25.1C42.8 18.5 44 22.1 44 26c0 9.9-8.1 18-18 18"/></svg>' + message;
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
                setTimeout(() => toast.classList.add('hidden'), 400);
            }, duration);
        }
    </script>
</body>

</html>