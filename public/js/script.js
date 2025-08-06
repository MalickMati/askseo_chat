let activeReceiverId = null;
let activeGroupId = null;
let isTabActive = true;
let isMediaPlaying = false;
let isFilteredView = false;
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
    if (localStorage.getItem('theme') === 'light') {
        document.documentElement.classList.add('light-theme');
    }
});

window.addEventListener('load', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        setTimeout(() => {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                } else {
                    console.log('Notification permission denied.');
                }
            });
        }, 1000);
    }
});

function toggleTheme() {
    const root = document.documentElement;
    root.classList.toggle('light-theme');
    localStorage.setItem('theme', root.classList.contains('light-theme') ? 'light' : 'dark');
}

function initializeChatUI() {
    messagesContainer.messageIds = new Set();
    messagesContainer.addEventListener('scroll', handleScroll);
}

function setupEventListeners() {
    // Hamburger menu toggle
    document.getElementById('hamburger').addEventListener('click', function () {
        sidebar.classList.toggle('active');
    });

    document.addEventListener('click', function (event) {
        // If the menu is open
        if (chatMenu.classList.contains('active')) {
            // Check if the clicked target is not the menu or the menu button
            if (!chatMenu.contains(event.target) && !chatMenuButton.contains(event.target)) {
                chatMenu.classList.remove('active');
            }
        }
        if (sidebarMenu.classList.contains('active')) {
            // Check if the clicked target is not the menu or the menu button
            if (!sidebarMenu.contains(event.target) && !sidebarMenuButton.contains(event.target)) {
                sidebarMenu.classList.remove('active');
            }
        }
    });

    window.addEventListener('click', function (e) {
        const modal = document.getElementById('groupMembersModal');
        if (e.target === modal) {
            closeGroupMembersModal();
        }
    });

    document.getElementById('messageInput').addEventListener('paste', async function (e) {
        const clipboardItems = e.clipboardData.items;
        for (let item of clipboardItems) {
            if (item.type.indexOf("image") !== -1) {
                const blob = item.getAsFile();
                const fileInput = document.getElementById('fileInput');

                const dt = new DataTransfer();
                dt.items.add(blob);
                fileInput.files = dt.files;

                handleFileUpload.call(fileInput);
                break;
            }
        }
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
            <div class="menu-item" data-group-id="${activeGroupId}" data-filter="links">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentcolor"><path d="m10 17.55-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43 3 3 0 0 0-.18-.21 4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55M20.69 3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43 3 3 0 0 0 .23.21 4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33"/></svg>
                <span>Links</span>
            </div>
            <div class="menu-item" data-group-id="${activeGroupId}" data-filter="media">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentcolor"><path d="M13 1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 2H3v10h10zM9.5 8l2.5 2.857V12H4v-1.2L5.5 9l1.524 1.83zm-3-3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3"/></svg>
                <span>Media</span>
            </div>
            <div class="menu-item" data-group-id="${activeGroupId}" data-filter="documents">
                <svg width="20" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#a)"><path d="M12 13h3m-3 3h8m-8 4h8m-8 4h8m1-17V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v22s0 1 1 1h1m23 2h4a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6m2 27a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1z" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h32v32H0z"/></clipPath></defs></svg>
                <span>Files</span>
            </div>
            ${isAdmin ? `<div class="menu-item" data-group-id="${activeGroupId}" onclick="openAddMemberModal(activeGroupId);">
                <svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 4a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6V5a1 1 0 0 1 1-1" fill="currentcolor"/></svg>
                <span>Add Member</span>
            </div>` : ''}
            ${isAdmin ? `<div class="menu-item" data-group-id="${activeGroupId}" onclick="openRemoveMemberModal(activeGroupId);">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentcolor"><path d="M14 3a1 1 0 0 1 0 2h-.154l-.704 9.153A2 2 0 0 1 11.148 16H4.852a2 2 0 0 1-1.994-1.847L2.154 5H2a1 1 0 1 1 0-2h3V2A2 2 0 0 1 6.85.005L7 0h2a2 2 0 0 1 1.995 1.85L11 2v1zm-2.16 2H4.159l.693 9h6.296zM9 2H7v1h2z"/></svg>
                <span>Remove Member</span>
            </div>` : ''}
            <div class="menu-item" data-group-id="${activeGroupId}" onclick="openGroupMembersModal(activeGroupId);">
                <svg width="20" height="20" fill="currentcolor" viewBox="0 0 100 100" xml:space="preserve"><path d="M57 44H45c-3.3 0-6 2.7-6 6v9c0 1.1.5 2.1 1.2 2.8S41.9 63 43 63v9c0 3.3 2.7 6 6 6h4c3.3 0 6-2.7 6-6v-9c1.1 0 2.1-.4 2.8-1.2.7-.7 1.2-1.7 1.2-2.8v-9c0-3.3-2.7-6-6-6"/><circle cx="51" cy="33" r="7"/><path d="M36.6 66.7c-.2-.2-.5-.4-.7-.6-1.9-2-3-4.5-3-7.1v-9c0-3.2 1.3-6.2 3.4-8.3.6-.6.1-1.7-.7-1.7H26c-3.3 0-6 2.7-6 6v9c0 1.1.5 2.1 1.2 2.8S22.9 59 24 59v9c0 3.3 2.7 6 6 6h4c.9 0 1.7-.2 2.4-.5q.6-.3.6-.9v-5.1c0-.3-.1-.6-.4-.8"/><circle cx="32" cy="29" r="7"/><path d="M76 40h-9.6c-.9 0-1.3 1-.7 1.7 2.1 2.2 3.4 5.1 3.4 8.3v9c0 2.6-1 5.1-3 7.1-.2.2-.4.4-.7.6-.2.2-.4.5-.4.8v5.1c0 .4.2.8.6.9.7.3 1.5.5 2.4.5h4c3.3 0 6-2.7 6-6v-9c1.1 0 2.1-.4 2.8-1.2.7-.7 1.2-1.7 1.2-2.8v-9c0-3.3-2.7-6-6-6"/><circle cx="70" cy="29" r="7"/></svg>
                <span>Show Members</span>
            </div>
            <div class="menu-item" data-group-id="${activeGroupId}" onclick="leave_chat_group(activeGroupId);">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="currentcolor"><path fill-rule="evenodd" d="M11.707 3.293 15.414 7l-3.707 3.707a1 1 0 0 1-1.414-1.414L11.586 8H4.5a1.5 1.5 0 1 0 0 3H6a1 1 0 1 1 0 2H4.5a3.5 3.5 0 1 1 0-7h7.086l-1.293-1.293a1 1 0 1 1 1.414-1.414"/></svg>
                <span>Leave Group</span>
            </div>
        `;
        } else if (activeReceiverId) {
            chatMenu.innerHTML = `
        <div class="menu-item" data-receiver-id="${activeReceiverId}}" data-filter="links">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentcolor"><path d="m10 17.55-1.77 1.72a2.47 2.47 0 0 1-3.5-3.5l4.54-4.55a2.46 2.46 0 0 1 3.39-.09l.12.1a1 1 0 0 0 1.4-1.43 3 3 0 0 0-.18-.21 4.46 4.46 0 0 0-6.09.22l-4.6 4.55a4.48 4.48 0 0 0 6.33 6.33L11.37 19A1 1 0 0 0 10 17.55M20.69 3.31a4.49 4.49 0 0 0-6.33 0L12.63 5A1 1 0 0 0 14 6.45l1.73-1.72a2.47 2.47 0 0 1 3.5 3.5l-4.54 4.55a2.46 2.46 0 0 1-3.39.09l-.12-.1a1 1 0 0 0-1.4 1.43 3 3 0 0 0 .23.21 4.47 4.47 0 0 0 6.09-.22l4.55-4.55a4.49 4.49 0 0 0 .04-6.33"/></svg>
            <span>Links</span>
        </div>
        <div class="menu-item" data-receiver-id="${activeReceiverId}}" data-filter="media">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="currentcolor"><path d="M13 1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 2H3v10h10zM9.5 8l2.5 2.857V12H4v-1.2L5.5 9l1.524 1.83zm-3-3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3"/></svg>
            <span>Media</span>
        </div>
        <div class="menu-item" data-receiver-id="${activeReceiverId}}" data-filter="documents">
            <svg width="20" height="20" viewBox="0 0 32 32" fill="none"><g clip-path="url(#a)"><path d="M12 13h3m-3 3h8m-8 4h8m-8 4h8m1-17V2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v22s0 1 1 1h1m23 2h4a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6m2 27a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1z" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h32v32H0z"/></clipPath></defs></svg>
            <span>Files</span>
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
    if (isMediaPlaying || isFilteredView) {
        refreshSidebar();
        return;
    }

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
        const extIcons = {
            pdf: '<svg width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 6.5V6H2v.5zm4 0V6H6v.5zm0 4H6v.5h.5zm7-7h.5v-.207l-.146-.147zm-3-3 .354-.354L10.707 0H10.5zM2.5 7h1V6h-1zm.5 4V8.5H2V11zm0-2.5v-2H2v2zm.5-.5h-1v1h1zm.5-.5a.5.5 0 0 1-.5.5v1A1.5 1.5 0 0 0 5 7.5zM3.5 7a.5.5 0 0 1 .5.5h1A1.5 1.5 0 0 0 3.5 6zM6 6.5v4h1v-4zm.5 4.5h1v-1h-1zM9 9.5v-2H8v2zM7.5 6h-1v1h1zM9 7.5A1.5 1.5 0 0 0 7.5 6v1a.5.5 0 0 1 .5.5zM7.5 11A1.5 1.5 0 0 0 9 9.5H8a.5.5 0 0 1-.5.5zM10 6v5h1V6zm.5 1H13V6h-2.5zm0 2H12V8h-1.5zM2 5V1.5H1V5zm11-1.5V5h1V3.5zM2.5 1h8V0h-8zm7.646-.146 3 3 .708-.708-3-3zM2 1.5a.5.5 0 0 1 .5-.5V0A1.5 1.5 0 0 0 1 1.5zM1 12v1.5h1V12zm1.5 3h10v-1h-10zM14 13.5V12h-1v1.5zM12.5 15a1.5 1.5 0 0 0 1.5-1.5h-1a.5.5 0 0 1-.5.5zM1 13.5A1.5 1.5 0 0 0 2.5 15v-1a.5.5 0 0 1-.5-.5z" fill="currentcolor"/></svg>',
            zip: '<svg height="20" width="20" fill="currentcolor" viewBox="0 0 512 512" xml:space="preserve"><path d="M413.4 0H114.7C91.1 0 72 19.1 72 42.7v426.7c0 23.5 19.1 42.7 42.7 42.7h298.7c23.5 0 42.7-19.1 42.7-42.7V42.7C456 19.1 436.9 0 413.4 0m-192 469.3L242.7 320h42.7l21.3 149.3zM328 128h-64v42.7h64v42.7h-64V256h64v42.7h-64V256h-64v-42.7h64v-42.7h-64V128h64V85.3h-64V42.7h64v42.7h64zm-74.6 277.3L242.7 448h42.7l-10.7-42.7z"/></svg>',
            doc: '<svg width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 10V7h.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5zm4-2.5a.5.5 0 0 1 1 0v2a.5.5 0 0 1-1 0z" fill="currentcolor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5A1.5 1.5 0 0 1 2.5 0h8.207L14 3.293V13.5a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 1 13.5zM3.5 6H2v5h1.5A1.5 1.5 0 0 0 5 9.5v-2A1.5 1.5 0 0 0 3.5 6m4 0A1.5 1.5 0 0 0 6 7.5v2a1.5 1.5 0 0 0 3 0v-2A1.5 1.5 0 0 0 7.5 6m2.5 5V6h3v2h-1V7h-1v3h1V9h1v2z" fill="currentcolor"/></svg>',
            xls: '<svg width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5A1.5 1.5 0 0 1 2.5 0h8.207L14 3.293V13.5a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 1 13.5zm2 5.793V6H2v1.707l.793.793L2 9.293V11h1V9.707l.5-.5.5.5V11h1V9.293L4.207 8.5 5 7.707V6H4v1.293l-.5.5zM6 6h1v4h2v1H6zm7 0h-3v3h2v1h-2v1h3V8h-2V7h2z" fill="currentcolor"/></svg>',
            ppt: '<svg width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 8h.5a.5.5 0 0 0 0-1H3zm4 0h.5a.5.5 0 0 0 0-1H7z" fill="currentcolor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5A1.5 1.5 0 0 1 2.5 0h8.207L14 3.293V13.5a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 1 13.5zM2 6h1.5a1.5 1.5 0 1 1 0 3H3v2H2zm4 0h1.5a1.5 1.5 0 1 1 0 3H7v2H6zm5 5h1V7h1V6h-3v1h1z" fill="currentcolor"/></svg>',
            sql: `<svg width="20" height="20" viewBox="0 0 32 32"><path d="M8.562 15.256A21.2 21.2 0 0 0 16 16.449a21.2 21.2 0 0 0 7.438-1.194c1.864-.727 2.525-1.535 2.525-2V9.7a10.4 10.4 0 0 1-2.084 1.076A22.3 22.3 0 0 1 16 12.078a22.4 22.4 0 0 1-7.879-1.3A10.3 10.3 0 0 1 6.037 9.7v3.55c0 .474.663 1.278 2.525 2.006m0 6.705a15.6 15.6 0 0 0 2.6.741 25 25 0 0 0 4.838.453 25 25 0 0 0 4.838-.452 15.6 15.6 0 0 0 2.6-.741c1.864-.727 2.525-1.535 2.525-2v-3.39a10.7 10.7 0 0 1-1.692.825A23.5 23.5 0 0 1 16 18.74a23.5 23.5 0 0 1-8.271-1.348 11 11 0 0 1-1.692-.825v3.393c0 .466.663 1.271 2.525 2.001M16 30c5.5 0 9.963-1.744 9.963-3.894v-2.837a10.5 10.5 0 0 1-1.535.762l-.157.063A23.5 23.5 0 0 1 16 25.445a23.4 23.4 0 0 1-8.271-1.351l-.157-.063a10.5 10.5 0 0 1-1.535-.762v2.837C6.037 28.256 10.5 30 16 30" style="fill:currentcolor"/><ellipse cx="16" cy="5.894" rx="9.963" ry="3.894" style="fill:currentcolor"/></svg>`,
            apk: `<svg width="20" height="20" viewBox="0 0 550.801 550.801" xml:space="preserve" fill="currentcolor"><path d="M136.129 282.393c-2.753-9.181-5.508-20.656-7.802-29.834h-.453c-2.3 9.178-4.602 20.891-7.117 29.834l-9.181 32.827h34.188zm134.051-30.752c-7.117 0-11.934.686-14.468 1.377v45.67c2.987.686 6.661.918 11.712.918 18.597 0 30.062-9.408 30.062-25.255 0-14.223-9.872-22.71-27.306-22.71"/><path d="M488.427 197.019h-13.226v-63.822c0-.401-.063-.799-.116-1.205-.021-2.531-.828-5.023-2.563-6.993L366.325 3.694c-.031-.034-.063-.045-.084-.076-.633-.709-1.371-1.298-2.151-1.804-.232-.158-.465-.287-.707-.422a11.3 11.3 0 0 0-2.131-.896c-.2-.053-.379-.135-.58-.19A11 11 0 0 0 358.193 0H97.201c-11.918 0-21.6 9.693-21.6 21.601v175.413H62.378c-17.049 0-30.874 13.818-30.874 30.87v160.542c0 17.044 13.824 30.876 30.874 30.876h13.223V529.2c0 11.907 9.682 21.601 21.6 21.601h356.4c11.907 0 21.601-9.693 21.601-21.601V419.302h13.226c17.044 0 30.87-13.827 30.87-30.87V227.89c-.001-17.058-13.827-30.871-30.871-30.871M97.201 21.601h250.193v110.51c0 5.967 4.841 10.8 10.8 10.8h95.407v54.108h-356.4zm234.942 251.843c0 15.14-5.052 27.997-14.222 36.719-11.944 11.243-29.61 16.3-50.274 16.3-4.583 0-8.709-.237-11.929-.69v55.308h-34.652V228.456c10.79-1.83 25.943-3.206 47.274-3.206 21.584 0 36.951 4.126 47.287 12.382 9.866 7.807 16.516 20.661 16.516 35.812M95.516 381.08h-36.26l47.271-154.691h45.9l47.965 154.691h-37.645l-11.929-39.704h-44.292zm358.085 142.267h-356.4V419.302h356.4zM440.259 381.08l-37.874-66.783-13.31 16.295v50.488h-34.657V226.389h34.657v68.392h.686c3.438-5.964 7.113-11.47 10.558-16.985l35.121-51.411h42.909l-51.188 65.87 53.937 88.815H440.26z"/></svg>`,
            default: '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2 5C.895 5 0 5.9 0 7v14c0 1.1.895 2 2 2h20c1.105 0 2-.9 2-2V7c0-1.1-.895-2-2-2z" fill="currentcolor"/><path d="M3 1c-1.105 0-2 .9-2 2v14c0 1.1.895 2 2 2h18c1.105 0 2-.9 2-2V5c0-1.1-.895-2-2-2h-8l-3-2z" fill="currentcolor"/><path d="M23 14V6c0-1.1-.895-2-2-2H3c-1.105 0-2 .9-2 2v8z" fill="#bdc3c7"/><path d="M2 5C.895 5 0 5.9 0 7v13c0 1.1.895 2 2 2h20c1.105 0 2-.9 2-2V7c0-1.1-.895-2-2-2z" fill="currentcolor"/></svg>'
        };
        const icon = extIcons[ext.toLowerCase()] || extIcons.default;
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
            content = `
                <div class="file-preview-card">
                    <div class="file-icon">
                        ${icon}
                    </div>
                    <div class="file-details">
                        <a href="${fileUrl}" download title="Download ${fileName}">${fileName}</a>
                        <small>.${ext.toLowerCase()} file</small>
                    </div>
                </div>`;
        }
    } else {
        // content = msg.message || '';
        if (msg.message) {
            const urlRegex = /\b((https?:\/\/)?(([\w-]+\.)+[\w-]{2,})(\/[\w\-._~:/?#[\]@!$&'()*+,;=%]*)?)\b/gi;
            const escaped = msg.message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
            content = escaped.replace(urlRegex, function (url) {
                let href = url;
                if (!href.startsWith("http")) {
                    href = "https://" + href;
                }
                return `<a href="${href}" target="_blank" class="detected-link">${url}</a>`;
            });
        } else {
            content = '';
        }
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

    // Inject circular progress UI
    sendButton.innerHTML = `
        <div class="circular-progress">
            <svg viewBox="0 0 36 36">
                <circle class="bg" cx="18" cy="18" r="16"></circle>
                <circle class="progress" cx="18" cy="18" r="16" stroke-dasharray="100" stroke-dashoffset="100"></circle>
            </svg>
            <div class="percent">0%</div>
        </div>
    `;
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
    const xhr = new XMLHttpRequest();

    xhr.open("POST", route, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

    const progressCircle = sendButton.querySelector(".progress");
    const percentText = sendButton.querySelector(".percent");

    xhr.upload.addEventListener("progress", function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            const dashoffset = 100 - percent;
            progressCircle.style.strokeDashoffset = dashoffset;
            percentText.textContent = `${percent}%`;
        }
    });

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            sendButton.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                    <path d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z" fill="#fff" />
                </svg>`;
            sendButton.disabled = false;

            try {
                const data = JSON.parse(xhr.responseText);
                if (data.success) {
                    fileInput.value = '';
                    document.getElementById('filePreviewContainer').innerHTML = '';
                    document.getElementById('filePreviewContainer').style.display = 'none';
                    document.getElementById('messageInput').value = '';

                    if (activeReceiverId) {
                        checkForNewMessages(activeReceiverId, false);
                    } else if (activeGroupId) {
                        checkForNewMessages(activeGroupId, true);
                    }
                } else {
                    showNotificationToast(3, data.message || 'Failed to send message');
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                }
            } catch (err) {
                console.error("Invalid JSON response", err);
                showNotificationToast(3, 'Unexpected server response');
            }
        }
    };

    xhr.onerror = function () {
        sendButton.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
            <path d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z" fill="#fff" />
        </svg>`;
        sendButton.disabled = false;
        showNotificationToast(3, 'Failed to send message');
    };

    xhr.send(formData);
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
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showNotificationToast(3, data.message);
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            }
        })
        .catch(err => console.error("Failed to mark group messages as read", err));
}

function refreshSidebar() {
    fetch('/sidebar/data')
        .then(res => res.json())
        .then(data => {
            const chatList = document.querySelector('.chat-list');
            chatList.innerHTML = ''; // 🔁 Clear existing list

            // === Rebuild group chats ===
            data.groups.forEach(group => {
                const item = document.createElement('div');
                item.className = 'chat-item';
                item.setAttribute('data-group-id', group.id);
                item.innerHTML = `
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
                `;
                chatList.appendChild(item);

                const key = `group_${group.id}`;
                if (!isTabActive && group.last_message && group.unread_count > 0 && lastMessageMap[key] !== group.last_message) {
                    lastMessageMap[key] = group.last_message;
                    showChatNotification(group.name, group.last_message);
                }
            });

            // === Rebuild private chats ===
            data.users.forEach(user => {
                const item = document.createElement('div');
                item.className = 'chat-item';
                item.setAttribute('data-user-id', user.id);
                item.innerHTML = `
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
                `;
                chatList.appendChild(item);

                const key = `user_${user.id}`;
                if (!isTabActive && user.last_message && user.unread_count > 0 && lastMessageMap[key] !== user.last_message) {
                    lastMessageMap[key] = user.last_message;
                    showChatNotification(user.username, user.last_message);
                }
            });

            // === Reattach click handlers ===
            document.querySelectorAll('.chat-item').forEach(item => {
                item.onclick = function () {
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
                };
            });

            // === Mark current chat as read ===
            if (activeReceiverId && !isMediaPlaying && !isFilteredView) {
                markMessagesAsRead(activeReceiverId);
            }
            if (activeGroupId && !isMediaPlaying && !isFilteredView) {
                markGroupMessagesAsRead(activeGroupId);
            }
        })
        .catch(err => console.error('Failed to refresh sidebar:', err));
}

function showChatNotification(title, body) {
    // Only vibrate/sound if tab is hidden (i.e. backgrounded)
    const isTabHidden = document.hidden;

    if (isTabHidden) {
        // Try vibration (most Android phones support it)
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200]);
        }

        // Play sound (if user has interacted previously)
        messageSound.play().catch(e => console.warn("Sound play failed:", e));

        // Desktop Notification
        if (Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: body,
                icon: icon,
                tag: 'chat-notification'
            });

            notification.onclick = function () {
                window.focus();
                notification.close();
            };
        }
    } else {
        // Foreground tab: play sound immediately
        messageSound.play().catch(e => console.warn("Sound play failed:", e));
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

// Delegate clicks on the whole document or a container
document.body.addEventListener('click', function (event) {
    const button = event.target.closest('.menu-item');
    if (!button) return;

    const groupId = button.getAttribute('data-group-id');
    const receiverId = button.getAttribute('data-receiver-id');
    const filterType = button.getAttribute('data-filter');
    // e.g. 'links', 'media', 'documents'

    if (!filterType) {
        return;
    }

    let url = "";
    messagesContainer.innerHTML = '';

    if (groupId && groupId !== "null") {
        url = `/chat/filter/group-messages?group_id=${groupId}&filter=${filterType}`;
    } else if (receiverId && receiverId !== "null") {
        url = `/chat/filter/private-messages?receiver_id=${receiverId}&filter=${filterType}`;
    } else {
        console.error('Neither group_id nor receiver_id found on the clicked menu-item.');
        return;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            displayFilteredMessages(data.messages.data, filterType);
        })
        .catch(err => {
            console.error('Error fetching filtered messages:', err);
        });
});

function displayFilteredMessages(messages, filter) {
    isFilteredView = true;
    messagesContainer.innerHTML = '';
    chatMenu.classList.remove('active');
    while (messagesContainer.firstChild) {
        messagesContainer.removeChild(messagesContainer.firstChild);
    }

    if (messagesContainer.messageIds instanceof Set) {
        messagesContainer.messageIds.clear();
    } else {
        messagesContainer.messageIds = new Set();
    }
    inputarea.style.display = 'none';
    if (messages.length === 0) {
        let typeLabel = filter;
        messagesContainer.innerHTML = '';
        messagesContainer.innerHTML = `
            <div class="no-messages">
                No ${typeLabel.toLowerCase()} found in this chat.
            </div>
        `;
        return;
    }
    appendMessages(messages, true);
}

function leave_chat_group(groupId) {
    if (!confirm("Are you sure you want to leave this group?")) return;

    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/groups/${groupId}/leave`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Failed to leave group");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotificationToast(1, 'You left the group!');
                messagesContainer.innerHTML = '';
                messagesContainer.innerHTML = '';
            } else {
                showNotificationToast(2, data.message);
            }
        })
        .catch(error => {
            console.error("Error leaving group:", error);
            showNotificationToast(3, 'Error while leaving the group!');
        });
}

function openAddMemberModal(groupId) {
    const modal = document.getElementById('addMemberModal');
    const userList = document.getElementById('userList');
    userList.innerHTML = '';
    modal.classList.remove('hidden');

    fetch(`/groups/${groupId}/members-list`)
        .then(res => res.json())
        .then(data => {
            const allUsers = data.all_users || [];
            const memberIds = new Set(data.group_member_ids || []);

            const usersToAdd = allUsers.filter(user => !memberIds.has(user.id));

            if (usersToAdd.length === 0) {
                userList.innerHTML = '<li>No users available to add.</li>';
                return;
            }

            usersToAdd.forEach(user => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <label>
                        <input type="checkbox" value="${user.id}">
                        ${user.name}
                    </label>
                `;
                userList.appendChild(li);
            });
        })
        .catch(err => {
            console.error(err);
            userList.innerHTML = '<li>Error loading user list</li>';
        });

    const form = document.getElementById('addMembersForm');
    form.onsubmit = function (e) {
        e.preventDefault();

        const selectedIds = Array.from(
            userList.querySelectorAll('input[type="checkbox"]:checked')
        ).map(cb => cb.value);

        if (selectedIds.length === 0) {
            showNotificationToast(2, 'Please select at least one user', 3000);
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/groups/${groupId}/add-members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ users: selectedIds })
        })
            .then(res => res.json())
            .then(data => {
                showNotificationToast(1, 'Users added');
                modal.classList.add('hidden');
            })
            .catch(err => {
                console.error(err);
                showNotificationToast(3, 'Error adding users!', 5000);
            });
    };
}

// Modal close
document.getElementById('closeAddMemberModal').addEventListener('click', () => {
    document.getElementById('addMemberModal').classList.add('hidden');
});

function openGroupMembersModal(groupId) {
    const modal = document.getElementById('groupMembersModal');
    const membersList = document.getElementById('groupMembersList');
    const countSpan = document.getElementById('groupMembersCount');

    // Reset modal content
    membersList.innerHTML = '<li>Loading members...</li>';
    countSpan.textContent = '0';

    fetch(`/group/${groupId}/members`)
        .then(res => res.json())
        .then(data => {
            membersList.innerHTML = ''; // Clear loader text

            if (!data.members || data.members.length === 0) {
                membersList.innerHTML = '<li class="custom-member-item">No members found.</li>';
                countSpan.textContent = '0';
                return;
            }

            data.members.forEach(member => {
                const li = document.createElement('li');
                li.classList.add('custom-member-item');
                li.innerHTML = `
                    <img src="${member.avatar_url}" alt="${member.name}">
                    <span>${member.name}</span>
                `;
                membersList.appendChild(li);
            });

            countSpan.textContent = data.members.length;

            // Show modal
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching members:', error);
            membersList.innerHTML = '<li class="custom-member-item">Failed to load members.</li>';
        });
}

function closeGroupMembersModal() {
    document.getElementById('groupMembersModal').style.display = 'none';
}

function openRemoveMemberModal(groupId) {
    fetch(`/groups/${groupId}/members-list`)
        .then(res => res.json())
        .then(data => {
            const userList = document.getElementById("removeUserList");
            const countSpan = document.getElementById("removeMembersCount");

            userList.innerHTML = "";
            let count = 0;

            data.all_users.forEach(user => {
                if (
                    data.group_member_ids.includes(user.id) &&
                    user.id !== currentUserId
                ) {
                    count++;
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" name="users[]" value="${user.id}" style="margin-right: 10px;">
                            <span>${user.name}</span>
                        </label>
                    `;
                    userList.appendChild(li);
                }
            });

            countSpan.textContent = count;
            document.getElementById('removeMemberModal').style.display = 'block';

            // Attach submit handler
            const form = document.getElementById('removeMembersForm');
            form.onsubmit = function (e) {
                e.preventDefault();

                const selected = [...form.querySelectorAll('input[name="users[]"]:checked')]
                    .map(cb => cb.value);

                if (selected.length === 0) return;

                fetch(`/groups/${groupId}/remove-members`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ users: selected })
                })
                    .then(res => res.json())
                    .then(response => {
                        showNotificationToast(1, 'Members Removed!');
                        closeRemoveMemberModal();
                    })
                    .catch(err => console.error(err));
            };
        });
}

function closeRemoveMemberModal() {
    document.getElementById("removeMemberModal").style.display = "none";
}
