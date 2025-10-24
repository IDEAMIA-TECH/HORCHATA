<?php
/**
 * Horchata Mexican Food - Instalación Completa
 * Script para crear tablas e insertar menú completo
 */

// Configuración de la base de datos
$host = '173.231.22.109';
$dbname = 'ideamiadev_horchata';
$username = 'ideamiadev_horchata';
$password = 'DfabGqB&gX3xM?ea';

echo "<h1>🚀 Instalación Completa - Horchata Mexican Food</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px;'>";

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Conexión Exitosa</h3>";
    echo "<p>Conectado a la base de datos: <strong>$dbname</strong></p>";
    echo "</div>";
    
    // Paso 1: Crear esquema de base de datos
    echo "<h2>📋 Paso 1: Creando Esquema de Base de Datos</h2>";
    
    if (file_exists('database/schema.sql')) {
        $schema_sql = file_get_contents('database/schema.sql');
        
        // Deshabilitar verificación de claves foráneas
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Dividir el SQL en statements individuales
        $statements = array_filter(array_map('trim', explode(';', $schema_sql)));
        
        $created_tables = [];
        $errors = [];
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                    
                    // Extraer nombre de tabla si es CREATE TABLE
                    if (preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $statement, $matches)) {
                        $created_tables[] = $matches[1];
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error en statement: " . substr($statement, 0, 50) . "... - " . $e->getMessage();
                }
            }
        }
        
        // Rehabilitar verificación de claves foráneas
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        if (!empty($created_tables)) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Tablas Creadas Exitosamente</h4>";
            echo "<p>Se crearon <strong>" . count($created_tables) . "</strong> tablas:</p>";
            echo "<ul>";
            foreach ($created_tables as $table) {
                echo "<li><strong>$table</strong></li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        
        if (!empty($errors)) {
            echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
            echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Errores Encontrados</h4>";
            echo "<p>Se encontraron " . count($errors) . " errores:</p>";
            echo "<ul>";
            foreach (array_slice($errors, 0, 3) as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            if (count($errors) > 3) {
                echo "<li>... y " . (count($errors) - 3) . " errores más</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Archivo de Esquema No Encontrado</h4>";
        echo "<p>No se encontró el archivo <code>database/schema.sql</code></p>";
        echo "</div>";
    }
    
    // Paso 2: Insertar categorías
    echo "<h2>📁 Paso 2: Creando Categorías</h2>";
    
    $categories_sql = "
    INSERT INTO `categories` (`id`, `name_en`, `name_es`, `description_en`, `description_es`, `sort_order`, `is_active`) VALUES
    (1, 'Breakfast Plates', 'Platos de Desayuno', 'Traditional Mexican breakfast plates', 'Platos de desayuno tradicionales mexicanos', 1, 1),
    (2, 'Breakfast Burritos', 'Burritos de Desayuno', 'Hearty breakfast burritos', 'Burritos de desayuno sustanciosos', 2, 1),
    (3, 'Daily Specials', 'Especiales del Día', 'Chef\'s daily specials', 'Especiales del chef del día', 3, 1),
    (4, 'Seafood', 'Mariscos', 'Fresh seafood dishes', 'Platos de mariscos frescos', 4, 1),
    (5, 'Special Burritos', 'Burritos Especiales', 'Our signature burritos', 'Nuestros burritos especiales', 5, 1),
    (6, 'Combinations', 'Combinaciones', 'Perfect meal combinations', 'Combinaciones perfectas de comida', 6, 1),
    (7, 'Tacos & Quesadillas', 'Tacos y Quesadillas', 'Traditional tacos and quesadillas', 'Tacos y quesadillas tradicionales', 7, 1),
    (8, 'Desserts', 'Postres', 'Sweet endings to your meal', 'Finales dulces para tu comida', 8, 1),
    (9, 'Nachos & Sides', 'Nachos y Acompañamientos', 'Appetizers and side dishes', 'Aperitivos y acompañamientos', 9, 1),
    (10, 'Salads & Burgers', 'Ensaladas y Hamburguesas', 'Fresh salads and burgers', 'Ensaladas frescas y hamburguesas', 10, 1)
    ";
    
    try {
        $pdo->exec($categories_sql);
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Categorías Creadas</h4>";
        echo "<p>Se han creado <strong>10 categorías</strong> correctamente.</p>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al Crear Categorías</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Paso 3: Insertar productos del menú
    echo "<h2>🍽️ Paso 3: Insertando Productos del Menú</h2>";
    
    if (file_exists('database/menu-data-fixed.sql')) {
        $menu_sql = file_get_contents('database/menu-data-fixed.sql');
        
        // Dividir en statements individuales
        $statements = array_filter(array_map('trim', explode(';', $menu_sql)));
        
        $inserted_products = 0;
        $errors = [];
        
        foreach ($statements as $statement) {
            if (!empty($statement) && 
                !preg_match('/^--/', $statement) && 
                !preg_match('/^SELECT/', $statement) &&
                !preg_match('/^UPDATE/', $statement) &&
                !preg_match('/^DELETE/', $statement) &&
                !preg_match('/^SET/', $statement)) {
                
                try {
                    $pdo->exec($statement);
                    
                    if (preg_match('/INSERT INTO.*products/i', $statement)) {
                        $inserted_products++;
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error: " . $e->getMessage();
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
            foreach (array_slice($errors, 0, 3) as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            if (count($errors) > 3) {
                echo "<li>... y " . (count($errors) - 3) . " errores más</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Archivo No Encontrado</h4>";
        echo "<p>No se encontró el archivo <code>database/menu-data-fixed.sql</code></p>";
        echo "</div>";
    }
    
    // Paso 4: Crear usuario admin
    echo "<h2>👤 Paso 4: Creando Usuario Administrador</h2>";
    
    $admin_sql = "
    INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `role`, `is_active`) VALUES
    ('admin', 'admin@horchatamexicanfood.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1)
    ";
    
    try {
        $pdo->exec($admin_sql);
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Usuario Admin Creado</h4>";
        echo "<p>Usuario administrador creado correctamente.</p>";
        echo "<p><strong>Credenciales:</strong></p>";
        echo "<ul>";
        echo "<li>Usuario: admin@horchatamexicanfood.com</li>";
        echo "<li>Contraseña: password</li>";
        echo "</ul>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al Crear Usuario Admin</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Paso 5: Configuraciones básicas
    echo "<h2>⚙️ Paso 5: Configuraciones Básicas</h2>";
    
    $settings_sql = "
    INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
    ('site_name', 'Horchata Mexican Food', 'Nombre del sitio'),
    ('site_description', 'Authentic Mexican Food Restaurant', 'Descripción del sitio'),
    ('currency', 'USD', 'Moneda del sistema'),
    ('tax_rate', '8.25', 'Tasa de impuestos'),
    ('timezone', 'America/Los_Angeles', 'Zona horaria'),
    ('language', 'en', 'Idioma por defecto'),
    ('email_notifications', '1', 'Notificaciones por email'),
    ('order_notifications', '1', 'Notificaciones de pedidos')
    ";
    
    try {
        $pdo->exec($settings_sql);
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Configuraciones Creadas</h4>";
        echo "<p>Se han creado <strong>8 configuraciones</strong> básicas del sistema.</p>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Error al Crear Configuraciones</h4>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    // Paso 6: Verificación final
    echo "<h2>📊 Paso 6: Verificación Final</h2>";
    
    try {
        // Contar categorías
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $categories_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Contar productos
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $products_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Contar usuarios admin
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Contar configuraciones
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM settings");
        $settings_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Verificación Exitosa</h4>";
        echo "<p><strong>Categorías:</strong> $categories_count</p>";
        echo "<p><strong>Productos:</strong> $products_count</p>";
        echo "<p><strong>Usuarios Admin:</strong> $admin_count</p>";
        echo "<p><strong>Configuraciones:</strong> $settings_count</p>";
        echo "</div>";
        
        // Mostrar resumen por categoría
        if ($products_count > 0) {
            echo "<h3>📋 Resumen por Categoría:</h3>";
            $stmt = $pdo->query("
                SELECT c.name_en, COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id
                GROUP BY c.id, c.name_en
                ORDER BY c.sort_order
            ");
            $category_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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
        echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Instalación Completa Exitosa!</h3>";
        echo "<p>El sistema de Horchata Mexican Food ha sido instalado correctamente.</p>";
        echo "<p><strong>Próximos pasos:</strong></p>";
        echo "<ul>";
        echo "<li>✅ <strong>Configurar PayPal:</strong> Actualizar credenciales en <code>config/development.php</code></li>";
        echo "<li>✅ <strong>Probar el sistema:</strong> <a href='index.php'>Ir al sitio</a></li>";
        echo "<li>✅ <strong>Panel administrativo:</strong> <a href='admin/index.php'>Acceder al admin</a></li>";
        echo "<li>✅ <strong>Subir imágenes:</strong> Agregar fotos de productos</li>";
        echo "<li>✅ <strong>¡Comenzar a recibir pedidos!</strong></li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Instalación Incompleta</h3>";
        echo "<p>Algunos elementos no se instalaron correctamente.</p>";
        echo "<p><strong>Acciones recomendadas:</strong></p>";
        echo "<ul>";
        echo "<li>Revisar los errores mostrados arriba</li>";
        echo "<li>Verificar permisos de la base de datos</li>";
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
