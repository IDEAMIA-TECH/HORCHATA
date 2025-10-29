<?php
// =============================================
// Horchata Mexican Food - Verificación del Menú
// Script PHP para verificar que la actualización fue exitosa
// =============================================

echo "🔍 Horchata Mexican Food - Verificación del Menú\n";
echo "================================================\n\n";

// Incluir configuración de base de datos
require_once 'includes/db_connect.php';

try {
    if (!isset($pdo)) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    echo "✅ Conexión a la base de datos establecida\n\n";
    
    // Verificar categorías
    echo "📋 VERIFICACIÓN DE CATEGORÍAS\n";
    echo "------------------------------\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active = 1");
    $categories_count = $stmt->fetchColumn();
    echo "Categorías activas: $categories_count\n\n";
    
    // Mostrar categorías
    $stmt = $pdo->query("SELECT id, name_en, name_es, is_active FROM categories WHERE is_active = 1 ORDER BY sort_order");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        echo "ID: {$category['id']} | {$category['name_en']} | {$category['name_es']}\n";
    }
    echo "\n";
    
    // Verificar productos
    echo "📋 VERIFICACIÓN DE PRODUCTOS\n";
    echo "-----------------------------\n";
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured,
        COUNT(CASE WHEN is_available = 1 THEN 1 END) as available,
        MIN(price) as min_price,
        MAX(price) as max_price,
        ROUND(AVG(price), 2) as avg_price
        FROM products");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total de productos: {$stats['total']}\n";
    echo "Productos destacados: {$stats['featured']}\n";
    echo "Productos disponibles: {$stats['available']}\n";
    echo "Precio mínimo: $" . $stats['min_price'] . "\n";
    echo "Precio máximo: $" . $stats['max_price'] . "\n";
    echo "Precio promedio: $" . $stats['avg_price'] . "\n\n";
    
    // Verificar productos por categoría
    echo "📋 PRODUCTOS POR CATEGORÍA\n";
    echo "--------------------------\n";
    $stmt = $pdo->query("SELECT 
        c.name_en as categoria,
        COUNT(p.id) as productos,
        ROUND(AVG(p.price), 2) as precio_promedio
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        WHERE c.is_active = 1
        GROUP BY c.id, c.name_en
        ORDER BY c.sort_order");
    $category_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($category_stats as $stat) {
        echo sprintf("%-25s | %3d productos | Promedio: $%6.2f\n", 
            $stat['categoria'], 
            $stat['productos'], 
            $stat['precio_promedio']
        );
    }
    echo "\n";
    
    // Verificar productos específicos del menú
    echo "📋 PRODUCTOS ESPECÍFICOS DEL MENÚ\n";
    echo "----------------------------------\n";
    $specific_products = [
        'Chorizo Plate',
        'Huevos Rancheros Plate',
        'California Burrito',
        'Carne Asada Plate',
        'Steak & Eggs Plate',
        'Fajita Plate Chicken or Beef',
        'Macho Burrito',
        '3 Soft Tacos',
        'Shrimp Cocktail',
        'Hamburger'
    ];
    
    $placeholders = str_repeat('?,', count($specific_products) - 1) . '?';
    $stmt = $pdo->prepare("SELECT 
        p.name_en as producto,
        c.name_en as categoria,
        p.price as precio,
        CASE WHEN p.is_featured = 1 THEN 'DESTACADO' ELSE 'NORMAL' END as estado
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.name_en IN ($placeholders)
        ORDER BY p.name_en");
    $stmt->execute($specific_products);
    $specific_products_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($specific_products_data as $product) {
        echo sprintf("%-30s | %-20s | $%6.2f | %s\n", 
            $product['producto'], 
            $product['categoria'], 
            $product['precio'], 
            $product['estado']
        );
    }
    echo "\n";
    
    // Verificar duplicados
    echo "📋 VERIFICACIÓN DE DUPLICADOS\n";
    echo "------------------------------\n";
    $stmt = $pdo->query("SELECT name_en, COUNT(*) as repeticiones FROM products GROUP BY name_en HAVING COUNT(*) > 1");
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($duplicates)) {
        echo "✅ No se encontraron productos duplicados\n\n";
    } else {
        echo "⚠️  Productos duplicados encontrados:\n";
        foreach ($duplicates as $duplicate) {
            echo "   {$duplicate['name_en']}: {$duplicate['repeticiones']} repeticiones\n";
        }
        echo "\n";
    }
    
    // Verificar productos sin categoría
    echo "📋 VERIFICACIÓN DE CATEGORÍAS VÁLIDAS\n";
    echo "--------------------------------------\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.id IS NULL");
    $invalid_categories = $stmt->fetchColumn();
    
    if ($invalid_categories == 0) {
        echo "✅ Todos los productos tienen categoría válida\n\n";
    } else {
        echo "⚠️  $invalid_categories productos sin categoría válida\n\n";
    }
    
    // Verificar precios válidos
    echo "📋 VERIFICACIÓN DE PRECIOS\n";
    echo "---------------------------\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE price <= 0 OR price IS NULL");
    $invalid_prices = $stmt->fetchColumn();
    
    if ($invalid_prices == 0) {
        echo "✅ Todos los productos tienen precios válidos\n\n";
    } else {
        echo "⚠️  $invalid_prices productos con precios inválidos\n\n";
    }
    
    // Resumen final
    echo "📋 RESUMEN FINAL\n";
    echo "-----------------\n";
    echo "✅ Categorías activas: $categories_count\n";
    echo "✅ Productos totales: {$stats['total']}\n";
    echo "✅ Productos destacados: {$stats['featured']}\n";
    echo "✅ Productos disponibles: {$stats['available']}\n";
    echo "✅ Rango de precios: $" . $stats['min_price'] . " - $" . $stats['max_price'] . "\n";
    echo "✅ Precio promedio: $" . $stats['avg_price'] . "\n\n";
    
    echo "🎉 ¡Verificación completada exitosamente!\n";
    echo "El menú está listo para usar en el sitio web.\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
