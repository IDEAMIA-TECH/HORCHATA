<?php
/**
 * Horchata Mexican Food - Corrector de Datos del Menú
 * Script para corregir problemas de claves foráneas
 */

// Configuración de la base de datos
$host = '173.231.22.109';
$dbname = 'ideamiadev_horchata';
$username = 'ideamiadev_horchata';
$password = 'DfabGqB&gX3xM?ea';

echo "<h1>🔧 Corrector de Datos del Menú - Horchata Mexican Food</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px;'>";

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Conexión Exitosa</h3>";
    echo "<p>Conectado a la base de datos: <strong>$dbname</strong></p>";
    echo "</div>";
    
    // Paso 1: Verificar si las categorías existen
    echo "<h2>📋 Paso 1: Verificando Categorías</h2>";
    
    $stmt = $pdo->query("SELECT id, name_en FROM categories ORDER BY id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 20px;'>";
        echo "<h4 style='color: #856404; margin-top: 0;'>⚠️ No hay categorías</h4>";
        echo "<p>Necesitas crear las categorías primero. Ejecuta el instalador completo.</p>";
        echo "<p><a href='install-menu.php' style='background-color: #d4af37; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ejecutar Instalador Completo</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Categorías Encontradas</h4>";
        echo "<p>Se encontraron <strong>" . count($categories) . "</strong> categorías:</p>";
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li><strong>ID {$category['id']}:</strong> " . htmlspecialchars($category['name_en']) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    // Paso 2: Limpiar productos existentes si es necesario
    echo "<h2>🧹 Paso 2: Limpiando Datos Existentes</h2>";
    
    try {
        // Deshabilitar verificación de claves foráneas temporalmente
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Limpiar tablas en orden correcto
        $tables_to_clean = ['order_items', 'orders', 'reviews', 'review_tokens', 'products'];
        
        foreach ($tables_to_clean as $table) {
            try {
                $pdo->exec("DELETE FROM $table");
                echo "<p>✅ Tabla <strong>$table</strong> limpiada</p>";
            } catch (PDOException $e) {
                echo "<p>⚠️ No se pudo limpiar <strong>$table</strong>: " . $e->getMessage() . "</p>";
            }
        }
        
        // Rehabilitar verificación de claves foráneas
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Datos Limpiados</h4>";
        echo "<p>Las tablas han sido limpiadas correctamente.</p>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al Limpiar</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Paso 3: Insertar categorías si no existen
    echo "<h2>📁 Paso 3: Creando Categorías</h2>";
    
    if (empty($categories)) {
        $categories_sql = "
        INSERT INTO `categories` (`name_en`, `name_es`, `description_en`, `description_es`, `sort_order`, `is_active`) VALUES
        ('Breakfast Plates', 'Platos de Desayuno', 'Traditional Mexican breakfast plates', 'Platos de desayuno tradicionales mexicanos', 1, 1),
        ('Breakfast Burritos', 'Burritos de Desayuno', 'Hearty breakfast burritos', 'Burritos de desayuno sustanciosos', 2, 1),
        ('Daily Specials', 'Especiales del Día', 'Chef\'s daily specials', 'Especiales del chef del día', 3, 1),
        ('Seafood', 'Mariscos', 'Fresh seafood dishes', 'Platos de mariscos frescos', 4, 1),
        ('Special Burritos', 'Burritos Especiales', 'Our signature burritos', 'Nuestros burritos especiales', 5, 1),
        ('Combinations', 'Combinaciones', 'Perfect meal combinations', 'Combinaciones perfectas de comida', 6, 1),
        ('Tacos & Quesadillas', 'Tacos y Quesadillas', 'Traditional tacos and quesadillas', 'Tacos y quesadillas tradicionales', 7, 1),
        ('Desserts', 'Postres', 'Sweet endings to your meal', 'Finales dulces para tu comida', 8, 1),
        ('Nachos & Sides', 'Nachos y Acompañamientos', 'Appetizers and side dishes', 'Aperitivos y acompañamientos', 9, 1),
        ('Salads & Burgers', 'Ensaladas y Hamburguesas', 'Fresh salads and burgers', 'Ensaladas frescas y hamburguesas', 10, 1)
        ";
        
        try {
            $pdo->exec($categories_sql);
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Categorías Creadas</h4>";
            echo "<p>Se han creado 10 categorías correctamente.</p>";
            echo "</div>";
        } catch (PDOException $e) {
            echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
            echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al Crear Categorías</h4>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    }
    
    // Paso 4: Insertar productos por lotes pequeños
    echo "<h2>🍽️ Paso 4: Insertando Productos</h2>";
    
    // Leer el archivo de datos del menú
    if (file_exists('database/menu-data.sql')) {
        $menu_sql = file_get_contents('database/menu-data.sql');
        
        // Dividir en statements individuales
        $statements = array_filter(array_map('trim', explode(';', $menu_sql)));
        
        $inserted_products = 0;
        $errors = [];
        
        foreach ($statements as $statement) {
            if (!empty($statement) && 
                !preg_match('/^--/', $statement) && 
                !preg_match('/^SELECT/', $statement) &&
                !preg_match('/^UPDATE/', $statement) &&
                !preg_match('/^DELETE/', $statement)) {
                
                try {
                    $pdo->exec($statement);
                    
                    if (preg_match('/INSERT INTO.*products/i', $statement)) {
                        $inserted_products++;
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error en statement: " . substr($statement, 0, 100) . "... - " . $e->getMessage();
                }
            }
        }
        
        if ($inserted_products > 0) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Productos Insertados</h4>";
            echo "<p>Se han insertado <strong>$inserted_products</strong> productos correctamente.</p>";
            echo "</div>";
        }
        
        if (!empty($errors)) {
            echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
            echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Errores Encontrados</h4>";
            echo "<p>Se encontraron " . count($errors) . " errores:</p>";
            echo "<ul>";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            if (count($errors) > 5) {
                echo "<li>... y " . (count($errors) - 5) . " errores más</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Archivo No Encontrado</h4>";
        echo "<p>No se encontró el archivo <code>database/menu-data.sql</code></p>";
        echo "</div>";
    }
    
    // Paso 5: Verificar resultado final
    echo "<h2>📊 Paso 5: Verificación Final</h2>";
    
    try {
        // Contar categorías
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $categories_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Contar productos
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $products_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Contar productos por categoría
        $stmt = $pdo->query("
            SELECT c.name_en, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name_en
            ORDER BY c.sort_order
        ");
        $category_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Verificación Exitosa</h4>";
        echo "<p><strong>Categorías:</strong> $categories_count</p>";
        echo "<p><strong>Productos:</strong> $products_count</p>";
        echo "</div>";
        
        if ($products_count > 0) {
            echo "<h3>📋 Productos por Categoría:</h3>";
            echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background-color: #d4af37; color: white; font-weight: bold;'>";
            echo "<th>Categoría</th><th>Productos</th>";
            echo "</tr>";
            
            foreach ($category_products as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name_en']) . "</td>";
                echo "<td style='text-align: center;'>" . $row['product_count'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error en Verificación</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Resumen final
    echo "<h2>🎉 Resumen Final</h2>";
    
    if ($products_count > 0) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Corrección Exitosa!</h3>";
        echo "<p>El menú ha sido corregido e instalado correctamente.</p>";
        echo "<p><strong>Próximos pasos:</strong></p>";
        echo "<ul>";
        echo "<li>✅ <strong>Verificar instalación:</strong> <a href='check-database.php'>Verificar estado de la BD</a></li>";
        echo "<li>✅ <strong>Configurar PayPal:</strong> Actualizar credenciales en <code>config/development.php</code></li>";
        echo "<li>✅ <strong>Probar el sistema:</strong> <a href='index.php'>Ir al sitio</a></li>";
        echo "<li>✅ <strong>Panel administrativo:</strong> <a href='admin/index.php'>Acceder al admin</a></li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Instalación Incompleta</h3>";
        echo "<p>Algunos productos no se insertaron correctamente.</p>";
        echo "<p><strong>Acciones recomendadas:</strong></p>";
        echo "<ul>";
        echo "<li>Revisar los errores mostrados arriba</li>";
        echo "<li>Verificar que las categorías existan</li>";
        echo "<li>Intentar ejecutar el script nuevamente</li>";
        echo "<li>Contactar al administrador del servidor</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h2 style='color: #721c24;'>❌ Error de Conexión</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Verifica las credenciales de la base de datos:</strong></p>";
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
