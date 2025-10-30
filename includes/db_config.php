<?php
/**
 * Configuración de Base de Datos - Horchata Mexican Food
 * Sistema de Pedidos Web
 */

// Configuración de la base de datos
define('DB_HOST', '173.231.22.109');
define('DB_NAME', 'ideamiadev_horchata');
define('DB_USER', 'ideamiadev_horchata');
define('DB_PASS', 'DfabGqB&gX3xM?ea');
define('DB_CHARSET', 'utf8mb4');

// Configuración de conexión PDO
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// Opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Contacte al administrador.");
}

// Configuración adicional
define('SITE_URL', 'https://horchatamexfood.com');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', 'assets/images/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (solo para desarrollo)
if (defined('DEVELOPMENT') && DEVELOPMENT) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
