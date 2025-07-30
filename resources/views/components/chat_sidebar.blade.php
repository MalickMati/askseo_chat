<div class="sidebar" id="sidebar" data-current-user-id="{{ $currentUser['id'] }}">
    <div class="sidebar-header">
        <div class="user-info">
            <img src="{{ asset($currentUser['img']) }}" alt="User" class="user-avatar {{ $currentUser['status'] }}">
            <div class="user-name">
                <span>{{ $currentUser['username'] }}</span>
                <span class="status {{ $currentUser['status'] }}">{{ $currentUser['status'] }}</span>
            </div>
        </div>
        <div class="sidebar-actions">
            <svg id="sidebarMenuButton" style="cursor:pointer;" width="20" height="20" fill="#54656F"
                viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M156 128a28 28 0 1 1-28-28 28.03 28.03 0 0 1 28 28m-28-52a28 28 0 1 0-28-28 28.03 28.03 0 0 0 28 28m0 104a28 28 0 1 0 28 28 28.03 28.03 0 0 0-28-28" />
            </svg>
            <button class="hamburger" id="hamburgerclose">
                <svg width="20" height="20" fill="#54656F" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M11.293 3.293a1 1 0 1 1 1.414 1.414L9.414 8l3.293 3.293a1 1 0 0 1-1.414 1.414L8 9.414l-3.293 3.293a1 1 0 0 1-1.414-1.414L6.586 8 3.293 4.707a1 1 0 0 1 1.414-1.414L8 6.586z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- <div class="search-bar">
        <div class="search-container">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12m-8 6a8 8 0 1 1 14.32 4.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387A8 8 0 0 1 2 10"
                    fill="#8B8299" />
            </svg>
            <input type="text" placeholder="Search or start new chat">
        </div>
    </div> -->

    <div class="chat-list">
        

    </div>
</div>