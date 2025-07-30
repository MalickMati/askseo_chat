<div class="modal" id="editUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit User</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <div class="form-group">
                    <div class="avatar-upload">
                        <div class="avatar-preview">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User Avatar"
                                id="modalAvatarPreview">
                        </div>
                        <div class="avatar-upload-btn" id="modalUploadTrigger">
                            Change Photo
                            <input type="file" id="modalProfileImage" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="modalFullName">Full Name</label>
                    <input type="text" id="modalFullName" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="modalEmail">Email Address</label>
                    <input type="email" id="modalEmail" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>User Role</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="roleAdmin" name="userRole" value="admin">
                            <label for="roleAdmin">Admin</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="roleModerator" name="userRole" value="moderator">
                            <label for="roleModerator">Moderator</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="rolegeneral_user" name="userRole" value="general_user" checked>
                            <label for="rolegeneral_user">User</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="modalPassword">Change Password (Leave blank to keep current)</label>
                    <input type="password" id="modalPassword" name="password" class="form-control" placeholder="New Password">
                </div>

                <div class="form-group">
                    <label for="modalConfirmPassword">Confirm New Password</label>
                    <input type="password" id="modalConfirmPassword" name="password_confirmation" class="form-control"
                        placeholder="Confirm New Password">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-secondary" id="cancelEdit">Cancel</button>
            <button class="btn-modal btn-modal-primary" id="saveChanges">Save Changes</button>
        </div>
    </div>
</div>