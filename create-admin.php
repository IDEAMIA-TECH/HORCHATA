<?php
/**
 * Create Admin - Crear usuario administrador
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Crear Usuario Administrador</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Crear Usuario Administrador</h1>
        <hr>
";

try {
    $pdo = getDbConnection();
    
    // Verificar si ya existe el usuario
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute(['admin@horchatamexicanfood.com', 'admin@horchatamexicanfood.com']);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "<div class='alert alert-info'>ℹ️ Usuario administrador ya existe</div>";
        echo "<div class='card'>";
        echo "<div class='card-header'><h5>Información del Usuario</h5></div>";
        echo "<div class='card-body'>";
        echo "<p><strong>ID:</strong> " . $existingUser['id'] . "</p>";
        echo "<p><strong>Username:</strong> " . ($existingUser['username'] ?? 'No definido') . "</p>";
        echo "<p><strong>Email:</strong> " . ($existingUser['email'] ?? 'No definido') . "</p>";
        echo "<p><strong>Rol:</strong> " . $existingUser['role'] . "</p>";
        echo "<p><strong>Activo:</strong> " . ($existingUser['is_active'] ? 'Sí' : 'No') . "</p>";
        echo "</div>";
        echo "</div>";
        
        // Actualizar contraseña y asegurar que esté activo
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE users SET 
                password = ?, 
                is_active = 1, 
                role = 'admin',
                username = COALESCE(NULLIF(username, ''), ?)
            WHERE id = ?
        ");
        $stmt->execute([$password, 'admin@horchatamexicanfood.com', $existingUser['id']]);
        
        echo "<div class='alert alert-success'>✅ Usuario actualizado correctamente</div>";
        
    } else {
        echo "<div class='alert alert-warning'>⚠️ Usuario no encontrado, creando...</div>";
        
        // Crear usuario administrador
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (
                username, email, password, role, 
                first_name, last_name, is_active, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            'admin@horchatamexicanfood.com',
            'admin@horchatamexicanfood.com',
            $password,
            'admin',
            'Administrador',
            'Sistema',
            1
        ]);
        
        if ($result) {
            $userId = $pdo->lastInsertId();
            echo "<div class='alert alert-success'>✅ Usuario administrador creado exitosamente (ID: $userId)</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Error al crear usuario administrador</div>";
        }
    }
    
    // Mostrar credenciales
    echo "<div class='card mt-4'>";
    echo "<div class='card-header'><h5>Credenciales de Acceso</h5></div>";
    echo "<div class='card-body'>";
    echo "<div class='alert alert-info'>";
    echo "<h6>Usuario Administrador:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li><strong>Usuario/Email:</strong> admin@horchatamexicanfood.com</li>";
    echo "<li><strong>Contraseña:</strong> password</li>";
    echo "</ul>";
    echo "</div>";
    echo "<p><a href='admin/index.php' class='btn btn-primary'>Acceder al Panel Administrativo</a></p>";
    echo "</div>";
    echo "</div>";
    
    // Verificar todos los usuarios
    echo "<h3 class='mt-4'>Todos los Usuarios</h3>";
    $stmt = $pdo->query("SELECT id, username, email, role, is_active, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Rol</th><th>Activo</th><th>Creado</th></tr></thead>";
        echo "<tbody>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . ($user['username'] ?? 'N/A') . "</td>";
            echo "<td>" . ($user['email'] ?? 'N/A') . "</td>";
            echo "<td><span class='badge bg-" . ($user['role'] === 'admin' ? 'danger' : 'primary') . "'>" . $user['role'] . "</span></td>";
            echo "<td>" . ($user['is_active'] ? '✅' : '❌') . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
