<?php
/**
 * Horchata Mexican Food - User Profile
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

// Obtener información del usuario actual
$admin_id = $_SESSION['admin_user_id'] ?? 0;
$user = getUserInfo($admin_id);

// Configurar página
$page_title = 'Mi Perfil';

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user me-2"></i>Mi Perfil
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="phone" class="form-label">Teléfono (opcional)</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cambiar Contraseña</h6>
                </div>
                <div class="card-body">
                    <form id="passwordForm">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock me-1"></i>Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Cuenta</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 2rem;">
                            <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <h5 class="mt-3"><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></h5>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username'] ?? ''); ?></p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Rol:</strong>
                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?>">
                            <?php echo ucfirst($user['role'] ?? 'staff'); ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Estado:</strong>
                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Último acceso:</strong>
                        <p class="text-muted mb-0">
                            <?php echo $user['last_login'] ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'Nunca'; ?>
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>Cuenta creada:</strong>
                        <p class="text-muted mb-0">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Seguridad</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Consejos de seguridad:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Usa una contraseña fuerte</li>
                            <li>No compartas tu contraseña</li>
                            <li>Cambia tu contraseña regularmente</li>
                            <li>Cierra sesión desde dispositivos públicos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript específico para perfil -->
<script>
$(document).ready(function() {
    // Configurar formulario de perfil
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        updateProfile();
    });

    // Configurar formulario de contraseña
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        changePassword();
    });
});

function updateProfile() {
    const formData = new FormData($('#profileForm')[0]);
    
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
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr, status, error);
            showNotification('Error de conexión', 'error');
        }
    });
}

function changePassword() {
    const newPassword = $('#new_password').val();
    const confirmPassword = $('#confirm_password').val();

    if (newPassword !== confirmPassword) {
        showNotification('Las contraseñas no coinciden', 'error');
        return;
    }

    if (newPassword.length < 6) {
        showNotification('La contraseña debe tener al menos 6 caracteres', 'error');
        return;
    }

    const formData = new FormData($('#passwordForm')[0]);
    
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
                $('#passwordForm')[0].reset();
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr, status, error);
            showNotification('Error de conexión', 'error');
        }
    });
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
/**
 * Obtener información del usuario
 */
function getUserInfo($user_id) {
    return fetchOne("SELECT * FROM users WHERE id = ?", [$user_id]);
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        // Validar que el usuario solo actualice su propio perfil
        if ($user_id != $_SESSION['admin_user_id']) {
            die(json_encode(['success' => false, 'message' => 'No autorizado']));
        }
        
        $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, updated_at = NOW() WHERE id = ?";
        
        if (executeQuery($sql, [$first_name, $last_name, $username, $email, $user_id])) {
            // Actualizar sesión
            $_SESSION['admin_username'] = $username;
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ]);
            exit;
        }
    }
    
    if ($_POST['action'] === 'change_password') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        
        // Validar que el usuario solo cambie su propia contraseña
        if ($user_id != $_SESSION['admin_user_id']) {
            die(json_encode(['success' => false, 'message' => 'No autorizado']));
        }
        
        // Verificar contraseña actual
        $user = fetchOne("SELECT password FROM users WHERE id = ?", [$user_id]);
        if (!password_verify($current_password, $user['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Contraseña actual incorrecta'
            ]);
            exit;
        }
        
        // Actualizar contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
        
        if (executeQuery($sql, [$hashed_password, $user_id])) {
            echo json_encode([
                'success' => true,
                'message' => 'Contraseña cambiada exitosamente'
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al cambiar la contraseña'
            ]);
            exit;
        }
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>

