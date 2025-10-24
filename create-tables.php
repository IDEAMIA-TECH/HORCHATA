<?php
/**
 * Horchata Mexican Food - Creador de Tablas
 * Script para crear las tablas de la base de datos
 */

// Configuración de la base de datos
$host = '173.231.22.109';
$dbname = 'ideamiadev_horchata';
$username = 'ideamiadev_horchata';
$password = 'DfabGqB&gX3xM?ea';

echo "<h1>🔧 Creador de Tablas - Horchata Mexican Food</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Conexión Exitosa</h3>";
    echo "<p>Conectado a la base de datos: <strong>$dbname</strong></p>";
    echo "</div>";
    
    // Leer y ejecutar el esquema
    echo "<h2>📋 Creando Tablas...</h2>";
    
    if (file_exists('database/schema.sql')) {
        $schema_sql = file_get_contents('database/schema.sql');
        
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
        
        if (!empty($created_tables)) {
            echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 10px;'>";
            echo "<h4 style='color: #155724; margin-top: 0;'>✅ Tablas Creadas Exitosamente</h4>";
            echo "<ul>";
            foreach ($created_tables as $table) {
                echo "<li><strong>$table</strong></li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        
        if (!empty($errors)) {
            echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 10px;'>";
            echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Errores Encontrados</h4>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545; margin-bottom: 10px;'>";
        echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Archivo de Esquema No Encontrado</h4>";
        echo "<p>No se encontró el archivo <code>database/schema.sql</code></p>";
        echo "</div>";
    }
    
    // Verificar tablas creadas
    echo "<h2>🔍 Verificando Tablas Creadas</h2>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($tables)) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 10px;'>";
        echo "<h4 style='color: #155724; margin-top: 0;'>✅ Tablas en la Base de Datos</h4>";
        echo "<p>Se encontraron <strong>" . count($tables) . "</strong> tablas:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong></li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin-bottom: 10px;'>";
        echo "<h4 style='color: #856404; margin-top: 0;'>⚠️ No se Encontraron Tablas</h4>";
        echo "<p>No hay tablas en la base de datos.</p>";
        echo "</div>";
    }
    
    // Verificar estructura de tablas específicas
    $expected_tables = ['users', 'categories', 'products', 'orders', 'order_items', 'reviews', 'review_tokens', 'settings', 'notifications'];
    
    echo "<h2>📊 Verificación Detallada</h2>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #d4af37; color: white; font-weight: bold;'>";
    echo "<th>Tabla</th><th>Estado</th><th>Columnas</th><th>Registros</th>";
    echo "</tr>";
    
    foreach ($expected_tables as $table) {
        if (in_array($table, $tables)) {
            try {
                // Obtener información de la tabla
                $stmt = $pdo->query("DESCRIBE $table");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                $status = $count > 0 ? "✅ Con datos" : "⚠️ Vacía";
                $record_count = $count;
                
                echo "<tr style='background-color: " . ($count > 0 ? '#d4edda' : '#fff3cd') . ";'>";
                echo "<td><strong>$table</strong></td>";
                echo "<td>$status</td>";
                echo "<td>" . count($columns) . "</td>";
                echo "<td>$record_count</td>";
                echo "</tr>";
                
            } catch (PDOException $e) {
                echo "<tr style='background-color: #f8d7da;'>";
                echo "<td><strong>$table</strong></td>";
                echo "<td>❌ Error</td>";
                echo "<td>0</td>";
                echo "<td>0</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr style='background-color: #f8d7da;'>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>❌ No existe</td>";
            echo "<td>0</td>";
            echo "<td>0</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    // Próximos pasos
    echo "<h2>🚀 Próximos Pasos</h2>";
    
    if (count($tables) >= 8) {
        echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Tablas Creadas Exitosamente!</h3>";
        echo "<p>Las tablas de la base de datos han sido creadas correctamente.</p>";
        echo "<p><strong>Próximos pasos:</strong></p>";
        echo "<ol>";
        echo "<li>✅ <strong>Poblar con datos:</strong> <a href='install-menu.php'>Ejecutar instalador del menú</a></li>";
        echo "<li>✅ <strong>Verificar instalación:</strong> <a href='check-database.php'>Verificar estado de la BD</a></li>";
        echo "<li>✅ <strong>Configurar PayPal:</strong> Actualizar credenciales en <code>config/development.php</code></li>";
        echo "<li>✅ <strong>¡Comenzar a usar el sistema!</strong></li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Instalación Incompleta</h3>";
        echo "<p>Algunas tablas no se crearon correctamente.</p>";
        echo "<p><strong>Acciones recomendadas:</strong></p>";
        echo "<ol>";
        echo "<li>Verificar permisos de la base de datos</li>";
        echo "<li>Revisar los errores mostrados arriba</li>";
        echo "<li>Intentar ejecutar el script nuevamente</li>";
        echo "<li>Contactar al administrador del servidor</li>";
        echo "</ol>";
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
