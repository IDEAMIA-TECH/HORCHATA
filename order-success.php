<?php
/**
 * Horchata Mexican Food - Confirmación de Pedido
 * Página de éxito después del checkout
 */

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Obtener ID de la orden
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

// Obtener información de la orden (asegurarse de incluir campos de pago)
$order = fetchOne("
    SELECT o.*, 
           o.payment_method,
           o.payment_status,
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?
    GROUP BY o.id
", [$order_id]);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Obtener items de la orden
$order_items = fetchAll("
    SELECT oi.*, p.image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
", [$order_id]);

// Configurar página
$page_title = __('order_confirmed');
$page_scripts = ['assets/js/order-success.js'];

// Incluir header
include 'includes/header.php';
?>

<!-- Success Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Header -->
                <div class="text-center mb-5">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h1 class="text-success mb-3"><?php echo __('order_confirmed'); ?>!</h1>
                    <p class="lead text-muted">
                        <?php echo __('order_confirmed_message'); ?>
                    </p>
                    
                    <!-- QR Code for Restaurant -->
                    <div class="qr-code-section mt-4">
                        <div class="card shadow-sm border-0" style="max-width: 300px; margin: 0 auto;">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-qrcode me-2"></i><?php echo __('qr_code_for_restaurant'); ?>
                                </h6>
                                <div id="qrcode" class="mb-3"></div>
                                <p class="small text-muted mb-0">
                                    <?php echo __('scan_qr_to_view_order'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Details Card -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i><?php echo __('order_details'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('order_number'); ?></h6>
                                <p class="h5 text-primary order-number"><?php echo htmlspecialchars($order['order_number']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('order_date'); ?></h6>
                                <p class="order-date"><?php echo date('M d, Y \a \l\a\s g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('customer'); ?></h6>
                                <p class="customer-name"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('email'); ?></h6>
                                <p class="customer-email"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="text-muted">Pickup</h6>
                                <p class="pickup-date">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('M d, Y', strtotime($order['pickup_time'])); ?>
                                    <br>
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('g:i A', strtotime($order['pickup_time'])); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('status'); ?></h6>
                                <span class="badge bg-warning fs-6 order-status">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-utensils me-2"></i><?php echo __('order_items'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order_items as $item): ?>
                        <div class="order-item d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="item-image me-3">
                                <img src="<?php echo $item['image'] ? str_replace('../', '', $item['image']) : 'assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                     class="rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="item-details flex-grow-1">
                                <h6 class="mb-1 item-name"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                <small class="text-muted item-quantity"><?php echo __('quantity'); ?>: <?php echo $item['quantity']; ?></small>
                            </div>
                            <div class="item-price">
                                <strong class="item-price">$<?php echo number_format($item['subtotal'], 2); ?></strong>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Order Totals -->
                        <div class="order-totals mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo __('subtotal'); ?>:</span>
                                <span class="subtotal">$<?php echo number_format($order['subtotal'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo __('tax'); ?>:</span>
                                <span class="tax">$<?php echo number_format($order['tax'], 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong><?php echo __('total'); ?>:</strong>
                                <strong class="text-primary total">$<?php echo number_format($order['total'], 2); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i><?php echo __('payment_information'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Obtener valores de pago de la orden
                        $payment_method_raw = isset($order['payment_method']) ? $order['payment_method'] : null;
                        $payment_status_raw = isset($order['payment_status']) ? $order['payment_status'] : null;
                        
                        // Debug: Mostrar valores en la página (habilitar con ?debug=1 en la URL)
                        $show_debug = isset($_GET['debug']) && $_GET['debug'] === '1';
                        if ($show_debug) {
                            error_log("DEBUG order-success order_id={$order_id}: payment_method = " . var_export($payment_method_raw, true));
                            error_log("DEBUG order-success order_id={$order_id}: payment_status = " . var_export($payment_status_raw, true));
                            error_log("DEBUG order-success order_id={$order_id}: order data = " . json_encode($order));
                        }
                        
                        // Normalizar método de pago - manejar diferentes formatos
                        $payment_method_value = $payment_method_raw ? trim($payment_method_raw) : '';
                        $payment_method_lower = strtolower($payment_method_value);
                        // Remover espacios y guiones bajos
                        $payment_method_clean = str_replace([' ', '_', '-'], '', $payment_method_lower);
                        
                        // Normalizar estado de pago
                        $payment_status_value = $payment_status_raw ? trim($payment_status_raw) : '';
                        $payment_status_lower = strtolower($payment_status_value);
                        
                        // Inicializar variables de visualización
                        $display_method = '';
                        $display_status = '';
                        $status_badge = 'warning';
                        $status_icon = '<i class="fas fa-clock me-1"></i>';
                        
                        // Determinar método de pago - verificar múltiples variaciones posibles
                        // Orden importante: PayPal, Wire Transfer, Pickup, luego fallback
                        
                        // Debug temporal: mostrar valores si están vacíos o no coinciden
                        $debug_info = '';
                        if (isset($_GET['debug']) && $_GET['debug'] === '1') {
                            $debug_info = '<!-- DEBUG: payment_method_raw=' . htmlspecialchars(var_export($payment_method_raw, true)) . 
                                         ' | payment_method_clean=' . htmlspecialchars($payment_method_clean) . 
                                         ' | payment_status=' . htmlspecialchars($payment_status_lower) . ' -->';
                        }
                        
                        // 1. PayPal - verificar PRIMERO (puede tener variaciones)
                        if ($payment_method_clean === 'paypal' || strpos($payment_method_clean, 'paypal') !== false) {
                            // PayPal - verificar diferentes variaciones
                            $display_method = '<i class="fab fa-paypal me-2 text-primary"></i>' . __('paypal');
                            // PayPal siempre se marca como pagado
                            $display_status = __('paid');
                            $status_badge = 'success';
                            $status_icon = '<i class="fas fa-check-circle me-1"></i>';
                        }
                        // 2. Wire Transfer - verificar SEGUNDO (antes que pickup para evitar conflictos)
                        elseif ($payment_method_clean === 'wiretransfer' || 
                                $payment_method_lower === 'wire_transfer' ||
                                $payment_method_lower === 'wire-transfer' ||
                                $payment_method_lower === 'wire transfer' ||
                                strpos($payment_method_clean, 'wire') !== false || 
                                strpos($payment_method_clean, 'transfer') !== false ||
                                (strpos($payment_method_lower, 'wire') !== false && strpos($payment_method_lower, 'transfer') !== false)) {
                            // Wire Transfer
                            $display_method = '<i class="fas fa-university me-2 text-info"></i>' . __('wire_transfer');
                            $display_status = __('pending');
                            $status_badge = 'warning';
                            $status_icon = '<i class="fas fa-clock me-1"></i>';
                        }
                        // 3. Pickup - verificar TERCERO
                        elseif ($payment_method_clean === 'pickup' || strpos($payment_method_clean, 'pickup') !== false) {
                            // Pickup
                            $display_method = '<i class="fas fa-money-bill-wave me-2 text-success"></i>' . __('pay_on_pickup');
                            $display_status = __('pending');
                            $status_badge = 'warning';
                            $status_icon = '<i class="fas fa-clock me-1"></i>';
                        }
                        // 4. Si está vacío, intentar determinar por estado y otros indicadores
                        elseif (empty($payment_method_clean)) {
                            // Si no hay método, intentar determinar por el estado de pago
                            if ($payment_status_lower === 'paid') {
                                // Si está pagado pero no hay método, probablemente es PayPal
                                $display_method = '<i class="fab fa-paypal me-2 text-primary"></i>' . __('paypal');
                                $display_status = __('paid');
                                $status_badge = 'success';
                                $status_icon = '<i class="fas fa-check-circle me-1"></i>';
                            } else {
                                // Si no está pagado y no hay método, por defecto mostrar pickup
                                // PERO si el usuario confirma que es Wire Transfer, se puede actualizar manualmente
                                // Por ahora, mostramos un mensaje genérico
                                $display_method = '<i class="fas fa-money-bill-wave me-2 text-success"></i>' . __('pay_on_pickup');
                                $display_status = __('pending');
                            }
                        }
                        // 5. Método desconocido - mostrar el valor original o pickup
                        else {
                            // Método desconocido - intentar mostrar el valor original, si no hay mostrar pickup
                            if (!empty($payment_method_value)) {
                                $display_method = htmlspecialchars($payment_method_value);
                            } else {
                                $display_method = '<i class="fas fa-money-bill-wave me-2 text-success"></i>' . __('pay_on_pickup');
                            }
                            $display_status = __('pending');
                            $status_badge = 'warning';
                            $status_icon = '<i class="fas fa-clock me-1"></i>';
                        }
                        
                        // Verificar si hay un estado de pago específico que sobrescriba el predeterminado
                        if (!empty($payment_status_lower)) {
                            if ($payment_status_lower === 'paid') {
                                $display_status = __('paid');
                                $status_badge = 'success';
                                $status_icon = '<i class="fas fa-check-circle me-1"></i>';
                            } elseif ($payment_status_lower === 'pending' || $payment_status_lower === 'unpaid') {
                                $display_status = __('pending');
                                $status_badge = 'warning';
                                $status_icon = '<i class="fas fa-clock me-1"></i>';
                            }
                        }
                        ?>
                        <?php if (!empty($debug_info)) echo $debug_info; ?>
                        
                        <?php if ($show_debug): ?>
                        <!-- DEBUG INFO - Temporal -->
                        <div class="alert alert-warning mt-3 mb-3">
                            <strong>DEBUG INFO (order_id=<?php echo $order_id; ?>):</strong><br>
                            <strong>payment_method (raw):</strong> <?php echo htmlspecialchars(var_export($payment_method_raw, true)); ?><br>
                            <strong>payment_method (value):</strong> "<?php echo htmlspecialchars($payment_method_value); ?>"<br>
                            <strong>payment_method (lower):</strong> "<?php echo htmlspecialchars($payment_method_lower); ?>"<br>
                            <strong>payment_method (clean):</strong> "<?php echo htmlspecialchars($payment_method_clean); ?>"<br>
                            <strong>payment_status (raw):</strong> <?php echo htmlspecialchars(var_export($payment_status_raw, true)); ?><br>
                            <strong>payment_status (lower):</strong> "<?php echo htmlspecialchars($payment_status_lower); ?>"<br>
                            <strong>Display Method:</strong> <?php echo htmlspecialchars($display_method); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('payment_method'); ?></h6>
                                <p class="payment-method">
                                    <?php echo $display_method; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('payment_status'); ?></h6>
                                <span class="badge bg-<?php echo $status_badge; ?> payment-status">
                                    <?php echo $status_icon . $display_status; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Special Instructions -->
                <?php if (!empty($order['notes'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Instrucciones Especiales
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="special-instructions"><?php echo htmlspecialchars($order['notes']); ?></p>
                    </div>
                </div>
                <?php else: ?>
                <div class="card mb-4" style="display: none;">
                    <div class="card-body">
                        <p class="special-instructions">Ninguna</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="text-center mb-5">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="index.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-home me-2"></i><?php echo __('back_to_home'); ?>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="menu.php" class="btn btn-outline-success btn-lg w-100">
                                <i class="fas fa-utensils me-2"></i><?php echo __('view_menu'); ?>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-primary btn-lg w-100" onclick="printOrder()">
                                <i class="fas fa-print me-2"></i><?php echo __('print_order'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Review Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i><?php echo __('leave_review'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            <?php echo __('leave_review_message'); ?>
                        </p>
                        <a href="reviews-public.php?token=<?php echo $order['review_token'] ?? ''; ?>" 
                           class="btn btn-warning" target="_blank">
                            <i class="fas fa-star me-2"></i><?php echo __('write_review'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-phone me-2"></i><?php echo __('contact_information'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Obtener información de contacto del restaurante desde settings
                        $restaurant_phone = getSetting('restaurant_phone', '+1 (310) 204-2659');
                        $restaurant_email = getSetting('restaurant_email', 'contact@horchatamexicanfood.com');
                        $restaurant_address = getSetting('restaurant_address', '10814 Jefferson Blvd, Culver City, CA 90232');
                        $restaurant_tel = 'tel:' . preg_replace('/[^\d+]/', '', $restaurant_phone);
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('phone'); ?></h6>
                                <p>
                                    <i class="fas fa-phone me-2"></i>
                                    <a href="<?php echo $restaurant_tel; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($restaurant_phone); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('email'); ?></h6>
                                <p>
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($restaurant_email); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($restaurant_email); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-muted"><?php echo __('address'); ?></h6>
                                <p>
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($restaurant_address); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estilos adicionales para la página de éxito -->
<style>
.success-icon {
    animation: bounceIn 0.6s ease-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.order-item {
    transition: all 0.3s ease;
}

.order-item:hover {
    background: rgba(212, 175, 55, 0.05);
    border-radius: 8px;
    padding: 15px;
    margin: -15px;
}

.card {
    border: none;
    box-shadow: var(--shadow);
    border-radius: 15px;
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), #f4d37f);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    font-weight: 600;
}

.order-totals {
    background: rgba(212, 175, 55, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
}

#qrcode {
    display: inline-block;
    padding: 10px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.qr-code-section {
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 12px 20px;
    }
    
    #qrcode {
        transform: scale(0.85);
    }
}
</style>

<?php
// Incluir footer
include 'includes/footer.php';
?>

<!-- JavaScript para QR Code -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>
<script>
// Generar QR Code con la URL de la orden en el panel admin
$(document).ready(function() {
    console.log('Generando QR Code...');
    
    const orderUrl = 'https://horchatamexfood.com/admin/qr-scanner.php?order_id=<?php echo $order_id; ?>';
    
    // Generar QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: orderUrl,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    console.log('QR Code generado con URL:', orderUrl);
});
</script>
