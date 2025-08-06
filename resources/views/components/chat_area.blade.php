<div class="chat-area">
    <div class="chat-header">
        <button class="hamburger" id="hamburger">
            <svg width="20" height="20" fill="#54656F" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M2 3h12a1 1 0 0 1 0 2H2a1 1 0 1 1 0-2m0 4h12a1 1 0 0 1 0 2H2a1 1 0 1 1 0-2m0 4h12a1 1 0 0 1 0 2H2a1 1 0 0 1 0-2" />
            </svg>
        </button>
        <div class="chat-header-info">
            <img src="" alt="" class="user-avatar">
            <div style="margin-left: 15px;">
                <div class="chat-name"></div>
                <div style="font-size: 13px; color: #667781;"></div>
            </div>
        </div>
        <div class="chat-header-actions">
            <svg id="chatMenuButton" style="cursor:pointer;" width="20" height="20" fill="#54656F" viewBox="0 0 256 256">
                <path
                    d="M156 128a28 28 0 1 1-28-28 28.03 28.03 0 0 1 28 28m-28-52a28 28 0 1 0-28-28 28.03 28.03 0 0 0 28 28m0 104a28 28 0 1 0 28 28 28.03 28.03 0 0 0-28-28" />
            </svg>
        </div>
    </div>

    <div class="messages-container" id="messagesContainer">
        
    </div>

    <div class="input-area" id="inputarea">
        <div id="filePreviewContainer" class="file-preview-container"></div>
        <label for="fileInput">
            <svg style="cursor: pointer;" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M17.366 4.705a2.75 2.75 0 0 0-3.89 0l-8.131 8.132a4.25 4.25 0 1 0 6.01 6.01l8.762-8.762a.75.75 0 1 1 1.06 1.061l-8.761 8.762a5.75 5.75 0 1 1-8.132-8.132l8.132-8.131a4.25 4.25 0 1 1 6.01 6.01l-7.793 7.794a2.75 2.75 0 0 1-3.89-3.89l7.195-7.193a.75.75 0 1 1 1.06 1.06L7.804 14.62a1.25 1.25 0 0 0 1.768 1.768l7.794-7.794a2.75 2.75 0 0 0 0-3.889"
                    fill="#546573" />
            </svg>
        </label>
            <input type="file" id="fileInput" name="file" style="display: none;" />

        <div class="input-container">
            <input type="text" id="messageInput" placeholder="Type a message..." maxlength="999">
        </div>

        <button class="send-button" id="sendMessageBtn">
            <svg width="20" height="20" viewBox="0 0 15 15" fill="none">
                <path
                    d="M14.954.71a.5.5 0 0 1-.1.144L5.4 10.306l2.67 4.451a.5.5 0 0 0 .889-.06zM4.694 9.6.243 6.928a.5.5 0 0 1 .06-.889L14.293.045a.5.5 0 0 0-.146.101z"
                    fill="#fff" />
            </svg>
        </button>
    </div>
</div>