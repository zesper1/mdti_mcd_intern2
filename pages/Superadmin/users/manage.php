<?php
require_once '../../../src/config.php';
session_start();

// Session Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'Superadmin') {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-users-cog me-2"></i>User Management</h3>
        <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary align-self-center">Registered Users</h6>
                <button class="btn btn-primary btn-sm" onclick="openUserModal()">
                    <i class="fas fa-plus"></i> Add New User
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="5" class="text-center py-4">Loading users...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="text" id="userId" hidden>
                    
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" id="firstName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" id="lastName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" id="password" class="form-control" placeholder="••••••••">
                        
                        <div class="card bg-light border-0 mt-2">
                            <div class="card-body py-2 px-3">
                                <small class="text-muted" style="font-size: 0.8rem;">
                                    <strong>Requirements:</strong>
                                    <ul class="mb-0 ps-3">
                                        <li>8-16 characters long</li>
                                        <li>At least 1 Uppercase & 1 Lowercase</li>
                                        <li>At least 1 Number & 1 Special Character</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                        <div id="passwordHelp" class="form-text mt-1 text-primary" style="display:none;">
                            <i class="fas fa-info-circle"></i> Leave blank to keep current password.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select id="role" class="form-select">
                            <option value="Admin">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Member">Member</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <div id="msgIcon" class="mb-3"></div>
                <h5 id="msgTitle" class="mb-2"></h5>
                <p id="msgBody" class="text-muted small mb-0"></p>
            </div>
            <div class="modal-footer justify-content-center p-2">
                <button type="button" class="btn btn-sm btn-light w-50" data-bs-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', loadUsers);

    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

    // --- 1. FETCH USERS ---
    function loadUsers() {
        fetch('../../../src/Controller/UserController.php?action=fetch')
            .then(response => response.json())
            .then(json => {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = ''; 

                if (json.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                    return;
                }

                json.data.forEach(user => {
                    const row = `
                        <tr>
                            <td class="align-middle fw-bold">${user.first_name} ${user.last_name}</td>
                            <td class="align-middle">${user.email}</td>
                            <td class="align-middle"><span class="badge bg-secondary">${user.role_name || 'No Role'}</span></td>
                            <td class="align-middle">
                                <span class="badge bg-${user.is_active ? 'success' : 'danger'} dot-badge">
                                    ${user.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                    onclick="openEdit('${user.user_id}', '${user.first_name}', '${user.last_name}', '${user.email}', '${user.role_name}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteUser('${user.user_id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(err => console.error('Error:', err));
    }

    // --- 2. OPEN MODALS ---
    
    // For ADDING a new user
    function openUserModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Add New User';
        
        // Password is REQUIRED for new users
        const pwInput = document.getElementById('password');
        pwInput.required = true; 
        pwInput.placeholder = "Enter initial password";
        document.getElementById('passwordHelp').style.display = 'none';

        userModal.show();
    }

    // For EDITING an existing user
    function openEdit(id, first, last, email, role) {
        document.getElementById('userId').value = id;
        document.getElementById('firstName').value = first;
        document.getElementById('lastName').value = last;
        document.getElementById('email').value = email;
        document.getElementById('role').value = role || 'Member';
        
        // Password is OPTIONAL for existing users
        const pwInput = document.getElementById('password');
        pwInput.value = ''; // Clear it so they don't see the hash
        pwInput.required = false;
        pwInput.placeholder = "••••••••";
        document.getElementById('passwordHelp').style.display = 'block';

        document.getElementById('modalTitle').innerText = 'Edit User';
        userModal.show();
    }

    // --- 3. SAVE ACTION (AJAX) ---
    function saveUser() {
        const data = {
            id: document.getElementById('userId').value,
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value,
            password: document.getElementById('password').value // Send password
        };

        fetch('../../../src/Controller/UserController.php?action=save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                userModal.hide();
                loadUsers();
            }
            // Show the backend message (success or error)
            showMessage(res.success, res.message);
        });
    }

    // --- 4. DELETE ACTION (AJAX) ---
    function deleteUser(id) {
        if (!confirm('Are you sure you want to remove this user? This cannot be undone.')) return;

        fetch('../../../src/Controller/UserController.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(res => {
            showMessage(res.success, res.message);
            if (res.success) loadUsers();
        });
    }

    // --- 5. HELPER: MESSAGE MODAL ---
    function showMessage(success, message) {
        const icon = success 
            ? '<i class="fas fa-check-circle text-success fa-3x"></i>' 
            : '<i class="fas fa-times-circle text-danger fa-3x"></i>';
        
        document.getElementById('msgIcon').innerHTML = icon;
        document.getElementById('msgTitle').innerText = success ? 'Success!' : 'Error';
        document.getElementById('msgBody').innerText = message;
        messageModal.show();
    }
</script>

</body>
</html>