<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <meta name="theme-color" content="#6526DE">

    <!-- Apple-specific for iOS -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ASK SEO CHAT APP">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/js/app.js')
    @yield('css')
</head>

<body>
    <div class="admin-container">
        @yield('main_content')

    </div>
    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>



    <script>
        const messageSound = new Audio('/sounds/sound.mp3');
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
        document.addEventListener('DOMContentLoaded', function () {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        });
    </script>
    <script>
        setTimeout(() => {
            window.userId = {{ auth()->id() }};

            Echo.private(`private-channel.${window.userId}`)
                .listen('.message.received', (e) => {
                    showNotificationToast(1, 'Message Recieved: ' + e.message, 5000);
                    fetchUnreadCount();
                    messageSound.play().catch(e => console.warn("Sound play failed:", e));
                });
        }, 500);
    </script>
    <script>
        function fetchUnreadCount() {
            const chatbar = document.getElementById('chatbar');
            fetch('/unreadcount')
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch');
                    return response.json();
                })
                .then(data => {
                    if(data.total_unread > 0){
                        chatbar.innerHTML = data.total_unread;
                        chatbar.style.display = 'flex';
                    } else {
                        chatbar.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching unread messages:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', fetchUnreadCount());
    </script>
    @yield('js')
</body>

</html>