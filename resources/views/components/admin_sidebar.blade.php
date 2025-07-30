<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="{{ asset('favicon.ico') }}" alt="" style="height:25px;">
            <span>ASKSEO</span>
        </div>
    </div>

    <div class="admin-info">
        <img src="{{ asset($img) }}" alt="Admin" class="admin-avatar">
        <div class="admin-details">
            <div class="admin-name">{{ $name }}</div>
            <div class="admin-role">Super Admin</div>
        </div>
    </div>

    <div class="nav-menu">
        <div class="nav-item {{ $activePage === 'user_management' ? 'active' : '' }}" onclick="window.location.href='/admin'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8m14 10v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>User Management</span>
        </div>
        <div class="nav-item" onclick="window.location.href='/chat';">
            <svg width="20" height="20" viewBox="0 0 24 24" data-name="Flat Color" xmlns="http://www.w3.org/2000/svg"><path d="M19.91 16.51A8.45 8.45 0 0 0 22 11c0-5-4.49-9-10-9S2 6 2 11s4.49 9 10 9a11 11 0 0 0 3-.41l4.59 2.3A.9.9 0 0 0 20 22a1 1 0 0 0 .62-.22 1 1 0 0 0 .35-1Z" stroke="currentColor" fill="currentColor"/></svg>
            <span>Chat</span>
        </div>
        <div class="nav-item {{ $activePage === 'adduser' ? 'active' : '' }}" onclick="window.location.href='/add/user'">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 52 52" xml:space="preserve"><path d="M21.9 37c0-2.7.9-5.8 2.3-8.2 1.7-3 3.6-4.2 5.1-6.4 2.5-3.7 3-9 1.4-13-1.6-4.1-5.4-6.5-9.8-6.4s-8 2.8-9.4 6.9c-1.6 4.5-.9 9.9 2.7 13.3 1.5 1.4 2.9 3.6 2.1 5.7-.7 2-3.1 2.9-4.8 3.7-3.9 1.7-8.6 4.1-9.4 8.7C1.3 45.1 3.9 49 8 49h17c.8 0 1.3-1 .8-1.6-2.5-2.9-3.9-6.6-3.9-10.4"/><path d="M37.9 25c-6.6 0-12 5.4-12 12s5.4 12 12 12 12-5.4 12-12-5.4-12-12-12M44 38c0 .6-.5 1-1.1 1H40v3c0 .6-.5 1-1.1 1h-2c-.6 0-.9-.4-.9-1v-3h-3.1c-.6 0-.9-.4-.9-1v-2c0-.6.3-1 .9-1H36v-3c0-.6.3-1 .9-1h2c.6 0 1.1.4 1.1 1v3h2.9c.6 0 1.1.4 1.1 1z"/></svg>
            <span>Add New User</span>
        </div>
    </div>

    <div class="nav-item" style="margin-top: auto;" onclick="window.location.href = '/logout';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Logout</span>
    </div>
</div>