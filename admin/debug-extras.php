<?php
// Versión simplificada de extras.php para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 DEBUGGING EXTRAS.PHP\n";
echo "=======================\n\n";

try {
    echo "📋 Paso 1: Incluyendo init.php...\n";
    require_once '../includes/init.php';
    echo "✅ init.php incluido\n";
    
    echo "📋 Paso 2: Incluyendo db_connect.php...\n";
    require_once '../includes/db_connect.php';
    echo "✅ db_connect.php incluido\n";
    
    echo "📋 Paso 3: Verificando sesión...\n";
    if (!isset($_SESSION['user_id'])) {
        echo "❌ No hay sesión de usuario\n";
        echo "Redirigiendo a index.php...\n";
        // header('Location: index.php');
        // exit;
    } else {
        echo "✅ Sesión de usuario encontrada\n";
    }
    
    echo "📋 Paso 4: Verificando traducciones...\n";
    $page_title = __('admin_panel') . ' - ' . __('extras_management');
    echo "Título de página: " . $page_title . "\n";
    
    echo "📋 Paso 5: Verificando conexión a BD...\n";
    if (isset($pdo)) {
        echo "✅ Conexión a BD disponible\n";
        
        // Probar consulta simple
        $stmt = $pdo->query("SELECT COUNT(*) FROM product_extras");
        $count = $stmt->fetchColumn();
        echo "Extras en BD: " . $count . "\n";
    } else {
        echo "❌ No hay conexión a BD\n";
    }
    
    echo "\n✅ Todos los pasos completados exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "❌ Error fatal: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n🎯 Debugging completado\n";
?>
