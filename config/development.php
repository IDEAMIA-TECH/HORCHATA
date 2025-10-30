<?php
/**
 * Configuración de Desarrollo
 * Horchata Mexican Food - Sistema de Pedidos
 */

// Configuración de entorno
define('ENVIRONMENT', 'development');
define('DEVELOPMENT', true);
define('DEBUG', true);

// Configuración de la base de datos
define('DB_HOST', '173.231.22.109');
define('DB_NAME', 'horchatamexfood_horchata');
define('DB_USER', 'horchatamexfood_horchata');
define('DB_PASS', 'DfabGqB&gX3xM?ea');
define('DB_CHARSET', 'utf8mb4');

// URLs del sitio
define('SITE_URL', 'http://localhost/horchata');
define('ADMIN_URL', SITE_URL . '/admin');
define('API_URL', SITE_URL . '/api');

// Configuración de archivos
define('UPLOAD_PATH', 'assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/' . UPLOAD_PATH);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuración de correo
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_FROM', 'orders@horchatamexicanfood.com');
define('MAIL_FROM_NAME', 'Horchata Mexican Food');

// Configuración de PayPal (modo desarrollo)
define('PAYPAL_CLIENT_ID', 'YOUR_PAYPAL_CLIENT_ID');
define('PAYPAL_CLIENT_SECRET', 'YOUR_PAYPAL_CLIENT_SECRET');
define('PAYPAL_MODE', 'sandbox'); // sandbox o live
define('PAYPAL_CURRENCY', 'USD');

// Configuración de sesiones
define('SESSION_LIFETIME', 7200); // 2 horas
define('SESSION_NAME', 'HORCHATA_SESSION');

// Configuración de seguridad
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Configuración de notificaciones
define('NOTIFICATION_POLL_INTERVAL', 10); // segundos
define('AUTO_PRINT_ENABLED', false);

// Configuración de idiomas
define('DEFAULT_LANGUAGE', 'es');
define('SUPPORTED_LANGUAGES', ['en', 'es']);

// Configuración de zona horaria
define('TIMEZONE', 'America/Mexico_City');

// Configuración de errores
if (DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de zona horaria
date_default_timezone_set(TIMEZONE);

// Configuración de sesiones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Configuración de CORS para desarrollo
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Manejo de preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
