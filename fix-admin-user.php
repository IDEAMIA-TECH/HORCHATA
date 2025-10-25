<?php
/**
 * Fix Admin User - Corregir usuario administrador
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Corregir Usuario Administrador</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Corregir Usuario Administrador</h1>
        <hr>
";

try {
    $pdo = getDbConnection();
    
    // Verificar si existe el usuario administrador
    echo "<h2>1. Verificar Usuario Administrador</h2>";
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute(['admin@horchatamexicanfood.com', 'admin@horchatamexicanfood.com']);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "<div class='alert alert-info'>ℹ️ Usuario encontrado:</div>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $existingUser['id'] . "</li>";
        echo "<li><strong>Email:</strong> " . $existingUser['email'] . "</li>";
        echo "<li><strong>Username:</strong> " . ($existingUser['username'] ?? 'No definido') . "</li>";
        echo "<li><strong>Rol:</strong> " . $existingUser['role'] . "</li>";
        echo "<li><strong>Activo:</strong> " . ($existingUser['is_active'] ? 'Sí' : 'No') . "</li>";
        echo "</ul>";
        
        // Verificar si la contraseña es correcta
        if (password_verify('password', $existingUser['password'])) {
            echo "<div class='alert alert-success'>✅ Contraseña correcta</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ Contraseña incorrecta, actualizando...</div>";
            
            // Actualizar contraseña
            $newPassword = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newPassword, $existingUser['id']]);
            echo "<div class='alert alert-success'>✅ Contraseña actualizada</div>";
        }
        
        // Asegurar que tenga username
        if (empty($existingUser['username'])) {
            echo "<div class='alert alert-warning'>⚠️ Username vacío, actualizando...</div>";
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute(['admin@horchatamexicanfood.com', $existingUser['id']]);
            echo "<div class='alert alert-success'>✅ Username actualizado</div>";
        }
        
        // Asegurar que esté activo
        if (!$existingUser['is_active']) {
            echo "<div class='alert alert-warning'>⚠️ Usuario inactivo, activando...</div>";
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$existingUser['id']]);
            echo "<div class='alert alert-success'>✅ Usuario activado</div>";
        }
        
        // Asegurar que tenga rol de admin
        if ($existingUser['role'] !== 'admin') {
            echo "<div class='alert alert-warning'>⚠️ Rol incorrecto, actualizando...</div>";
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$existingUser['id']]);
            echo "<div class='alert alert-success'>✅ Rol actualizado a admin</div>";
        }
        
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
            echo "<div class='alert alert-success'>✅ Usuario administrador creado (ID: $userId)</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Error al crear usuario</div>";
        }
    }
    
    // Verificar usuarios existentes
    echo "<h2>2. Todos los Usuarios en la Base de Datos</h2>";
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
    } else {
        echo "<div class='alert alert-warning'>⚠️ No hay usuarios en la base de datos</div>";
    }
    
    // Probar login
    echo "<h2>3. Probar Login</h2>";
    echo "<div class='card'>";
    echo "<div class='card-body'>";
    echo "<h5>Credenciales de Prueba:</h5>";
    echo "<ul>";
    echo "<li><strong>Usuario:</strong> admin@horchatamexicanfood.com</li>";
    echo "<li><strong>Contraseña:</strong> password</li>";
    echo "</ul>";
    echo "<p><a href='admin/index.php' class='btn btn-primary'>Probar Login</a></p>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
