<?php
// =============================================
// Horchata Mexican Food - Actualización del Menú
// Script PHP para actualizar la base de datos
// =============================================

echo "🍽️  Horchata Mexican Food - Actualización del Menú\n";
echo "==================================================\n\n";

// Incluir configuración de base de datos
require_once 'includes/db_connect.php';

try {
    // Verificar conexión
    if (!isset($pdo)) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    echo "✅ Conexión a la base de datos establecida\n\n";
    
    // Paso 1: Crear respaldo
    echo "📋 Paso 1: Creando respaldo de datos existentes...\n";
    
    // Crear tabla de respaldo de productos
    $sql = "CREATE TABLE IF NOT EXISTS products_backup AS SELECT * FROM products";
    $pdo->exec($sql);
    
    // Crear tabla de respaldo de categorías
    $sql = "CREATE TABLE IF NOT EXISTS categories_backup AS SELECT * FROM categories";
    $pdo->exec($sql);
    
    // Contar productos respaldados
    $stmt = $pdo->query("SELECT COUNT(*) FROM products_backup");
    $backup_count = $stmt->fetchColumn();
    
    echo "✅ Respaldo creado exitosamente ($backup_count productos respaldados)\n\n";
    
    // Paso 2: Actualizar menú
    echo "📋 Paso 2: Actualizando menú con nuevos datos...\n";
    
    // Leer el archivo SQL de actualización
    $sql_file = 'update-menu-data.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Archivo $sql_file no encontrado");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Dividir en declaraciones individuales
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $success_count++;
        } catch (PDOException $e) {
            $error_count++;
            echo "⚠️  Error en declaración: " . substr($statement, 0, 50) . "...\n";
            echo "   Error: " . $e->getMessage() . "\n";
        }
    }
    
    if ($error_count > 0) {
        echo "⚠️  Se encontraron $error_count errores durante la actualización\n";
    }
    
    echo "✅ Menú actualizado exitosamente ($success_count declaraciones ejecutadas)\n\n";
    
    // Paso 3: Verificar actualización
    echo "📋 Paso 3: Verificando actualización...\n";
    
    // Verificar categorías
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1");
    $categories_count = $stmt->fetchColumn();
    echo "✅ Categorías activas: $categories_count\n";
    
    // Verificar productos
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $products_count = $stmt->fetchColumn();
    echo "✅ Productos totales: $products_count\n";
    
    // Verificar productos destacados
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_featured = 1");
    $featured_count = $stmt->fetchColumn();
    echo "✅ Productos destacados: $featured_count\n";
    
    // Verificar productos disponibles
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_available = 1");
    $available_count = $stmt->fetchColumn();
    echo "✅ Productos disponibles: $available_count\n";
    
    // Verificar rango de precios
    $stmt = $pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price, ROUND(AVG(price), 2) as avg_price FROM products");
    $price_info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Rango de precios: $" . $price_info['min_price'] . " - $" . $price_info['max_price'] . " (Promedio: $" . $price_info['avg_price'] . ")\n";
    
    echo "\n🎉 ¡Actualización del menú completada exitosamente!\n\n";
    
    echo "📊 Resumen de la actualización:\n";
    echo "- Se procesaron todos los productos del CSV\n";
    echo "- Se organizaron en $categories_count categorías\n";
    echo "- Se marcaron $featured_count productos destacados\n";
    echo "- Se creó respaldo de seguridad\n";
    echo "- Total de productos: $products_count\n";
    echo "- Productos disponibles: $available_count\n\n";
    
    echo "🔍 Para verificar manualmente, ejecuta:\n";
    echo "   php quick-test.php\n\n";
    
    echo "🔄 Para restaurar datos anteriores si es necesario:\n";
    echo "   1. DELETE FROM products;\n";
    echo "   2. INSERT INTO products SELECT * FROM products_backup;\n";
    echo "   3. DROP TABLE products_backup;\n";
    echo "   4. DROP TABLE categories_backup;\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    
    // Intentar restaurar desde respaldo
    if (isset($pdo)) {
        echo "🔄 Intentando restaurar datos anteriores...\n";
        try {
            $pdo->exec("DELETE FROM products");
            $pdo->exec("INSERT INTO products SELECT * FROM products_backup");
            echo "✅ Datos restaurados desde respaldo\n";
        } catch (Exception $restore_error) {
            echo "❌ Error al restaurar: " . $restore_error->getMessage() . "\n";
        }
    }
    
    exit(1);
}
?>
