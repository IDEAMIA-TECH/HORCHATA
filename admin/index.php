<?php
/**
 * Horchata Mexican Food - Panel Administrativo
 * Sistema de Login
 */

session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

$error_message = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Por favor, completa todos los campos';
    } else {
        // Buscar usuario en la base de datos (por username o email)
        $user = fetchOne("
            SELECT id, username, email, password, role, first_name, last_name, is_active
            FROM users 
            WHERE (username = ? OR email = ?) AND is_active = 1
        ", [$username, $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login exitoso
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Actualizar último login
            executeQuery("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Usuario o contraseña incorrectos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Administrativo | Horchata Mexican Food</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #8b4513;
            --accent-color: #ff6b35;
            --dark-color: #2c2c2c;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), #f4d03f);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), #f4d03f);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            background: transparent;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
        }
        
        .back-to-site {
            position: absolute;
            top: 20px;
            left: 20px;
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .back-to-site:hover {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .login-container {
                margin: 1rem;
            }
            
            .login-header, .login-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Back to Site Link -->
                <a href="../index.php" class="back-to-site">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Sitio
                </a>
                
                <!-- Login Container -->
                <div class="login-container">
                    <!-- Header -->
                    <div class="login-header">
                        <img src="../assets/images/LOGO.JPG" alt="Horchata Mexican Food" 
                             style="max-width: 200px; margin-bottom: 1rem; 
                                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 
                                                0 5px 15px rgba(212, 175, 55, 0.3); 
                                    border-radius: 10px;
                                    background: rgba(255, 255, 255, 0.1);
                                    padding: 5px;">
                        <
                    </div>
                    
                    <!-- Login Form -->
                    <div class="login-form">
                        <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="username" class="form-label fw-bold">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Ingresa tu usuario"
                                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Ingresa tu contraseña"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                        
                        <!-- Additional Info -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Acceso restringido al personal autorizado
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-focus en el campo de usuario
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Mostrar/ocultar contraseña
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const inputGroup = passwordInput.closest('.input-group');
            
            // Agregar botón de mostrar contraseña
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'btn btn-outline-secondary';
            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            toggleButton.style.borderRadius = '0 10px 10px 0';
            
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
            
            inputGroup.appendChild(toggleButton);
        });
    </script>
</body>
</html>
