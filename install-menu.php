<?php
/**
 * Horchata Mexican Food - Instalador del Menú
 * Script para poblar la base de datos con el menú completo
 */

// Configuración de la base de datos
$host = '173.231.22.109';
$dbname = 'ideamiadev_horchata';
$username = 'ideamiadev_horchata';
$password = 'DfabGqB&gX3xM?ea';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🍽️ Instalador del Menú - Horchata Mexican Food</h1>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";
    
    // Leer y ejecutar el esquema de la base de datos
    echo "<h2>📋 Paso 1: Ejecutando esquema de base de datos...</h2>";
    $schema_sql = file_get_contents('database/schema.sql');
    $pdo->exec($schema_sql);
    echo "✅ Esquema de base de datos ejecutado correctamente<br><br>";
    
    // Leer y ejecutar los datos del menú
    echo "<h2>🍽️ Paso 2: Insertando menú completo...</h2>";
    $menu_sql = file_get_contents('database/menu-data.sql');
    $pdo->exec($menu_sql);
    echo "✅ Menú completo insertado correctamente<br><br>";
    
    // Verificar la instalación
    echo "<h2>📊 Paso 3: Verificando instalación...</h2>";
    
    // Contar categorías
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $categories_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📁 Categorías creadas: <strong>$categories_count</strong><br>";
    
    // Contar productos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $products_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "🍽️ Productos creados: <strong>$products_count</strong><br>";
    
    // Verificar usuario admin
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "👤 Usuarios admin: <strong>$admin_count</strong><br><br>";
    
    // Mostrar resumen por categoría
    echo "<h2>📈 Resumen por Categoría:</h2>";
    $stmt = $pdo->query("
        SELECT 
            c.name_en as 'Categoría',
            COUNT(p.id) as 'Productos',
            MIN(p.price) as 'Precio_Mín',
            MAX(p.price) as 'Precio_Máx',
            ROUND(AVG(p.price), 2) as 'Precio_Promedio'
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name_en
        ORDER BY c.sort_order
    ");
    
    echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #d4af37; color: white; font-weight: bold;'>";
    echo "<th>Categoría</th><th>Productos</th><th>Precio Mín</th><th>Precio Máx</th><th>Precio Promedio</th>";
    echo "</tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Categoría']) . "</td>";
        echo "<td style='text-align: center;'>" . $row['Productos'] . "</td>";
        echo "<td style='text-align: center;'>$" . number_format($row['Precio_Mín'], 2) . "</td>";
        echo "<td style='text-align: center;'>$" . number_format($row['Precio_Máx'], 2) . "</td>";
        echo "<td style='text-align: center;'>$" . number_format($row['Precio_Promedio'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Mostrar algunos productos destacados
    echo "<h2>⭐ Productos Destacados:</h2>";
    $stmt = $pdo->query("
        SELECT p.name_en, p.price, c.name_en as category
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.is_featured = 1
        ORDER BY p.price ASC
        LIMIT 10
    ");
    
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li><strong>" . htmlspecialchars($row['name_en']) . "</strong> - $" . number_format($row['price'], 2) . " (" . htmlspecialchars($row['category']) . ")</li>";
    }
    echo "</ul><br>";
    
    // Información de acceso
    echo "<h2>🔑 Información de Acceso:</h2>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #d4af37;'>";
    echo "<strong>Panel Administrativo:</strong><br>";
    echo "URL: <a href='admin/index.php'>admin/index.php</a><br>";
    echo "Usuario: admin@horchatamexicanfood.com<br>";
    echo "Contraseña: password<br><br>";
    echo "<strong>Sitio Público:</strong><br>";
    echo "URL: <a href='index.php'>index.php</a><br>";
    echo "</div><br>";
    
    // Próximos pasos
    echo "<h2>🚀 Próximos Pasos:</h2>";
    echo "<ol>";
    echo "<li>✅ <strong>Configurar PayPal:</strong> Actualizar credenciales en <code>config/development.php</code></li>";
    echo "<li>✅ <strong>Subir imágenes:</strong> Agregar fotos de productos en <code>assets/images/products/</code></li>";
    echo "<li>✅ <strong>Configurar dominio:</strong> Apuntar dominio al servidor</li>";
    echo "<li>✅ <strong>Configurar SSL:</strong> Instalar certificado SSL para HTTPS</li>";
    echo "<li>✅ <strong>Entrenar personal:</strong> Capacitar en el uso del panel administrativo</li>";
    echo "<li>✅ <strong>¡Comenzar a recibir pedidos!</strong> 🎉</li>";
    echo "</ol>";
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-top: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Instalación Completada Exitosamente!</h3>";
    echo "<p style='margin-bottom: 0;'>El sistema de Horchata Mexican Food está listo para recibir pedidos reales.</p>";
    echo "</div>";
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h2 style='color: #721c24;'>❌ Error en la Instalación</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Por favor, verifica la configuración de la base de datos y vuelve a intentar.</p>";
    echo "</div>";
}
?>

<style>
body {
    background-color: #f8f9fa;
    margin: 0;
    padding: 20px;
}

h1 {
    color: #d4af37;
    text-align: center;
    margin-bottom: 30px;
}

h2 {
    color: #2c2c2c;
    border-bottom: 2px solid #d4af37;
    padding-bottom: 5px;
}

table {
    margin: 10px 0;
}

th, td {
    text-align: left;
    padding: 8px;
}

ul, ol {
    line-height: 1.6;
}

code {
    background-color: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}

a {
    color: #d4af37;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
