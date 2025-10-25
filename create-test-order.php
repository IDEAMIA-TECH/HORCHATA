<?php
/**
 * Create Test Order - Crear orden de prueba con token de reseña
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Crear Orden de Prueba</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Crear Orden de Prueba</h1>
        <hr>
";

try {
    $pdo = getDbConnection();
    
    // Generar token único
    $token = 'test-token-' . uniqid();
    
    // Crear orden de prueba
    $stmt = $pdo->prepare("
        INSERT INTO orders (
            order_number, 
            customer_name, 
            customer_email, 
            customer_phone,
            pickup_time,
            payment_method,
            payment_status,
            status,
            subtotal,
            tax,
            total,
            review_token,
            notes,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $orderNumber = 'TEST' . date('YmdHis');
    $customerName = 'Cliente Prueba';
    $customerEmail = 'test@example.com';
    $customerPhone = '555-123-4567';
    $pickupTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $paymentMethod = 'pickup';
    $paymentStatus = 'pending';
    $status = 'completed';
    $subtotal = 11.99;
    $tax = 0.99;
    $total = 12.98;
    $notes = 'Orden de prueba para testing';
    
    $result = $stmt->execute([
        $orderNumber,
        $customerName,
        $customerEmail,
        $customerPhone,
        $pickupTime,
        $paymentMethod,
        $paymentStatus,
        $status,
        $subtotal,
        $tax,
        $total,
        $token,
        $notes
    ]);
    
    if ($result) {
        $orderId = $pdo->lastInsertId();
        
        // Crear item de prueba
        $stmt = $pdo->prepare("
            INSERT INTO order_items (
                order_id,
                product_id,
                product_name,
                quantity,
                price,
                subtotal
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $orderId,
            1, // ID de producto
            'Huevos Rancheros Plate',
            1,
            11.99,
            11.99
        ]);
        
        echo "<div class='alert alert-success'>✅ Orden de prueba creada exitosamente</div>";
        echo "<div class='card mt-4'>";
        echo "<div class='card-header'><h5>Detalles de la Orden</h5></div>";
        echo "<div class='card-body'>";
        echo "<p><strong>ID de Orden:</strong> $orderId</p>";
        echo "<p><strong>Número de Orden:</strong> $orderNumber</p>";
        echo "<p><strong>Cliente:</strong> $customerName</p>";
        echo "<p><strong>Email:</strong> $customerEmail</p>";
        echo "<p><strong>Token de Reseña:</strong> <code>$token</code></p>";
        echo "<p><strong>Estado:</strong> $status</p>";
        echo "<p><strong>Total:</strong> $" . number_format($total, 2) . "</p>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='mt-4'>";
        echo "<h4>Enlaces de Prueba:</h4>";
        echo "<div class='list-group'>";
        echo "<a href='reviews.php?token=$token' class='list-group-item list-group-item-action' target='_blank'>";
        echo "<i class='fas fa-star me-2'></i>Ir a Página de Reseñas";
        echo "</a>";
        echo "<a href='test-reviews.php' class='list-group-item list-group-item-action'>";
        echo "<i class='fas fa-bug me-2'></i>Diagnóstico de Reviews";
        echo "</a>";
        echo "</div>";
        echo "</div>";
        
    } else {
        echo "<div class='alert alert-danger'>❌ Error al crear orden de prueba</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
