let activeReceiverId = null;
let activeGroupId = null;
let isTabActive = true;
let isMediaPlaying = false;
let lastRenderedChatId = null;
const lastMessageMap = {};
const messageSound = new Audio('/sounds/sound.mp3');
const chat_header = document.querySelector('.chat-header-info');

// Pagination variables
let currentPage = 1;
let isLoadingMessages = false;
let hasMoreMessages = true;
let isInitialLoad = true;

// DOM elements
const messagesContainer = document.getElementById('messagesContainer');
const sidebar = document.getElementById('sidebar');
const inputarea = document.getElementById('inputarea');
const chatHeader = document.querySelector('.chat-header-info');
const sidebarMenuButton = document.getElementById('sidebarMenuButton');
const sidebarMenu = document.getElementById('sidebarMenu');
const chatMenuButton = document.getElementById('chatMenuButton');
const chatMenu = document.getElementById('chatMenu');

document.addEventListener('DOMContentLoaded', function () {
    initializeChatUI();
    setupEventListeners();
    refreshSidebar();
    setInterval(pollForUpdates, pollingtime);
    if (window.innerWidth <= 900) {
        sidebar.classList.add('active');
    }
});

function initializeChatUI() {
    messagesContainer.messageIds = new Set();
    messagesContainer.addEventListener('scroll', handleScroll);
}

function setupEventListeners() {
    // Hamburger menu toggle
    document.getElementById('hamburger').addEventListener('click', function () {
        sidebar.classList.toggle('active');
    });

    // Close sidebar
    document.getElementById('hamburgerclose').addEventListener('click', function () {
        sidebar.classList.remove('active');
    });

    sidebarMenuButton.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebarMenu.classList.toggle('active');
        chatMenu.classList.remove('active');
    });

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
            <div class="menu-item" data-group-id="${activeGroupId}" onclick="openGroupMembersModal(activeGroupId);">
                <svg width="20" height="20" fill="currentcolor" viewBox="0 0 100 100" xml:space="preserve"><path d="M57 44H45c-3.3 0-6 2.7-6 6v9c0 1.1.5 2.1 1.2 2.8S41.9 63 43 63v9c0 3.3 2.7 6 6 6h4c3.3 0 6-2.7 6-6v-9c1.1 0 2.1-.4 2.8-1.2.7-.7 1.2-1.7 1.2-2.8v-9c0-3.3-2.7-6-6-6"/><circle cx="51" cy="33" r="7"/><path d="M36.6 66.7c-.2-.2-.5-.4-.7-.6-1.9-2-3-4.5-3-7.1v-9c0-3.2 1.3-6.2 3.4-8.3.6-.6.1-1.7-.7-1.7H26c-3.3 0-6 2.7-6 6v9c0 1.1.5 2.1 1.2 2.8S22.9 59 24 59v9c0 3.3 2.7 6 6 6h4c.9 0 1.7-.2 2.4-.5q.6-.3.6-.9v-5.1c0-.3-.1-.6-.4-.8"/><circle cx="32" cy="29" r="7"/><path d="M76 40h-9.6c-.9 0-1.3 1-.7 1.7 2.1 2.2 3.4 5.1 3.4 8.3v9c0 2.6-1 5.1-3 7.1-.2.2-.4.4-.7.6-.2.2-.4.5-.4.8v5.1c0 .4.2.8.6.9.7.3 1.5.5 2.4.5h4c3.3 0 6-2.7 6-6v-9c1.1 0 2.1-.4 2.8-1.2.7-.7 1.2-1.7 1.2-2.8v-9c0-3.3-2.7-6-6-6"/><circle cx="70" cy="29" r="7"/></svg>
                <span>Show Members</span>
            </div>
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

    // Message input handling
    document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
    document.getElementById('messageInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // File input handling
    document.getElementById('fileInput').addEventListener('change', handleFileUpload);

    // Chat item click handlers
    document.querySelectorAll('.chat-item').forEach(item => {
        item.addEventListener('click', function () {
            const groupId = this.getAttribute('data-group-id');
            const userId = this.getAttribute('data-user-id');

            // Update active chat
            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            if (groupId) {
                switchToGroupChat(groupId);
            } else if (userId) {
                switchToPrivateChat(userId);
            }

            // Update UI
            if (window.innerWidth <= 900) {
                sidebar.classList.remove('active');
            }
        });
    });
}

function switchToGroupChat(groupId) {
    activeGroupId = groupId;
    activeReceiverId = null;
    loadMessages(groupId, true);
}

function switchToPrivateChat(userId) {
    activeReceiverId = userId;
    activeGroupId = null;
    loadMessages(userId, false);
}

function handleScroll() {
    if (messagesContainer.scrollTop < 100 && !isLoadingMessages && hasMoreMessages && !isInitialLoad) {
        loadMoreMessages();
    }
}

function pollForUpdates() {
    if (isMediaPlaying) return;

    if (!isTabActive) {
        refreshSidebar();
        return;
    }

    if (activeReceiverId) {
        checkForNewMessages(activeReceiverId, false);
    } else if (activeGroupId) {
        checkForNewMessages(activeGroupId, true);
    }

    refreshSidebar();
}

function checkForNewMessages(chatId, isGroup = false) {
    const endpoint = isGroup
        ? `/group-messages/${chatId}?latest_only=true`
        : `/messages/${chatId}?latest_only=true`;

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                const isAtBottom = messagesContainer.scrollTop + messagesContainer.clientHeight >= messagesContainer.scrollHeight - 50;
                appendMessages(data.messages, false);

                if (isAtBottom) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }
        })
        .catch(err => console.error("Error checking for new messages:", err));
}

function loadMessages(chatId, isGroup = false) {
    currentPage = 1;
    isInitialLoad = true;
    hasMoreMessages = true;

    const endpoint = isGroup
        ? `/group-messages/${chatId}?page=${currentPage}`
        : `/messages/${chatId}?page=${currentPage}`;

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            if (data.messages) {
                messagesContainer.innerHTML = '';
                messagesContainer.messageIds = new Set();

                appendMessages(data.messages, true);
                hasMoreMessages = data.has_more;

                if (!isGroup) {
                    markMessagesAsRead(chatId);
                } else {
                    markGroupMessagesAsRead(chatId);
                }

                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                lastRenderedChatId = chatId;
                isInitialLoad = false;
            }
        })
        .catch(err => console.error("Failed to load messages", err));
}

function loadMoreMessages() {
    if (isLoadingMessages || !hasMoreMessages) return;

    isLoadingMessages = true;
    currentPage++;

    const endpoint = activeGroupId
        ? `/group-messages/${activeGroupId}?page=${currentPage}`
        : `/messages/${activeReceiverId}?page=${currentPage}`;

    const loader = document.createElement('div');
    loader.className = 'message-loader';
    loader.innerHTML = 'Loading older messages...';
    messagesContainer.insertBefore(loader, messagesContainer.firstChild);

    const scrollPosBefore = messagesContainer.scrollHeight - messagesContainer.scrollTop;

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            messagesContainer.removeChild(loader);

            if (data.messages && data.messages.length > 0) {
                appendMessages(data.messages, false, true);
                hasMoreMessages = data.has_more;
                messagesContainer.scrollTop = messagesContainer.scrollHeight - scrollPosBefore;
            } else {
                hasMoreMessages = false;
            }

            isLoadingMessages = false;
        })
        .catch(err => {
            console.error("Failed to load more messages", err);
            messagesContainer.removeChild(loader);
            isLoadingMessages = false;
            currentPage--;
        });
}

function appendMessages(messages, isInitialLoad = false, prepend = false) {
    if (!messagesContainer.messageIds) {
        messagesContainer.messageIds = new Set();
    }

    messages.forEach(msg => {
        if (messagesContainer.messageIds.has(msg.id)) return;
        messagesContainer.messageIds.add(msg.id);

        const messageElement = createMessageElement(msg);

        if (prepend) {
            messagesContainer.insertBefore(messageElement, messagesContainer.firstChild);
        } else {
            messagesContainer.appendChild(messageElement);
        }
    });

    if (isInitialLoad && !prepend) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    attachMediaListeners();
}

function createMessageElement(msg) {
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

    messageInfo.innerHTML = isSent
        ? `<div class="message message-sent">
            ${content}
            <div class="message-time">${timeFormatted} ${ticks}</div>
        </div>`
        : `<span class="message-sender">${senderName}</span>
        <div class="message message-received">
           ${content}
            <div class="message-time">${timeFormatted}</div>
        </div>`;

    return messageInfo;
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

function sendMessage() {
    const fileInput = document.getElementById('fileInput');
    const files = fileInput.files;
    const message = document.getElementById('messageInput').value.trim();
    const sendButton = document.getElementById('sendMessageBtn');

    if (!message && files.length === 0) return;
    sendButton.innerHTML = `<span class="loader"></span>`;
    sendButton.disabled = true;

    const formData = new FormData();
    if (message) formData.append('message', message);
    if (files.length > 0) {
        formData.append('file', files[0]);
    }

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
        body: formData
    })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await res.text();
                throw new Error(text);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                fileInput.value = '';
                document.getElementById('filePreviewContainer').innerHTML = '';
                document.getElementById('filePreviewContainer').style.display = 'none';
                document.getElementById('messageInput').value = '';
                sendButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                    <path d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z" fill="#fff" />
                </svg>`;
                sendButton.disabled = false;

                if (activeReceiverId) {
                    checkForNewMessages(activeReceiverId, false);
                } else if (activeGroupId) {
                    checkForNewMessages(activeGroupId, true);
                }
            } else {
                showNotificationToast(3, data.message || 'Failed to send message');
            }
        })
        .catch(err => {
            console.error("Error sending message:", err);
            showNotificationToast(3, 'Failed to send message');
            sendButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                <path d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z" fill="#fff" />
            </svg>`;
            sendButton.disabled = false;
        });
}

function handleFileUpload() {
    const previewContainer = document.getElementById('filePreviewContainer');
    previewContainer.innerHTML = '';

    Array.from(this.files).forEach(file => {
        const div = document.createElement('div');
        div.classList.add('file-preview');

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.classList.add('preview-thumb');
            img.onload = () => URL.revokeObjectURL(img.src);
            div.appendChild(img);
        }

        const name = document.createElement('p');
        name.textContent = file.name;
        div.appendChild(name);

        previewContainer.appendChild(div);
        previewContainer.style.display = 'flex';
    });
}

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
        img.addEventListener('load', () => setTimeout(() => isMediaPlaying = false, 5000));
    });
}

function markMessagesAsRead(senderId) {
    fetch('/messages/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ sender_id: senderId })
    }).catch(err => console.error('Error marking messages as read:', err));
}

function markGroupMessagesAsRead(groupId) {
    fetch(`/group/${groupId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).catch(err => console.error("Failed to mark group messages as read", err));
}

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

                const lastKey = `group_${group.id}`;
                if (!isTabActive && group.last_message && group.unread_count > 0 && lastMessageMap[lastKey] !== group.last_message) {
                    lastMessageMap[lastKey] = group.last_message;
                    showChatNotification(group.name, group.last_message);
                }
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

                const lastKey = `user_${user.id}`;
                if (!isTabActive && user.last_message && user.unread_count > 0 && lastMessageMap[lastKey] !== user.last_message) {
                    lastMessageMap[lastKey] = user.last_message;
                    showChatNotification(user.username, user.last_message);
                }
            });

            // Re-attach click events
            document.querySelectorAll('.chat-item').forEach(item => {
                item.addEventListener('click', function () {
                    const userName = this.querySelector('.chat-name').textContent;
                    const userAvatar = this.querySelector('.chat-avatar').src;
                    document.querySelector('.chat-header-info .chat-name').textContent = userName;
                    document.querySelector('.chat-header-info .user-avatar').src = userAvatar;
                    const groupId = this.getAttribute('data-group-id');
                    const userId = this.getAttribute('data-user-id');
                    inputarea.style.display = 'flex';
                    chat_header.style.display = 'flex';

                    document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    if (groupId) {
                        switchToGroupChat(groupId);
                    } else if (userId) {
                        switchToPrivateChat(userId);
                    }

                    if (window.innerWidth <= 900) {
                        sidebar.classList.remove('active');
                    }
                });
            });
        })
        .catch(err => console.error('Failed to refresh sidebar:', err));
}

function showChatNotification(title, body) {
    if (Notification.permission === 'granted' && document.hidden) {
        const notification = new Notification(title, {
            body: body,
            icon: icon,
            tag: 'chat-notification'
        });

        messageSound.play().catch(e => console.warn("Sound play failed:", e));

        startTabFlash(`${title}: ${body}`);

        notification.onclick = function () {
            window.focus();
            notification.close();
        };
    }
}

function showNotificationToast(code = 1, message = "Success", duration = 2000) {
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
            messageSpan.innerHTML = '<svg width="20" height="20" viewBox="0 0 52 52" fill="#fff" xml:space="preserve"><path d="M26 2C12.8 2 2 12.8 2 26s10.8 24 24 24 24-10.8 24-24S39.2 2 26 2M8 26c0-9.9 8.1-18 18-18 3.9 0 7.5 1.2 10.4 3.3L11.3 36.4C9.2 33.5 8 29.9 8 26m18 18c-3.9 0-7.5-1.2-10.4-3.3l25.1-25.1C42.8 18.5 44 22.1 44 26c0 9.9-8.1 18-18 18"/></svg>' + message;
            break;
        default:
            toast.classList.add('notification-success');
            messageSpan.innerHTML = '<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16m3.293 4.293L10 13.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l6-6a1 1 0 1 0-1.414-1.414"/></svg>' + message;
    }

    toast.classList.add('show');
    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 400);
    }, duration);
}

let originalTitle = document.title;
let flashInterval = null;

function startTabFlash(newTitle) {
    if (flashInterval) return;
    flashInterval = setInterval(() => {
        document.title = document.title === originalTitle ? newTitle : originalTitle;
    }, 1000);
}

function stopTabFlash() {
    clearInterval(flashInterval);
    flashInterval = null;
    document.title = originalTitle;
}

// Initialize tab visibility tracking
document.addEventListener("visibilitychange", () => {
    isTabActive = !document.hidden;
    if (isTabActive) {
        stopTabFlash();
    }
});