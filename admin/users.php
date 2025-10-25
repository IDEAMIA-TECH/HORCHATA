<?php
/**
 * Horchata Mexican Food - Users Management
 * Admin Panel
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

// Obtener parámetros
$action = $_GET['action'] ?? 'list';
$user_id = $_GET['id'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'create':
            createUser();
            break;
        case 'update':
            updateUser();
            break;
        case 'delete':
            deleteUser();
            break;
        case 'toggle_status':
            toggleUserStatus();
            break;
        case 'change_password':
            changePassword();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'create':
    case 'edit':
        $user = $action === 'edit' ? getUser($user_id) : null;
        break;
    case 'list':
    default:
        $users = getAllUsers();
        $role_counts = getUserRoleCounts();
        break;
}

// Configurar página
$page_title = $action === 'edit' ? 'Edit User' : ($action === 'create' ? 'Create User' : 'Users Management');
$page_scripts = ['assets/js/users.js'];

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-users me-2"></i>Users Management
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshUsers()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="btn-group">
                <a href="users.php?action=create" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add User
                </a>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Users List -->
    <div class="row mb-4">
        <!-- Role Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Administrators
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $role_counts['admin'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Staff
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $role_counts['staff'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $role_counts['active'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $role_counts['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All roles</option>
                        <option value="admin">Administrator</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-bordered data-table" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" 
                                            onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 'false' : 'true'; ?>)" 
                                            title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fas fa-<?php echo $user['is_active'] ? 'pause' : 'play'; ?>"></i>
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- User Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php echo $action === 'create' ? 'Create New User' : 'Edit User'; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form id="userForm" method="POST">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo $user['first_name'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo $user['last_name'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo $user['username'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo $user['email'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role *</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Select role</option>
                                        <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                        <option value="staff" <?php echo ($user['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Password <?php echo $action === 'create' ? '*' : '(leave blank to keep current)'; ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?php echo $action === 'create' ? 'required' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?php echo ($user['is_active'] ?? true) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active User
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo $action === 'create' ? 'Create User' : 'Update User'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Manage user accounts and permissions for the admin panel.
                    </p>
                    
                    <h6>Roles:</h6>
                    <ul class="small">
                        <li><strong>Administrator:</strong> Full access to all features</li>
                        <li><strong>Staff:</strong> Limited access to specific features</li>
                    </ul>
                    
                    <h6>Security Tips:</h6>
                    <ul class="small">
                        <li>Use strong passwords</li>
                        <li>Regularly update user permissions</li>
                        <li>Deactivate unused accounts</li>
                        <li>Monitor user activity</li>
                    </ul>
                    
                    <?php if ($action === 'edit'): ?>
                    <hr>
                    <h6>User Stats:</h6>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Last Login:</strong> <?php echo $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para usuarios -->
<script>
$(document).ready(function() {
    // Configurar DataTable
    if ($.fn.DataTable) {
        $('#usersTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "Show _MENU_ users per page",
                "zeroRecords": "No users found",
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "infoEmpty": "No users available",
                "infoFiltered": "(filtered from _MAX_ total users)",
                "search": "Search:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    }
    
    // Configurar formulario
    setupUserForm();
});

function setupUserForm() {
    // Auto-save del formulario
    $('#userForm input, #userForm select').on('change', function() {
        autoSaveUser();
    });
    
    // Manejar envío del formulario
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
}

function refreshUsers() {
    location.reload();
}

function autoSaveUser() {
    const formData = new FormData($('#userForm')[0]);
    formData.append('action', 'auto_save_user');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSaveIndicator();
            }
        },
        error: function() {
            // No mostrar error en auto-save
        }
    });
}

function saveUser() {
    const formData = new FormData($('#userForm')[0]);
    
    // Mostrar loading
    const submitBtn = $('#userForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                setTimeout(function() {
                    window.location.href = 'users.php';
                }, 1500);
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Connection error', 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

function toggleUserStatus(userId, newStatus) {
    const action = newStatus === 'true' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'toggle_user_status',
                user_id: userId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Connection error', 'error');
            }
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_user',
                user_id: userId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Connection error', 'error');
            }
        });
    }
}

function showSaveIndicator() {
    let indicator = $('#saveIndicator');
    if (indicator.length === 0) {
        indicator = $('<div id="saveIndicator" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>');
        $('body').append(indicator);
    }
    
    indicator.html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check me-1"></i>Auto-saved
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    setTimeout(function() {
        indicator.find('.alert').alert('close');
    }, 3000);
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                     type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}
</script>

<?php
// Funciones auxiliares
function getAllUsers() {
    global $pdo;
    
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getUser($id) {
    global $pdo;
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserRoleCounts() {
    global $pdo;
    
    $sql = "SELECT 
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin,
                SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                COUNT(*) as total
            FROM users";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}

function createUser() {
    global $pdo;
    
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        throw new Exception('All required fields must be filled');
    }
    
    // Check if username or email already exists
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ? OR email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email]);
    $exists = $stmt->fetch()['count'];
    
    if ($exists > 0) {
        throw new Exception('Username or email already exists');
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (first_name, last_name, username, email, password, role, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$first_name, $last_name, $username, $email, $hashed_password, $role, $is_active])) {
        echo json_encode([
            'success' => true,
            'message' => 'User created successfully'
        ]);
    } else {
        throw new Exception('Error creating user');
    }
}

function updateUser() {
    global $pdo;
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($user_id <= 0) {
        throw new Exception('Invalid user ID');
    }
    
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($role)) {
        throw new Exception('All required fields must be filled');
    }
    
    // Check if username or email already exists (excluding current user)
    $sql = "SELECT COUNT(*) as count FROM users WHERE (username = ? OR email = ?) AND id != ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $user_id]);
    $exists = $stmt->fetch()['count'];
    
    if ($exists > 0) {
        throw new Exception('Username or email already exists');
    }
    
    $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ?, is_active = ?, updated_at = NOW()";
    $params = [$first_name, $last_name, $username, $email, $role, $is_active];
    
    if (!empty($password)) {
        $sql .= ", password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        throw new Exception('Error updating user');
    }
}

function deleteUser() {
    global $pdo;
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    
    if ($user_id <= 0) {
        throw new Exception('Invalid user ID');
    }
    
    // Prevent deleting own account
    if ($user_id == $_SESSION['admin_id']) {
        throw new Exception('Cannot delete your own account');
    }
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } else {
        throw new Exception('Error deleting user');
    }
}

function toggleUserStatus() {
    global $pdo;
    
    $user_id = (int)($_POST['user_id'] ?? 0);
    $status = $_POST['status'] === 'true' ? 1 : 0;
    
    if ($user_id <= 0) {
        throw new Exception('Invalid user ID');
    }
    
    // Prevent deactivating own account
    if ($user_id == $_SESSION['admin_id'] && $status == 0) {
        throw new Exception('Cannot deactivate your own account');
    }
    
    $sql = "UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$status, $user_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'User status updated successfully'
        ]);
    } else {
        throw new Exception('Error updating user status');
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
