<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../utils/helpers.php';

$userModel = new User();
$users = $userModel->getAll();
?>
<div id="usersTable" class="table-container">
    <div class="table-header">
        <h3>User Management</h3>
        <div class="filters-container">
            <div class="search-container">
                <div class="search-input-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" class="search-input" placeholder="Search users..." id="userSearchInput">
                </div>
            </div>
            <div class="role-filter-container">
                <select class="role-filter" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>
                <button class="filter-btn" onclick="applyUserFilters()">Filter</button>
                <button class="clear-filter-btn" onclick="clearUserFilters()">Clear</button>
            </div>
            <button class="modern-add-btn" onclick="openModal('userModal')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
                Add User
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th class="actions-column">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><span class='table-id'><?php echo $user['userID']; ?></span></td>
                    <td><span class='table-username'><?php echo htmlspecialchars($user['userName']); ?></span></td>
                    <td><span class='table-role role-<?php echo strtolower($user['userRole']); ?>'><?php echo htmlspecialchars($user['userRole']); ?></span></td>
                    <td><span class='table-created'><?php echo $user['created_at']; ?></span></td>
                    <td><span class='table-created'><?php echo $user['updated_at']; ?></span></td>
                    <td class='actions-cell'>
                        <div class='action-buttons'>
                            <button class='btn btn-edit' onclick='openEditUserModal(<?php echo $user['userID']; ?>, <?php echo json_encode($user['userName']); ?>, <?php echo json_encode($user['userRole']); ?>)'>
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125' />
                                </svg>
                                Edit
                            </button>
                            <?php if ($user['canDelete']): ?>
                            <a href='users/delete_user.php?id=<?php echo $user['userID']; ?>' class='btn btn-delete' onclick="return confirm('Delete this employee?')">
                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' width='14' height='14'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' />
                                </svg>
                                Delete
                            </a>
                            <?php else: ?>
                            <span class='btn-placeholder'></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 