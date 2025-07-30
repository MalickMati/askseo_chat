@extends('layouts.app')

@section('content')
    <x-chat_sidebar :currentUser=$currentUser :users=$allusers :groups=$allgroups></x-chat_sidebar>
    <x-chat_area></x-chat_area>
    <x-chat_sidebar_menu></x-chat_sidebar_menu>
    <x-chat_menu></x-chat_menu>
    <x-add_user_in_group_modal></x-add_user_in_group_modal>

    <div id="notification-toast" class="notification-toast hidden">
        <span id="notification-message"></span>
    </div>
@endsection

@if (session()->has('user_type') && (session('user_type') === 'admin' || session('user_type') === 'moderator') || session('user_type') === 'super_admin')
    <script>
        const isAdmin = true;
    </script>

@else
    <script>
        const isAdmin = false;
    </script>
@endif

@section('js')
    <script>
        let isTabActive = true;
        let isMediaPlaying = false;
        document.addEventListener("visibilitychange", () => {
            isTabActive = !document.hidden;
        });

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
    @if(session('error'))
        <script>
            showNotificationToast(2, "{{ session('error') }}", 10000);
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // DOM Elements
            const hamburger = document.getElementById('hamburger');
            const hamburgerclose = document.getElementById('hamburgerclose');
            const sidebar = document.getElementById('sidebar');
            const sidebarMenuButton = document.getElementById('sidebarMenuButton');
            const sidebarMenu = document.getElementById('sidebarMenu');
            const chatMenuButton = document.getElementById('chatMenuButton');
            const chatMenu = document.getElementById('chatMenu');
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const messagesContainer = document.getElementById('messagesContainer');
            const chatItems = document.querySelectorAll('.chat-item');


            // Toggle sidebar on hamburger click
            hamburger.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });

            hamburgerclose.addEventListener('click', function () {
                sidebar.classList.remove('active');
            });

            // Toggle sidebar menu
            sidebarMenuButton.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebarMenu.classList.toggle('active');
                chatMenu.classList.remove('active');
            });

            // Toggle chat menu
            chatMenuButton.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebarMenu.classList.remove('active');

                // Clear old menu content
                chatMenu.innerHTML = '';

                if (activeGroupId) {
                    // Group-specific menu
                    chatMenu.innerHTML = `
                                                    ${isAdmin ? `<div class="menu-item" data-group-id="${activeGroupId}" onclick="openAddMemberModal(activeGroupId);">
                                                        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1" fill="currentcolor"/></svg>
                                                        <span>Add Member</span>
                                                    </div>` : ''}
                                                    <div class="menu-item" data-group-id="${activeGroupId}" onclick="leave_chat_group(activeGroupId);">
                                                        <svg width="20" height="20" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentcolor"><path fill-rule="evenodd" d="M11.707 3.293 15.414 7l-3.707 3.707a1 1 0 0 1-1.414-1.414L11.586 8H4.5a1.5 1.5 0 1 0 0 3H6a1 1 0 1 1 0 2H4.5a3.5 3.5 0 1 1 0-7h7.086l-1.293-1.293a1 1 0 1 1 1.414-1.414"/></svg>
                                                        <span>Leave Group</span>
                                                    </div>
                                                `;
                } else if (activeReceiverId) {
                    chatMenu.innerHTML = `
                                                <div class="menu-item" data-group-id="${activeReceiverId}}">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentcolor"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m6.32 5.095L7.096 18.321A8 8 0 0 0 18.32 7.096M12 4a8 8 0 0 0-6.32 12.905L16.904 5.679A7.97 7.97 0 0 0 12 4"/></svg>
                                                    <span>Block</span>
                                                </div>
                                                `;
                } else {
                    chatMenu.innerHTML = '';
                }

                chatMenu.classList.toggle('active');
            });

            // Close menus when clicking elsewhere
            document.addEventListener('click', function () {
                sidebarMenu.classList.remove('active');
                chatMenu.classList.remove('active');
            });

            // Prevent menu from closing when clicking inside
            sidebarMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            chatMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            // Switch chats when clicking on chat items
            chatItems.forEach(item => {
                item.addEventListener('click', function () {
                    // Remove active class from all items
                    chatItems.forEach(i => i.classList.remove('active'));
                    // Add active class to clicked item
                    this.classList.add('active');

                    // Update chat header with selected user
                    const userName = this.querySelector('.chat-name').textContent;
                    const userAvatar = this.querySelector('.chat-avatar').src;

                    document.querySelector('.chat-header-info .chat-name').textContent = userName;
                    document.querySelector('.chat-header-info .user-avatar').src = userAvatar;

                    // In a real app, you would load the messages for this chat
                    inputarea.style.display = 'flex';
                    chat_header.style.display = 'flex';

                    // Close sidebar on mobile
                    if (window.innerWidth <= 900) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Initialize with sidebar closed on mobile
            if (window.innerWidth <= 900) {
                sidebar.classList.remove('active');
                sidebar.classList.toggle('active');
            }

            // Handle window resize
            window.addEventListener('resize', function () {
                if (window.innerWidth > 900) {
                    sidebar.classList.add('active');
                }
            });

            // Scroll to bottom initially
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            refreshSidebar();
            let isTabActive = document.visibilityState === 'visible';

            // Update the variable whenever the tab visibility changes
            document.addEventListener('visibilitychange', () => {
                isTabActive = document.visibilityState === 'visible';
            });

            // Optional: Check current status on page load
            window.addEventListener('load', () => {
            });

            setInterval(() => {
                if (!isTabActive) {
                    return;
                }
                if (isMediaPlaying) {
                    return;
                }
                if (activeReceiverId) {
                    loadMessages(activeReceiverId);
                } else if (activeGroupId) {
                    loadGroupMessages(activeGroupId);
                }

                refreshSidebar();
            }, pollingtime);

        });
    </script>

    <script>
        const currentUserId = {{ $currentUser['id'] }};
        const usersMap = {
            @foreach ($allusers as $user)
                {{ $user['id'] }}: "{{ addslashes($user['username']) }}",
            @endforeach
                                {{ $currentUser['id'] }}: "{{ addslashes($currentUser['username']) }}"
                            };

        let activeReceiverId = null;
        let activeGroupId = null;
        let refreshInterval = null;
        const inputarea = document.getElementById('inputarea');
        const chat_header = document.querySelector('.chat-header-info');

        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function () {
                const groupId = this.getAttribute('data-group-id');
                const userId = this.getAttribute('data-user-id');

                if (groupId) {
                    activeReceiverId = null;
                    activeGroupId = groupId;
                    loadGroupMessages(groupId);
                } else {
                    activeReceiverId = userId;
                    activeGroupId = null;
                    loadMessages(userId);
                }
            });
        });


        let lastMessageTimestamps = {}; // {chatId: 'timestamp'}

        function loadMessages(receiverId) {
            const after = lastMessageTimestamps[receiverId] || '';
            fetch(`/messages/${receiverId}?after=${encodeURIComponent(after)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        appendMessages(data.messages, currentUserId, usersMap, receiverId);

                        // Update last seen timestamp
                        const last = data.messages[data.messages.length - 1];
                        lastMessageTimestamps[receiverId] = last.created_at;

                        markMessagesAsRead(receiverId);
                    }
                })
                .catch(err => console.error("Failed to load messages", err));
        }

        function loadGroupMessages(groupId) {
            const after = lastMessageTimestamps[groupId] || '';
            fetch(`/group-messages/${groupId}?after=${encodeURIComponent(after)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        appendMessages(data.messages, currentUserId, usersMap, groupId);

                        const last = data.messages[data.messages.length - 1];
                        lastMessageTimestamps[groupId] = last.sent_at;

                        fetch(`/group/${groupId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error("Failed to load group messages", err);
                    showNotificationToast(3, 'Failed to load group messages!');
                });
        }


        function formatTimestamp(datetime) {
            const date = new Date(datetime);
            const now = new Date();
            const messageDate = date.toDateString();
            const today = now.toDateString();
            const yesterday = new Date();
            yesterday.setDate(now.getDate() - 1);

            if (messageDate === today) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } else if (messageDate === yesterday.toDateString()) {
                return "Yesterday";
            } else if (now - date < 7 * 24 * 60 * 60 * 1000) {
                return date.toLocaleDateString(undefined, { weekday: 'long' });
            } else {
                return date.toLocaleDateString();
            }
        }

        function appendMessages(messages, currentUserId, usersMap, activeUserId) {
            const container = document.getElementById('messagesContainer');
            const newMessages = [];

            const isInitialLoad = container.innerHTML.trim() === '';
            const isAtBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 50;

            messages.forEach(msg => {
                const isNew = !msg.read_at && msg.sender_id !== currentUserId;
                if (isNew) {
                    newMessages.push(msg);
                }
                const isSent = msg.sender_id === currentUserId;
                const isRead = !!msg.read_at;
                const ticks = isRead
                    ? '<span class="message-ticks read"><svg width="15" height="15" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.03 11.47a.75.75 0 0 0-1.06 1.06zM8.5 16l-.53.53a.75.75 0 0 0 1.06 0zm8.53-7.47a.75.75 0 0 0-1.06-1.06zm-8 2.94a.75.75 0 0 0-1.06 1.06zM12.5 16l-.53.53a.75.75 0 0 0 1.06 0zm8.53-7.47a.75.75 0 0 0-1.06-1.06zm-17.06 4 4 4 1.06-1.06-4-4zm5.06 4 8-8-1.06-1.06-8 8zm-1.06-4 4 4 1.06-1.06-4-4zm5.06 4 8-8-1.06-1.06-8 8z" fill="currentcolor"/></svg></span>'
                    : '<span class="message-ticks"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 7 9.429 17 6 13" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
                const timeFormatted = formatTimestamp(msg.sent_at || msg.created_at);
                const senderName = usersMap[msg.sender_id] || "Unknown";

                const messageInfo = document.createElement('div');
                messageInfo.classList.add('message-info');
                let content = "";

                if (msg.file_path) {
                    const fileUrl = `/storage/${msg.file_path}`;
                    const fileName = msg.original_filename || msg.file_path.split('/').pop();
                    const ext = fileName.split('.').pop().toLowerCase();

                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                    const isVideo = ['mp4', 'webm', 'ogg'].includes(ext);
                    const isAudio = ['mp3', 'wav', 'ogg', 'aac'].includes(ext);

                    if (isImage) {
                        content = `<a href="${fileUrl}" target="_blank">
                                        <img src="${fileUrl}" class="message-image-preview" alt="${fileName}" loading="lazy">
                                    </a>`;
                    } else if (isVideo) {
                        content = `<video controls class="message-video-preview" loading="lazy">
                                        <source src="${fileUrl}" type="video/${ext}">
                                        Your browser does not support the video tag.
                                    </video>`;
                    } else if (isAudio) {
                        content = `<audio controls class="message-audio-preview" loading="lazy">
                                        <source src="${fileUrl}" type="audio/${ext}">
                                        Your browser does not support the audio element.
                                    </audio>`;
                    } else {
                        content = `<a href="${fileUrl}" download class="file-download-link">
                                        ${fileName}
                                    </a>`;
                    }
                } else {
                    content = msg.message || '';
                }


                if (!isSent) {
                    messageInfo.innerHTML = `
                            <span class="message-sender">${senderName}</span>
                            <div class="message message-received">
                                ${content}
                                <div class="message-time">${timeFormatted}</div>
                            </div>
                        `;
                } else {
                    messageInfo.innerHTML = `
                            <div class="message message-sent">
                                ${content}
                                <div class="message-time">
                                    ${timeFormatted} ${ticks}
                                </div>
                            </div>
                        `;
                }

                if (!isTabActive && newMessages.length > 0) {
                    const latest = newMessages[newMessages.length - 1];
                    const senderName = usersMap[latest.sender_id] || "New Message";
                    const preview = latest.message?.slice(0, 50) || "File sent";
                    showChatNotification(senderName, preview);
                }
                container.appendChild(messageInfo);
            });
            attachMediaListeners();
            if (isInitialLoad || isAtBottom) {
                container.scrollTop = container.scrollHeight;
            }
        }

        function markMessagesAsRead(senderId) {
            fetch('/messages/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ sender_id: senderId })
            }).then(res => {
                if (!res.ok) throw new Error('Failed to mark as read');
            }).catch(err => {
                console.error('Error marking messages as read:', err);
                showNotificationToast(2, 'Error updating messages!');
            });
        }

    </script>

    <script>
        function sendMessage() {
            const fileInput = document.getElementById('fileInput');
            const files = fileInput.files;
            const message = document.getElementById('messageInput').value.trim();
            const sendbutton = document.getElementById('sendMessageBtn');

            // Do nothing if both are empty
            if (!message && !files) return;
            sendbutton.innerHTML = `<span class="loader"></span>`;

            const formData = new FormData();
            if (message) formData.append('message', message);
            if (files.length > 0) {
                formData.append('file', files[0]); // IMPORTANT: only 1 file
            }

            // Append receiver or group ID
            if (activeReceiverId) {
                formData.append('receiver_id', activeReceiverId);
            } else if (activeGroupId) {
                formData.append('group_id', activeGroupId);
            }

            const route = activeGroupId ? "/group-messages/send" : "/messages/send";

            fetch(route, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData,
                credentials: "same-origin"
            })
                .then(async res => {
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await res.text();
                        console.error("Unexpected response:", text);
                        alert("Unexpected server response. Check console.");
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data) return;

                    if (data.success) {
                        fileInput.value = '';
                        document.getElementById('filePreviewContainer').innerHTML = '';
                        document.getElementById('messageInput').value = '';
                        sendbutton.innerHTML = `
                                <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                                    <path d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z" fill="#fff" />
                                </svg>`;

                        if (activeReceiverId) {
                            loadMessages(activeReceiverId);
                        } else if (activeGroupId) {
                            loadGroupMessages(activeGroupId);
                        }
                    } else {
                        alert("Failed to send message.");
                    }
                })
                .catch(err => {
                    console.error("Error sending message:", err);
                    alert("An error occurred while sending the message.");
                });
        }



        // Handle click
        document.getElementById('sendMessageBtn').addEventListener('click', function () {
            sendMessage();
        });

        // Handle Enter key
        document.getElementById('messageInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // Prevent newline
                sendMessage();
            }
        });
    </script>

    <script>
        function refreshSidebar() {
            fetch('/sidebar/data')
                .then(res => res.json())
                .then(data => {
                    const chatList = document.querySelector('.chat-list');
                    chatList.innerHTML = '';

                    // Render groups
                    data.groups.forEach(group => {
                        chatList.innerHTML += `
                            <div class="chat-item" data-group-id="${group.id}">
                                <img src="/assets/images/logo.png" alt="Group" class="chat-avatar">
                                <div class="chat-details">
                                    <div class="chat-name-time">
                                        <span class="chat-name">${group.name}</span>
                                        <span class="chat-time">${group.last_time || ''}</span>
                                    </div>
                                    <div class="chat-last-message">
                                        <span>${group.last_message || 'Group Chat'}</span>
                                    </div>
                                </div>
                                ${group.unread_count > 0 ? `<div class="unread-badge">${group.unread_count}</div>` : ''}
                            </div>
                        `;
                    });

                    // Render users
                    data.users.forEach(user => {
                        chatList.innerHTML += `
                            <div class="chat-item" data-user-id="${user.id}">
                                <img src="${user.img || 'assets/images/default.png'}" class="chat-avatar ${user.status}">
                                <div class="chat-details">
                                    <div class="chat-name-time">
                                        <span class="chat-name">${user.username}</span>
                                        <span class="chat-time">${user.last_time || ''}</span>
                                    </div>
                                    <div class="chat-last-message">
                                        <span>${user.last_message || ''}</span>
                                    </div>
                                </div>
                                ${user.unread_count > 0 ? `<div class="unread-badge">${user.unread_count}</div>` : ''}
                            </div>
                        `;
                    });

                    // Re-attach click events to new chat items
                    attachChatItemEvents();
                })
                .catch(err => console.error('Failed to refresh sidebar:', err));
        }

        // Reattach event listeners
        function attachChatItemEvents() {
            const chatItems = document.querySelectorAll('.chat-item');
            chatItems.forEach(item => {
                item.addEventListener('click', function () {
                    chatItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    const userName = this.querySelector('.chat-name').textContent;
                    const userAvatar = this.querySelector('.chat-avatar').src;
                    document.querySelector('.chat-header-info .chat-name').textContent = userName;
                    document.querySelector('.chat-header-info .user-avatar').src = userAvatar;
                    inputarea.style.display = 'flex';
                    chat_header.style.display = 'flex';
                    if (window.innerWidth <= 900) {
                        sidebar.classList.remove('active');
                    }

                    const groupId = this.getAttribute('data-group-id');
                    const userId = this.getAttribute('data-user-id');

                    if (groupId) {
                        activeGroupId = groupId;
                        activeReceiverId = null;
                        loadGroupMessages(groupId);
                    } else if (userId) {
                        activeReceiverId = userId;
                        activeGroupId = null;
                        loadMessages(userId);
                    }
                });
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if ('Notification' in window && Notification.permission !== 'granted') {
                Notification.requestPermission().then(permission => {
                    console.log('Notification permission:', permission);
                });
            }
        });
        function showChatNotification(title, body) {
            if (Notification.permission === 'granted') {
                const notification = new Notification(title, {
                    body: body,
                    icon: "{{ asset('assets/images/logo.png') }}",
                    tag: 'chat-notification'
                });

                notification.onclick = function () {
                    window.focus();
                    notification.close();
                };
            }
        }
    </script>

    <script>
        // Leave Group Functionality
        function leave_chat_group(groupId) {
            if (confirm('Are you sure you want to leave this group?')) {
                fetch(`/groups/${groupId}/leave`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotificationToast(1, 'You left the group!');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);

                        } else {
                            showNotificationToast(2, 'Error while leaving the group!');
                        }
                    });
            }
        }
    </script>

    <script>
        // add member
        function openAddMemberModal(groupId) {
            document.getElementById('addMemberModal').classList.remove('hidden');
            fetch(`/groups/${groupId}/members-list`)
                .then(res => res.json())
                .then(data => {
                    const userList = document.getElementById('userList');
                    userList.innerHTML = '';

                    data.all_users.forEach(user => {
                        const isMember = data.group_member_ids.includes(user.id);

                        const li = document.createElement('li');
                        li.innerHTML = `
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="users[]" value="${user.id}" ${isMember ? 'checked disabled' : ''}>
                                <span>${user.name}</span>
                            </label>
                        `;
                        userList.appendChild(li);
                    });

                    document.getElementById('addMembersForm').dataset.groupId = groupId;
                    document.getElementById('addMemberModal').classList.remove('hidden');
                });
        }

        // Submit selected members
        document.getElementById('addMembersForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const groupId = this.dataset.groupId;
            const formData = new FormData(this);

            fetch(`/groups/${groupId}/add-members`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    showNotificationToast(1, data.message || 'Members Added!');
                    document.getElementById('addMemberModal').classList.add('hidden');
                });
        });

        document.getElementById('closeAddMemberModal').addEventListener('click', function () {
            document.getElementById('addMemberModal').classList.add('hidden');
        });
    </script>

    <script>
        document.getElementById('fileInput').addEventListener('change', function () {
            const previewContainer = document.getElementById('filePreviewContainer');
            previewContainer.innerHTML = ''; // clear previous

            Array.from(this.files).forEach(file => {
                const div = document.createElement('div');
                div.classList.add('file-preview');

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('preview-thumb');
                    img.onload = () => URL.revokeObjectURL(img.src); // cleanup
                    div.appendChild(img);
                }

                const name = document.createElement('p');
                name.textContent = file.name;
                div.appendChild(name);

                previewContainer.appendChild(div);
            });
        });
        function attachMediaListeners() {
            const videos = document.querySelectorAll('video');
            const audios = document.querySelectorAll('audio');
            const images = document.querySelectorAll('img.message-image-preview');

            videos.forEach(video => {
                video.addEventListener('play', () => isMediaPlaying = true);
                video.addEventListener('pause', () => isMediaPlaying = false);
                video.addEventListener('ended', () => isMediaPlaying = false);
            });

            audios.forEach(audio => {
                audio.addEventListener('play', () => isMediaPlaying = true);
                audio.addEventListener('pause', () => isMediaPlaying = false);
                audio.addEventListener('ended', () => isMediaPlaying = false);
            });

            images.forEach(img => {
                img.addEventListener('click', () => isMediaPlaying = true);
                img.addEventListener('load', () => setTimeout(() => isMediaPlaying = false, 5000)); // image view timeout
            });
        }

    </script>
@endsection