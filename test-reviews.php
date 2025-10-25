<?php
/**
 * Test Reviews - Verificar funcionamiento de reviews.php
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Reviews</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Test Reviews - Diagnóstico</h1>
        <hr>
";

try {
    // Verificar conexión a la base de datos
    echo "<h2>1. Verificar Conexión a Base de Datos</h2>";
    $pdo = getDbConnection();
    echo "<div class='alert alert-success'>✅ Conexión a base de datos exitosa</div>";
    
    // Verificar si existe la función fetchOne
    echo "<h2>2. Verificar Función fetchOne</h2>";
    if (function_exists('fetchOne')) {
        echo "<div class='alert alert-success'>✅ Función fetchOne disponible</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Función fetchOne no encontrada</div>";
        
        // Definir función fetchOne si no existe
        if (!function_exists('fetchOne')) {
            function fetchOne($sql, $params = []) {
                global $pdo;
                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error en fetchOne: " . $e->getMessage() . "</div>";
                    return false;
                }
            }
            echo "<div class='alert alert-info'>ℹ️ Función fetchOne definida localmente</div>";
        }
    }
    
    // Verificar tablas necesarias
    echo "<h2>3. Verificar Tablas</h2>";
    
    $tables = ['orders', 'order_items', 'reviews'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='alert alert-success'>✅ Tabla '$table' existe</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Tabla '$table' no existe</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>❌ Error verificando tabla '$table': " . $e->getMessage() . "</div>";
        }
    }
    
    // Verificar órdenes con tokens de reseña
    echo "<h2>4. Verificar Órdenes con Tokens de Reseña</h2>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE review_token IS NOT NULL AND review_token != ''");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<div class='alert alert-info'>📊 Total de órdenes con tokens: " . $result['total'] . "</div>";
        
        if ($result['total'] > 0) {
            // Mostrar algunas órdenes con tokens
            $stmt = $pdo->query("SELECT id, order_number, review_token, status, created_at FROM orders WHERE review_token IS NOT NULL AND review_token != '' LIMIT 5");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h4>Órdenes con tokens de reseña:</h4>";
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>ID</th><th>Número</th><th>Token</th><th>Estado</th><th>Fecha</th></tr></thead>";
            echo "<tbody>";
            foreach ($orders as $order) {
                echo "<tr>";
                echo "<td>" . $order['id'] . "</td>";
                echo "<td>" . $order['order_number'] . "</td>";
                echo "<td><code>" . substr($order['review_token'], 0, 20) . "...</code></td>";
                echo "<td><span class='badge bg-" . ($order['status'] === 'completed' ? 'success' : 'warning') . "'>" . $order['status'] . "</span></td>";
                echo "<td>" . $order['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            
            // Probar acceso a reviews.php con el primer token
            if (!empty($orders)) {
                $firstOrder = $orders[0];
                echo "<h4>Probar acceso a reviews.php:</h4>";
                echo "<div class='alert alert-info'>";
                echo "URL de prueba: <a href='reviews.php?token=" . $firstOrder['review_token'] . "' target='_blank'>reviews.php?token=" . $firstOrder['review_token'] . "</a>";
                echo "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>⚠️ No hay órdenes con tokens de reseña</div>";
            echo "<p>Para crear una orden de prueba con token de reseña:</p>";
            echo "<pre><code>";
            echo "INSERT INTO orders (order_number, customer_name, customer_email, review_token, status, created_at) VALUES ";
            echo "('TEST001', 'Cliente Prueba', 'test@example.com', 'test-token-123', 'completed', NOW());";
            echo "</code></pre>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>❌ Error verificando órdenes: " . $e->getMessage() . "</div>";
    }
    
    // Verificar si hay errores en el archivo reviews.php
    echo "<h2>5. Verificar Errores en reviews.php</h2>";
    $reviewsFile = __DIR__ . '/reviews.php';
    if (file_exists($reviewsFile)) {
        echo "<div class='alert alert-success'>✅ Archivo reviews.php existe</div>";
        
        // Verificar sintaxis PHP
        $output = shell_exec("php -l $reviewsFile 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "<div class='alert alert-success'>✅ Sintaxis PHP correcta</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Error de sintaxis: <pre>$output</pre></div>";
        }
    } else {
        echo "<div class='alert alert-danger'>❌ Archivo reviews.php no encontrado</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error general: " . $e->getMessage() . "</div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
