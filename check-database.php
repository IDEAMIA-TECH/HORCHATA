<?php
/**
 * Horchata Mexican Food - Verificador de Base de Datos
 * Script para verificar el estado de las tablas y datos
 */

// Configuración de la base de datos
$host = '173.231.22.109';
$dbname = 'ideamiadev_horchata';
$username = 'ideamiadev_horchata';
$password = 'DfabGqB&gX3xM?ea';

echo "<h1>🔍 Verificador de Base de Datos - Horchata Mexican Food</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px;'>";

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Conexión Exitosa</h3>";
    echo "<p>Conectado a la base de datos: <strong>$dbname</strong></p>";
    echo "</div>";
    
    // Verificar si las tablas existen
    echo "<h2>📋 Verificación de Tablas</h2>";
    
    $tables_to_check = [
        'users' => 'Usuarios del sistema',
        'categories' => 'Categorías de productos',
        'products' => 'Productos del menú',
        'orders' => 'Pedidos de clientes',
        'order_items' => 'Items de pedidos',
        'reviews' => 'Reseñas de clientes',
        'review_tokens' => 'Tokens para reseñas',
        'settings' => 'Configuración del sistema',
        'notifications' => 'Notificaciones del sistema'
    ];
    
    echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr style='background-color: #d4af37; color: white; font-weight: bold;'>";
    echo "<th>Tabla</th><th>Descripción</th><th>Estado</th><th>Registros</th><th>Acción</th>";
    echo "</tr>";
    
    $missing_tables = [];
    
    foreach ($tables_to_check as $table => $description) {
        try {
            // Verificar si la tabla existe
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            $table_exists = $stmt->rowCount() > 0;
            
            if ($table_exists) {
                // Contar registros
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                $status = $count > 0 ? "✅ Con datos ($count)" : "⚠️ Vacía";
                $action = $count > 0 ? "OK" : "Necesita datos";
                
                echo "<tr style='background-color: " . ($count > 0 ? '#d4edda' : '#fff3cd') . ";'>";
                echo "<td><strong>$table</strong></td>";
                echo "<td>$description</td>";
                echo "<td>$status</td>";
                echo "<td>$count</td>";
                echo "<td>$action</td>";
                echo "</tr>";
            } else {
                $missing_tables[] = $table;
                echo "<tr style='background-color: #f8d7da;'>";
                echo "<td><strong>$table</strong></td>";
                echo "<td>$description</td>";
                echo "<td>❌ No existe</td>";
                echo "<td>0</td>";
                echo "<td>Necesita crear</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "<tr style='background-color: #f8d7da;'>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>$description</td>";
            echo "<td>❌ Error</td>";
            echo "<td>0</td>";
            echo "<td>Error: " . $e->getMessage() . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    // Si hay tablas faltantes, mostrar opciones
    if (!empty($missing_tables)) {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 20px;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Tablas Faltantes</h3>";
        echo "<p>Las siguientes tablas no existen en la base de datos:</p>";
        echo "<ul>";
        foreach ($missing_tables as $table) {
            echo "<li><strong>$table</strong></li>";
        }
        echo "</ul>";
        echo "<p><strong>Solución:</strong> Ejecutar el script de instalación automática:</p>";
        echo "<p><a href='install-menu.php' style='background-color: #d4af37; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ejecutar Instalador</a></p>";
        echo "</div>";
    }
    
    // Verificar datos específicos
    echo "<h2>📊 Verificación de Datos</h2>";
    
    // Verificar categorías
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $categories_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($categories_count > 0) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 10px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Categorías: $categories_count</h4>";
            
            // Mostrar categorías
            $stmt = $pdo->query("SELECT name_en, name_es FROM categories ORDER BY sort_order");
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><strong>" . htmlspecialchars($row['name_en']) . "</strong> - " . htmlspecialchars($row['name_es']) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 10px;'>";
            echo "<h4 style='color: #856404; margin-top: 0;'>⚠️ No hay categorías</h4>";
            echo "<p>Necesitas ejecutar el instalador para crear las categorías.</p>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 10px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al verificar categorías</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Verificar productos
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $products_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($products_count > 0) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 10px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Productos: $products_count</h4>";
            
            // Mostrar algunos productos
            $stmt = $pdo->query("SELECT name_en, price, is_featured FROM products ORDER BY price ASC LIMIT 5");
            echo "<p><strong>Algunos productos:</strong></p>";
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $featured = $row['is_featured'] ? ' ⭐' : '';
                echo "<li><strong>" . htmlspecialchars($row['name_en']) . "</strong> - $" . number_format($row['price'], 2) . "$featured</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 10px;'>";
            echo "<h4 style='color: #856404; margin-top: 0;'>⚠️ No hay productos</h4>";
            echo "<p>Necesitas ejecutar el instalador para crear los productos del menú.</p>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 10px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al verificar productos</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Verificar usuario admin
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($admin_count > 0) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 10px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Usuario Admin: $admin_count</h4>";
            echo "<p>Usuario administrador creado correctamente.</p>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 10px;'>";
            echo "<h4 style='color: #856404; margin-top: 0;'>⚠️ No hay usuario admin</h4>";
            echo "<p>Necesitas crear un usuario administrador.</p>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 10px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al verificar usuario admin</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Resumen final
    echo "<h2>📋 Resumen del Estado</h2>";
    
    if (empty($missing_tables)) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Base de Datos Completa!</h3>";
        echo "<p>Todas las tablas existen y están listas para usar.</p>";
        echo "<p><strong>Próximos pasos:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Configurar PayPal en <code>config/development.php</code></li>";
        echo "<li>✅ Subir imágenes de productos</li>";
        echo "<li>✅ Configurar dominio y SSL</li>";
        echo "<li>✅ ¡Comenzar a recibir pedidos!</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Base de Datos Incompleta</h3>";
        echo "<p>Faltan " . count($missing_tables) . " tablas en la base de datos.</p>";
        echo "<p><strong>Acción requerida:</strong> Ejecutar el instalador automático.</p>";
        echo "<p><a href='install-menu.php' style='background-color: #d4af37; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ejecutar Instalador</a></p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h2 style='color: #721c24;'>❌ Error de Conexión</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Posibles causas:</strong></p>";
    echo "<ul>";
    echo "<li>Credenciales de base de datos incorrectas</li>";
    echo "<li>Servidor de base de datos no disponible</li>";
    echo "<li>Base de datos no existe</li>";
    echo "<li>Permisos insuficientes</li>";
    echo "</ul>";
    echo "<p><strong>Verifica:</strong></p>";
    echo "<ul>";
    echo "<li>Host: $host</li>";
    echo "<li>Database: $dbname</li>";
    echo "<li>Username: $username</li>";
    echo "<li>Password: [configurado]</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div>";
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
