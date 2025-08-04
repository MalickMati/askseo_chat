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
        <!-- <div class="nav-item {{ $activePage === 'adduser' ? 'active' : '' }}" onclick="window.location.href='/add/user'">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 52 52" xml:space="preserve"><path d="M21.9 37c0-2.7.9-5.8 2.3-8.2 1.7-3 3.6-4.2 5.1-6.4 2.5-3.7 3-9 1.4-13-1.6-4.1-5.4-6.5-9.8-6.4s-8 2.8-9.4 6.9c-1.6 4.5-.9 9.9 2.7 13.3 1.5 1.4 2.9 3.6 2.1 5.7-.7 2-3.1 2.9-4.8 3.7-3.9 1.7-8.6 4.1-9.4 8.7C1.3 45.1 3.9 49 8 49h17c.8 0 1.3-1 .8-1.6-2.5-2.9-3.9-6.6-3.9-10.4"/><path d="M37.9 25c-6.6 0-12 5.4-12 12s5.4 12 12 12 12-5.4 12-12-5.4-12-12-12M44 38c0 .6-.5 1-1.1 1H40v3c0 .6-.5 1-1.1 1h-2c-.6 0-.9-.4-.9-1v-3h-3.1c-.6 0-.9-.4-.9-1v-2c0-.6.3-1 .9-1H36v-3c0-.6.3-1 .9-1h2c.6 0 1.1.4 1.1 1v3h2.9c.6 0 1.1.4 1.1 1z"/></svg>
            <span>Add New User</span>
        </div> -->
        <div class="nav-item {{ $activePage === 'attendance' ? 'active' : '' }}" onclick="window.location.href='/admin/userattendance'">
            <svg width="20" height="20" viewBox="0 0 24 24"><path fill="currentcolor" d="M7 11c-1.1 0-2-.9-2-2V8c0-1.1.9-2 2-2s2 .9 2 2v1c0 1.1-.9 2-2 2m-2 6.993L9 18c.55 0 1-.45 1-1v-2c0-1.65-1.35-3-3-3s-3 1.35-3 3v2c0 .552.448.993 1 .993M19 18h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2m0-4h-6a1 1 0 1 1 0-2h6a1 1 0 1 1 0 2"/><path fill="currentcolor" d="M22 2H2C.9 2 0 2.9 0 4v16c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2m0 17.5c0 .28-.22.5-.5.5h-19c-.28 0-.5-.22-.5-.5v-15c0-.28.22-.5.5-.5h19c.28 0 .5.22.5.5z"/></svg>
            <span>Attendance</span>
        </div>
        <div class="nav-item {{ $activePage === 'tasks' ? 'active' : '' }}" onclick="window.location.href='/admin/usertasks'">
            <svg width="20" height="20" viewBox="0 0 1200 1200" fill="currentcolor" xml:space="preserve"><path d="M0 131.213v234.375h1200V131.213zm752.856 58.009h385.62v118.359h-385.62zM0 482.849v234.375h1200V482.85zm487.72 58.008h650.757v118.358H487.72zM0 834.412v234.375h1200V834.412zm894.946 58.008h243.529v118.359H894.946z"/></svg>
            <span>Task Management</span>
        </div>
        <div class="nav-item {{ $activePage === 'assigntasks' ? 'active' : '' }}" onclick="window.location.href='/admin/assigntasks'">
            <svg width="20" height="20" viewBox="0 0 36 36" fill="currentcolor"><path class="clr-i-solid clr-i-solid-path-1" d="M29.29 4.95h-7.2a4.31 4.31 0 0 0-8.17 0H7a1.75 1.75 0 0 0-2 1.69v25.62a1.7 1.7 0 0 0 1.71 1.69h22.58A1.7 1.7 0 0 0 31 32.26V6.64a1.7 1.7 0 0 0-1.71-1.69m-18 3a1 1 0 0 1 1-1h3.44v-.63a2.31 2.31 0 0 1 4.63 0V7h3.44a1 1 0 0 1 1 1v1.8H11.25Zm14.52 9.23-9.12 9.12-5.24-5.24a1.4 1.4 0 0 1 2-2l3.26 3.26 7.14-7.14a1.4 1.4 0 1 1 2 2Z"/><path fill="none" d="M0 0h36v36H0z"/></svg>
            <span>Assign Task</span>
        </div>
    </div>

    <div class="nav-item" style="margin-top: auto;" onclick="window.location.href = '/logout';">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Logout</span>
    </div>
</div>