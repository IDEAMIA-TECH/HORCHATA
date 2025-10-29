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
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('payment_method'); ?></h6>
                                <p class="payment-method">
                                    <?php if ($order['payment_method'] === 'paypal'): ?>
                                        <i class="fab fa-paypal me-2 text-primary"></i>PayPal
                                    <?php else: ?>
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i><?php echo __('pay_on_pickup'); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('payment_status'); ?></h6>
                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?> payment-status">
                                    <?php echo ucfirst($order['payment_status']); ?>
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
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('phone'); ?></h6>
                                <p><i class="fas fa-phone me-2"></i>(555) 123-4567</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><?php echo __('email'); ?></h6>
                                <p><i class="fas fa-envelope me-2"></i>orders@horchatamexicanfood.com</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-muted"><?php echo __('address'); ?></h6>
                                <p><i class="fas fa-map-marker-alt me-2"></i>123 Main Street, Ciudad, Estado</p>
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
    
    const orderUrl = 'https://ideamia-dev.com/HORCHATA/admin/qr-scanner.php?order_id=<?php echo $order_id; ?>';
    
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
