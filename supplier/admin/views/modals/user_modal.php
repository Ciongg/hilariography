<?php
require_once __DIR__ . '/../../utils/helpers.php';
?>
<!-- ADD USER MODAL -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <h3>Add User</h3>
        <form action="users/add_user.php" method="post" id="addUserForm">
            <div class="form-group">
                <label>Username <span style="color: red;">*</span></label>
                <input type="text" name="userName" id="addUserName" required>
            </div>
            <div class="form-group">
                <label>Password <span style="color: red;">*</span></label>
                <input type="password" name="userPassword" id="addUserPassword" required>
            </div>
            <div class="form-group">
                <label>Confirm Password <span style="color: red;">*</span></label>
                <input type="password" name="confirmPassword" id="addConfirmPassword" required>
                <div id="passwordError" style="color: red; font-size: 12px; margin-top: 5px; display: none;">Passwords do not match</div>
            </div>
            <div class="form-group">
                <label>Role <span style="color: red;">*</span></label>
                <select name="userRole" required>
                    <option value="">Select Role</option>
                    <option>Admin</option>
                    <option>Employee</option>
                </select>
            </div>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="closeModal('userModal')">Cancel</button>
                <button type="submit" class="save-btn" id="addUserSubmitBtn">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <h3 id="editUserModalTitle">Edit User</h3>
        <form id="editUserForm" method="post" action="users/edit_user.php">
            <input type="hidden" name="userID" id="editUserID">
            <div class="form-group">
                <label for="editUserName">Name</label>
                <input type="text" id="editUserName" name="userName" required>
            </div>
            <div class="form-group">
                <label for="editUserRole">Role</label>
                <select id="editUserRole" name="userRole" required>
                    <option value="Admin">Admin</option>
                    <option value="Employee">Employee</option>
                </select>
            </div>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="save-btn">Save</button>
            </div>
        </form>
    </div>
</div> 