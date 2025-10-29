<?php
// Versión mínima de extras.php para probar
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 EXTRAS.PHP - VERSIÓN MÍNIMA\n";
echo "==============================\n\n";

try {
    // Incluir archivos necesarios
    require_once '../includes/init.php';
    require_once '../includes/db_connect.php';
    
    echo "✅ Archivos incluidos correctamente\n";
    
    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        echo "❌ No hay sesión de usuario\n";
        echo "Redirigiendo a index.php...\n";
        header('Location: index.php');
        exit;
    }
    
    echo "✅ Sesión de usuario verificada\n";
    
    // Verificar conexión a BD
    if (!isset($pdo)) {
        throw new Exception("No hay conexión a la base de datos");
    }
    
    echo "✅ Conexión a BD verificada\n";
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) FROM product_extras");
    $count = $stmt->fetchColumn();
    echo "✅ Extras en BD: " . $count . "\n";
    
    // Mostrar página básica
    echo "\n📋 PÁGINA DE EXTRAS CARGADA EXITOSAMENTE\n";
    echo "========================================\n";
    echo "Total de extras: " . $count . "\n";
    echo "Usuario: " . $_SESSION['user_id'] . "\n";
    echo "Rol: " . ($_SESSION['user_role'] ?? 'no definido') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
}
?>
