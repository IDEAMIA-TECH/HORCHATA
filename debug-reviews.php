<?php
/**
 * Debug Reviews - Diagnóstico simple de reviews.php
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<h1>Debug Reviews</h1>";

try {
    // Verificar conexión
    echo "<p>✅ Conexión a BD: OK</p>";
    
    // Verificar función fetchOne
    if (function_exists('fetchOne')) {
        echo "<p>✅ Función fetchOne: OK</p>";
    } else {
        echo "<p>❌ Función fetchOne: NO ENCONTRADA</p>";
    }
    
    // Verificar si hay órdenes con tokens
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE review_token IS NOT NULL AND review_token != ''");
    $result = $stmt->fetch();
    echo "<p>📊 Órdenes con tokens: " . $result['total'] . "</p>";
    
    if ($result['total'] == 0) {
        echo "<p>⚠️ No hay órdenes con tokens. Creando orden de prueba...</p>";
        
        // Crear orden de prueba
        $token = 'test-token-' . time();
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                order_number, customer_name, customer_email, 
                review_token, status, subtotal, tax, total, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            'TEST' . time(),
            'Cliente Prueba',
            'test@example.com',
            $token,
            'completed',
            11.99,
            0.99,
            12.98
        ]);
        
        if ($result) {
            echo "<p>✅ Orden de prueba creada</p>";
            echo "<p>🔗 <a href='reviews.php?token=$token'>Probar reviews.php</a></p>";
        } else {
            echo "<p>❌ Error creando orden de prueba</p>";
        }
    } else {
        // Mostrar primera orden con token
        $stmt = $pdo->query("SELECT review_token FROM orders WHERE review_token IS NOT NULL AND review_token != '' LIMIT 1");
        $order = $stmt->fetch();
        if ($order) {
            echo "<p>🔗 <a href='reviews.php?token=" . $order['review_token'] . "'>Probar reviews.php</a></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
