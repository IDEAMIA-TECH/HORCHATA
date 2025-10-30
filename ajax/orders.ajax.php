<?php
/**
 * AJAX Endpoint para Órdenes
 * Horchata Mexican Food - Sistema de Checkout
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexión a BD
require_once '../includes/db_connect.php';
require_once '../includes/init.php';

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_order':
            createOrder();
            break;
        case 'get_order':
            getOrder();
            break;
        case 'update_order_status':
            updateOrderStatus();
            break;
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en orders.ajax.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
}

/**
 * Crear nueva orden
 */
function createOrder() {
    global $pdo;
    
    $order_data_raw = $_POST['order_data'] ?? '';
    
    if (empty($order_data_raw)) {
        error_log("orders.ajax.php: order_data está vacío o no existe");
        throw new Exception('Datos de orden no recibidos');
    }
    
    $order_data = json_decode($order_data_raw, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("orders.ajax.php: Error JSON decode: " . json_last_error_msg());
        error_log("orders.ajax.php: order_data_raw = " . substr($order_data_raw, 0, 200));
        throw new Exception('Datos de orden no válidos: ' . json_last_error_msg());
    }
    
    if (!$order_data) {
        throw new Exception('Datos de orden no válidos');
    }
    
    // Validar datos requeridos
    $required_fields = ['customer', 'items', 'totals', 'payment'];
    foreach ($required_fields as $field) {
        if (!isset($order_data[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }
    
    // Generar número de orden único
    $order_number = generateOrderNumber();
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // Insertar orden principal
        $order_sql = "INSERT INTO orders (
            order_number, customer_name, customer_email, customer_phone,
            pickup_time, status, payment_method, payment_status,
            subtotal, tax, total, notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $customer = $order_data['customer'];
        $totals = $order_data['totals'];
        $payment = $order_data['payment'];
        
        // Validar y obtener método de pago
        $payment_method = isset($payment['method']) ? trim($payment['method']) : 'pickup';
        if (empty($payment_method)) {
            $payment_method = 'pickup'; // Default si está vacío
        }
        
        // Validar y obtener estado de pago
        $payment_status = isset($payment['status']) ? trim($payment['status']) : 'pending';
        if (empty($payment_status)) {
            // Si el método es PayPal, asumir que está pagado
            if (strtolower($payment_method) === 'paypal') {
                $payment_status = 'paid';
            } else {
                $payment_status = 'pending';
            }
        }
        
        // Log para debug (temporal)
        error_log("Creating order - payment_method: " . $payment_method . ", payment_status: " . $payment_status);
        
        // Crear datetime para pickup
        $pickup_datetime = $customer['pickup_date'] . ' ' . $customer['pickup_time'] . ':00';
        
        $order_params = [
            $order_number,
            $customer['first_name'] . ' ' . $customer['last_name'],
            $customer['email'],
            $customer['phone'],
            $pickup_datetime,
            'pending',
            $payment_method,
            $payment_status,
            $totals['subtotal'],
            $totals['tax'],
            $totals['total'],
            $customer['special_instructions'] ?? ''
        ];
        
        $order_stmt = executeQuery($order_sql, $order_params);
        if ($order_stmt === false) {
            throw new Exception('Error al insertar la orden');
        }
        $order_id = $pdo->lastInsertId();
        
        // Insertar items de la orden
        foreach ($order_data['items'] as $item) {
            $item_sql = "INSERT INTO order_items (
                order_id, product_id, product_name, product_price, quantity, subtotal, customizations
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $item_subtotal = $item['price'] * $item['quantity'];
            
            // Convertir personalizaciones a JSON si existen
            $customizations = null;
            if (isset($item['customizations']) && !empty($item['customizations'])) {
                $customizations = json_encode($item['customizations']);
            }
            
            $item_params = [
                $order_id,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $item_subtotal,
                $customizations
            ];
            
            $item_stmt = executeQuery($item_sql, $item_params);
            if ($item_stmt === false) {
                throw new Exception('Error al insertar item de la orden: ' . ($item['name'] ?? 'desconocido'));
            }
        }
        
        // Si es pago con PayPal, guardar información de transacción
        if ($payment['method'] === 'paypal' && isset($payment['transaction_id'])) {
            // Aquí podrías guardar información adicional de PayPal si es necesario
            // Por ejemplo, en una tabla de transacciones
        }
        
        // Generar token para reseña
        $review_token = bin2hex(random_bytes(32));
        
        // Actualizar la orden con el review_token
        $update_token_sql = "UPDATE orders SET review_token = ? WHERE id = ?";
        $token_stmt = executeQuery($update_token_sql, [$review_token, $order_id]);
        if ($token_stmt === false) {
            throw new Exception('Error al actualizar el token de revisión');
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Enviar email de confirmación (opcional)
        sendOrderConfirmationEmail($customer['email'], $order_number, $order_data);
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'order_number' => $order_number,
            'review_token' => $review_token,
            'message' => 'Orden creada exitosamente'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Obtener información de una orden
 */
function getOrder() {
    $order_id = $_GET['order_id'] ?? 0;
    
    if (!$order_id) {
        throw new Exception('ID de orden requerido');
    }
    
    // Obtener información de la orden
    $order = fetchOne("
        SELECT o.*, 
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.id = ?
        GROUP BY o.id
    ", [$order_id]);
    
    if (!$order) {
        throw new Exception('Orden no encontrada');
    }
    
    // Obtener items de la orden
    $items = fetchAll("
        SELECT oi.*, p.image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ", [$order_id]);
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);
}

/**
 * Actualizar estado de una orden
 */
function updateOrderStatus() {
    $order_id = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!$order_id || !$status) {
        throw new Exception('ID de orden y estado requeridos');
    }
    
    $valid_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Estado no válido');
    }
    
    $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    executeQuery($sql, [$status, $order_id]);
    
    // Enviar correo de actualización de estado
    try {
        sendOrderStatusEmail((int)$order_id, $status);
    } catch (Exception $e) {
        error_log('sendOrderStatusEmail error: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Estado actualizado exitosamente'
    ]);
}

/**
 * Generar número de orden único
 */
function generateOrderNumber() {
    $prefix = 'HOR';
    $date = date('Ymd');
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    return $prefix . $date . $random;
}

/**
 * Generar token para reseña
 */
function generateReviewToken($order_id) {
    $token = bin2hex(random_bytes(32));
    
    // Guardar token en la base de datos
    $sql = "INSERT INTO review_tokens (order_id, token, expires_at) VALUES (?, ?, ?)";
    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    executeQuery($sql, [$order_id, $token, $expires_at]);
    
    return $token;
}

/**
 * Enviar email de confirmación
 */
function sendOrderConfirmationEmail($email, $order_number, $order_data) {
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $fromEmail = getSetting('email_from', 'orders@horchatamexicanfood.com');
    $fromName = getSetting('email_from_name', 'Horchata Mexican Food');
    $logoUrl = $siteUrl . '/assets/images/LOGO.JPG';
    
    // Construir HTML elegante con productos
    $itemsHtml = '';
    foreach ($order_data['items'] as $it) {
        $img = isset($it['image']) && $it['image'] ? $it['image'] : '';
        if ($img && str_starts_with($img, '../')) { $img = substr($img, 3); }
        if ($img && !str_starts_with($img, 'http')) { $img = rtrim($siteUrl, '/') . '/' . ltrim($img, '/'); }
        $itemsHtml .= '<tr>' .
            '<td style="padding:10px; border-bottom:1px solid #eee;"><img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($it['name']) . '" style="width:70px;height:70px;object-fit:cover;border-radius:8px;vertical-align:middle;margin-right:10px;">' .
            '<strong>' . htmlspecialchars($it['name']) . '</strong><br><small>Cant: ' . (int)$it['quantity'] . '</small></td>' .
            '<td style="padding:10px; border-bottom:1px solid #eee; text-align:right;">$' . number_format($it['price'] * $it['quantity'], 2) . '</td>' .
        '</tr>';
    }
    
    $subtotal = $order_data['totals']['subtotal'] ?? 0;
    $tax = $order_data['totals']['tax'] ?? 0;
    $total = $order_data['totals']['total'] ?? 0;
    $pickupDate = $order_data['customer']['pickup_date'] ?? '';
    $pickupTime = $order_data['customer']['pickup_time'] ?? '';
    
    $subject = 'Order Confirmation #' . $order_number . ' - Horchata Mexican Food';
    $html = '<div style="font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;padding:20px;">' .
        '<table style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);" cellpadding="0" cellspacing="0" width="100%">' .
            '<tr><td style="background:linear-gradient(135deg,#111,#333);padding:24px;text-align:center;">' .
                '<img src="' . htmlspecialchars($logoUrl) . '" alt="Horchata Mexican Food" style="max-width:180px;border-radius:10px;background:#fff;padding:8px;box-shadow:0 4px 20px rgba(212,175,55,0.35);border:2px solid rgba(212,175,55,0.25);">' .
                '<h2 style="color:#fff;margin:16px 0 0;font-weight:700;">Order Confirmation</h2>' .
                '<p style="color:#ddd;margin:8px 0 0;">Thank you for your order!</p>' .
            '</td></tr>' .
            '<tr><td style="padding:24px;">' .
                '<p style="margin:0 0 8px;color:#333;">Order Number: <strong>#' . htmlspecialchars($order_number) . '</strong></p>' .
                '<p style="margin:0 0 16px;color:#333;">Pickup: <strong>' . htmlspecialchars($pickupDate . ' ' . $pickupTime) . '</strong></p>' .
                '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">' .
                    $itemsHtml .
                    '<tr><td style="padding:10px;text-align:right;color:#555;">Subtotal</td><td style="padding:10px;text-align:right;">$' . number_format($subtotal,2) . '</td></tr>' .
                    '<tr><td style="padding:10px;text-align:right;color:#555;">Tax</td><td style="padding:10px;text-align:right;">$' . number_format($tax,2) . '</td></tr>' .
                    '<tr><td style="padding:10px;text-align:right;color:#111;font-weight:700;">Total</td><td style="padding:10px;text-align:right;color:#111;font-weight:700;">$' . number_format($total,2) . '</td></tr>' .
                '</table>' .
                '<div style="margin-top:20px;text-align:center;">' .
                    '<a href="' . htmlspecialchars($siteUrl . '/order-success.php?order_number=' . urlencode($order_number)) . '" style="display:inline-block;background:#d4af37;color:#111;text-decoration:none;font-weight:700;padding:12px 20px;border-radius:8px;">View Order</a>' .
                '</div>' .
            '</td></tr>' .
            '<tr><td style="background:#fafafa;padding:16px;text-align:center;color:#777;font-size:12px;">' .
                'Horchata Mexican Food &middot; This is an automated message.' .
            '</td></tr>' .
        '</table>' .
    '</div>';
    
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    @mail($email, $subject, $html, implode("\r\n", $headers));
}

function sendOrderStatusEmail(int $orderId, string $newStatus) {
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $fromEmail = getSetting('email_from', 'orders@horchatamexicanfood.com');
    $fromName = getSetting('email_from_name', 'Horchata Mexican Food');
    $logoUrl = $siteUrl . '/assets/images/LOGO.JPG';
    
    // Obtener orden + items + imágenes
    $order = fetchOne("SELECT * FROM orders WHERE id = ?", [$orderId]);
    if (!$order) { throw new Exception('Order not found'); }
    $items = fetchAll("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$orderId]) ?: [];
    
    $itemsHtml = '';
    foreach ($items as $it) {
        $img = $it['image'] ?? '';
        if ($img && str_starts_with($img, '../')) { $img = substr($img, 3); }
        if ($img && !str_starts_with($img, 'http')) { $img = rtrim($siteUrl, '/') . '/' . ltrim($img, '/'); }
        $itemsHtml .= '<tr>' .
            '<td style="padding:10px; border-bottom:1px solid #eee;"><img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($it['product_name']) . '" style="width:70px;height:70px;object-fit:cover;border-radius:8px;vertical-align:middle;margin-right:10px;">' .
            '<strong>' . htmlspecialchars($it['product_name']) . '</strong><br><small>Cant: ' . (int)$it['quantity'] . '</small></td>' .
            '<td style="padding:10px; border-bottom:1px solid #eee; text-align:right;">$' . number_format($it['subtotal'], 2) . '</td>' .
        '</tr>';
    }
    
    $subject = 'Your order #' . $order['order_number'] . ' is now ' . ucfirst($newStatus);
    $html = '<div style="font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;padding:20px;">' .
        '<table style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);" cellpadding="0" cellspacing="0" width="100%">' .
            '<tr><td style="background:linear-gradient(135deg,#111,#333);padding:24px;text-align:center;">' .
                '<img src="' . htmlspecialchars($logoUrl) . '" alt="Horchata Mexican Food" style="max-width:180px;border-radius:10px;background:#fff;padding:8px;box-shadow:0 4px 20px rgba(212,175,55,0.35);border:2px solid rgba(212,175,55,0.25);">' .
                '<h2 style="color:#fff;margin:16px 0 0;font-weight:700;">Order Update</h2>' .
                '<p style="color:#ddd;margin:8px 0 0;">Status: <strong style="color:#d4af37;">' . htmlspecialchars(ucfirst($newStatus)) . '</strong></p>' .
            '</td></tr>' .
            '<tr><td style="padding:24px;">' .
                '<p style="margin:0 0 12px;color:#333;">Order Number: <strong>#' . htmlspecialchars($order['order_number']) . '</strong></p>' .
                '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">' .
                    $itemsHtml .
                '</table>' .
                '<div style="margin-top:20px;text-align:center;">' .
                    '<a href="' . htmlspecialchars($siteUrl . '/order-success.php?order_id=' . urlencode((string)$orderId)) . '" style="display:inline-block;background:#d4af37;color:#111;text-decoration:none;font-weight:700;padding:12px 20px;border-radius:8px;">View Order</a>' .
                '</div>' .
            '</td></tr>' .
            '<tr><td style="background:#fafafa;padding:16px;text-align:center;color:#777;font-size:12px;">' .
                'Horchata Mexican Food &middot; This is an automated message.' .
            '</td></tr>' .
        '</table>' .
    '</div>';
    
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    @mail($order['customer_email'], $subject, $html, implode("\r\n", $headers));
}
?>
