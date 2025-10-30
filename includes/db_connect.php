<?php
/**
 * Conexión Simple a Base de Datos
 * Horchata Mexican Food - Sistema de Pedidos
 */

// Configuración de la base de datos
// PRODUCCIÓN: Actualizar con las credenciales correctas de producción
$host = '173.231.22.109'; // Actualizar si el host de producción es diferente
$dbname = 'horchatamexfood_horchata'; // Base de datos de producción
$username = 'horchatamexfood_horchata'; // Usuario de producción
$password = 'DfabGqB&gX3xM?ea'; // Actualizar con la contraseña de producción

try {
    // Conexión usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Conexión exitosa
    // echo "Conexión a la base de datos establecida correctamente.";
    
} catch(PDOException $e) {
    // Error de conexión
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Contacte al administrador.");
}

// Función auxiliar para ejecutar consultas
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Error en consulta SQL: " . $e->getMessage());
        return false;
    }
}

// Función para obtener un solo registro
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

// Función para obtener múltiples registros
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}

// Función para insertar y obtener el ID
function insertAndGetId($sql, $params = []) {
    global $pdo;
    $stmt = executeQuery($sql, $params);
    return $stmt ? $pdo->lastInsertId() : false;
}
?>
