<?php
/**
 * Horchata Mexican Food - Sistema de Checkout
 * Integración con PayPal
 */

// Iniciar sesión primero (necesario para el sistema de idiomas)
session_start();

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Verificar que hay productos en el carrito
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Si no hay productos, redirigir al menú
if (empty($cart_items)) {
    header('Location: menu.php');
    exit;
}

// Usar totales calculados en la sesión o calcular si no existen
$subtotal = $_SESSION['cart_subtotal'] ?? 0;
$tax = $_SESSION['cart_tax'] ?? 0;
$total = $_SESSION['cart_total'] ?? 0;

// Si no hay totales en sesión, calcular
if ($subtotal == 0) {
    $tax_rate = 0.0825; // 8.25% de impuestos
    $subtotal = 0;
    
    foreach ($cart_items as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $subtotal += $item_total;
    }
    
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax;
}

// Obtener configuraciones de PayPal
$paypal_enabled = getSetting('paypal_enabled', '0') === '1';
$paypal_client_id = getSetting('paypal_client_id', '');
$paypal_mode = getSetting('paypal_mode', 'sandbox');
$currency = getSetting('currency', 'USD');

// Obtener configuraciones de Wire Transfer
$wire_transfer_enabled = getSetting('wire_transfer_enabled', '0') === '1';
$wire_transfer_bank_name = getSetting('wire_transfer_bank_name', '');
$wire_transfer_account_holder = getSetting('wire_transfer_account_holder', '');
$wire_transfer_account_number = getSetting('wire_transfer_account_number', '');
$wire_transfer_routing_number = getSetting('wire_transfer_routing_number', '');
$wire_transfer_direct_deposit_routing_number = getSetting('wire_transfer_direct_deposit_routing_number', '');
$wire_transfer_swift_code = getSetting('wire_transfer_swift_code', '');
$wire_transfer_instructions = getSetting('wire_transfer_instructions', '');

// Configurar página
$page_title = __('finish_order');
$page_scripts = [
    'assets/js/checkout.js'
];

// Incluir header
include 'includes/header.php';
?>

<!-- PayPal SDK -->
<?php if ($paypal_enabled && !empty($paypal_client_id)): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypal_client_id); ?>&currency=<?php echo htmlspecialchars($currency); ?>"></script>
<?php endif; ?>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="checkout-form">
                    <h2 class="mb-4">
                        <i class="fas fa-shopping-cart me-2"></i><?php echo __('finish_order'); ?>
                    </h2>
                    
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i><?php echo __('customer_information'); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="customerForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label"><?php echo __('first_name'); ?> *</label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label"><?php echo __('last_name'); ?> *</label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label"><?php echo __('email_address'); ?> *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label"><?php echo __('phone_number'); ?> *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Pickup Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i><?php echo __('pickup_information'); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pickupDate" class="form-label"><?php echo __('pickup_date'); ?> *</label>
                                    <input type="date" class="form-control" id="pickupDate" name="pickup_date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pickupTime" class="form-label"><?php echo __('pickup_time'); ?> *</label>
                                    <select class="form-select" id="pickupTime" name="pickup_time" required>
                                        <option value=""><?php echo __('select_time'); ?></option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="11:30">11:30 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="12:30">12:30 PM</option>
                                        <option value="13:00">1:00 PM</option>
                                        <option value="13:30">1:30 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="14:30">2:30 PM</option>
                                        <option value="15:00">3:00 PM</option>
                                        <option value="15:30">3:30 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                        <option value="16:30">4:30 PM</option>
                                        <option value="17:00">5:00 PM</option>
                                        <option value="17:30">5:30 PM</option>
                                        <option value="18:00">6:00 PM</option>
                                        <option value="18:30">6:30 PM</option>
                                        <option value="19:00">7:00 PM</option>
                                        <option value="19:30">7:30 PM</option>
                                        <option value="20:00">8:00 PM</option>
                                        <option value="20:30">8:30 PM</option>
                                        <option value="21:00">9:00 PM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="specialInstructions" class="form-label"><?php echo __('special_instructions'); ?></label>
                                <textarea class="form-control" id="specialInstructions" name="special_instructions" rows="3" placeholder="<?php echo __('allergies_modifications'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i><?php echo __('payment_method'); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-methods">
                                <?php if ($paypal_enabled && !empty($paypal_client_id)): ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal me-2 text-primary"></i>PayPal
                                        <small class="text-muted d-block"><?php echo __('pay_safely_paypal'); ?></small>
                                    </label>
                                </div>
                                <?php endif; ?>
                                
                                <?php 
                                $wire_transfer_checked = '';
                                if (!$paypal_enabled || empty($paypal_client_id)) {
                                    if ($wire_transfer_enabled && !empty($wire_transfer_account_number)) {
                                        $wire_transfer_checked = 'checked';
                                    }
                                }
                                ?>
                                <?php if ($wire_transfer_enabled && !empty($wire_transfer_account_number)): ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="wire_transfer" value="wire_transfer" <?php echo $wire_transfer_checked; ?>>
                                    <label class="form-check-label" for="wire_transfer">
                                        <i class="fas fa-university me-2 text-info"></i><?php echo __('wire_transfer'); ?>
                                        <small class="text-muted d-block"><?php echo __('wire_transfer_description'); ?></small>
                                    </label>
                                </div>
                                <?php endif; ?>
                                
                                <div class="form-check">
                                    <?php 
                                    $pickup_checked = '';
                                    if (!$paypal_enabled || empty($paypal_client_id)) {
                                        if (!$wire_transfer_enabled || empty($wire_transfer_account_number)) {
                                            $pickup_checked = 'checked';
                                        }
                                    }
                                    ?>
                                    <input class="form-check-input" type="radio" name="payment_method" id="pickup_payment" value="pickup" <?php echo $pickup_checked; ?>>
                                    <label class="form-check-label" for="pickup_payment">
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i><?php echo __('pay_on_pickup'); ?>
                                        <small class="text-muted d-block"><?php echo __('cash_card_paypal_pickup'); ?></small>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Wire Transfer Information (mostrar cuando se selecciona) -->
                            <?php if ($wire_transfer_enabled && !empty($wire_transfer_account_number)): ?>
                            <div id="wireTransferInfo" class="wire-transfer-info mt-4" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-university me-2"></i><?php echo __('wire_transfer_information'); ?>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong><?php echo __('bank_name'); ?>:</strong>
                                                <p class="mb-0"><?php echo htmlspecialchars($wire_transfer_bank_name); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong><?php echo __('account_holder'); ?>:</strong>
                                                <p class="mb-0"><?php echo htmlspecialchars($wire_transfer_account_holder); ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong><?php echo __('account_number'); ?>:</strong>
                                                <p class="mb-0 font-monospace"><?php echo htmlspecialchars($wire_transfer_account_number); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong><?php echo __('routing_number_wire'); ?>:</strong>
                                                <p class="mb-0 font-monospace"><?php echo htmlspecialchars($wire_transfer_routing_number); ?></p>
                                            </div>
                                        </div>
                                        <?php if (!empty($wire_transfer_direct_deposit_routing_number)): ?>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong><?php echo __('routing_number_direct_deposit'); ?>:</strong>
                                                <p class="mb-0 font-monospace"><?php echo htmlspecialchars($wire_transfer_direct_deposit_routing_number); ?></p>
                                                <small class="text-muted"><?php echo __('for_direct_deposit'); ?></small>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($wire_transfer_swift_code)): ?>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong><?php echo __('swift_code'); ?>:</strong>
                                                <p class="mb-0 font-monospace"><?php echo htmlspecialchars($wire_transfer_swift_code); ?></p>
                                                <small class="text-muted"><?php echo __('for_international_wires'); ?></small>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($wire_transfer_instructions)): ?>
                                        <div class="mb-3">
                                            <strong><?php echo __('wire_transfer_instructions'); ?>:</strong>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($wire_transfer_instructions)); ?></p>
                                        </div>
                                        <?php endif; ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong><?php echo __('important'); ?>:</strong> <?php echo __('wire_transfer_note'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt me-2"></i><?php echo __('order_summary'); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div class="order-items mb-4">
                                <?php foreach ($cart_items as $item): ?>
                                <div class="order-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="item-info">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted"><?php echo __('quantity'); ?>: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <div class="item-price">
                                            <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                        </div>
                                    </div>
                                    
                                    <?php if (isset($item['customizations']['extras']) && !empty($item['customizations']['extras'])): ?>
                                    <div class="extras-info ms-3">
                                        <?php foreach ($item['customizations']['extras'] as $extra): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">+ <?php echo htmlspecialchars($extra['name']); ?></small>
                                            <small class="text-success">+$<?php echo number_format($extra['price'], 2); ?></small>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($item['customizations']['specialInstructions']) && !empty($item['customizations']['specialInstructions'])): ?>
                                    <div class="special-instructions ms-3">
                                        <small class="text-muted">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            <?php echo htmlspecialchars($item['customizations']['specialInstructions']); ?>
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Order Totals -->
                            <div class="order-totals">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo __('subtotal'); ?>:</span>
                                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo __('tax_percentage'); ?>:</span>
                                    <span>$<?php echo number_format($tax, 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong><?php echo __('total'); ?>:</strong>
                                    <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                                </div>
                            </div>
                            
                            <!-- PayPal Button -->
                            <div id="paypal-button-container" class="mb-3" style="display: none;">
                                <!-- PayPal button will be rendered here -->
                            </div>
                            
                            <!-- Place Order Button -->
                            <button class="btn btn-primary btn-lg w-100" id="placeOrderBtn">
                                <i class="fas fa-check me-2"></i><?php echo __('confirm_order'); ?>
                            </button>
                            
                            <!-- Security Notice -->
                            <div class="security-notice mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>
                                    <?php echo __('ssl_protected'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript específico para checkout -->
<script>
// Variables de PayPal desde PHP
const paypalEnabled = <?php echo $paypal_enabled && !empty($paypal_client_id) ? 'true' : 'false'; ?>;
const paypalMode = '<?php echo htmlspecialchars($paypal_mode); ?>';
const currency = '<?php echo htmlspecialchars($currency); ?>';

// Traducciones para JavaScript
const translations = {
    complete_all_fields: '<?php echo __('complete_all_fields'); ?>',
    paypal_error: '<?php echo __('error'); ?>: ',
    processing: '<?php echo __('processing'); ?>',
    confirm_order: '<?php echo __('confirm_order'); ?>',
    error_processing_order: '<?php echo __('error_processing_order'); ?>',
    connection_error: '<?php echo __('connection_error'); ?>'
};

// Esperar a que jQuery esté disponible
(function() {
    function initCheckout() {
        if (typeof jQuery === 'undefined') {
            // jQuery aún no está cargado, esperar un poco más
            setTimeout(initCheckout, 50);
            return;
        }
        
        // jQuery está disponible, ejecutar código
        jQuery(document).ready(function($) {
    // Configurar fecha mínima (hoy)
    const today = new Date().toISOString().split('T')[0];
    $('#pickupDate').attr('min', today);
    
    // Definir todas las funciones dentro de este contexto
    function setupPayPal() {
    // PayPal SDK se carga desde el header
    if (typeof paypal !== 'undefined') {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($total, 2, '.', ''); ?>',
                            currency_code: currency
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Procesar pedido con PayPal
                    processOrderWithPayPal(details);
                });
            },
            onError: function(err) {
                showNotification(translations.paypal_error + err.message, 'error');
            }
        }).render('#paypal-button-container');
    }
}

function setupCheckoutForm() {
    // Validar formulario antes de proceder
    $('#customerForm').on('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            const paymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (paymentMethod === 'paypal' && paypalEnabled) {
                // Mostrar botón de PayPal
                $('#paypal-button-container').show();
                $('#placeOrderBtn').hide();
            } else if (paymentMethod === 'wire_transfer') {
                // Mostrar información de Wire Transfer
                $('#wireTransferInfo').show();
                $('#placeOrderBtn').show();
            } else {
                // Procesar pedido sin PayPal
                processOrderWithoutPayPal();
            }
        }
    });
}

function setupPaymentMethod() {
    $('input[name="payment_method"]').on('change', function() {
        const paymentMethod = $(this).val();
        
        // Ocultar todos los paneles primero
        $('#paypal-button-container').hide();
        $('#wireTransferInfo').hide();
        
        if (paymentMethod === 'paypal' && paypalEnabled) {
            $('#paypal-button-container').show();
            $('#placeOrderBtn').hide();
        } else if (paymentMethod === 'wire_transfer') {
            $('#wireTransferInfo').show();
            $('#placeOrderBtn').show();
        } else {
            $('#placeOrderBtn').show();
        }
    });
    
    // Configurar estado inicial
    const selectedPayment = $('input[name="payment_method"]:checked').val();
    if (selectedPayment === 'paypal' && paypalEnabled) {
        $('#paypal-button-container').show();
        $('#placeOrderBtn').hide();
        $('#wireTransferInfo').hide();
    } else if (selectedPayment === 'wire_transfer') {
        $('#wireTransferInfo').show();
        $('#paypal-button-container').hide();
        $('#placeOrderBtn').show();
    } else {
        $('#paypal-button-container').hide();
        $('#wireTransferInfo').hide();
        $('#placeOrderBtn').show();
    }
}

function validateForm() {
    const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'pickupDate', 'pickupTime'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = $(`#${field}`);
        if (!input.val().trim()) {
            input.addClass('is-invalid');
            isValid = false;
        } else {
            input.removeClass('is-invalid');
        }
    });
    
    // Validar email
    const email = $('#email').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        $('#email').addClass('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        showNotification(translations.complete_all_fields, 'error');
    }
    
    return isValid;
}

function processOrderWithPayPal(paypalDetails) {
    const orderData = {
        customer: getCustomerData(),
        items: <?php echo json_encode($cart_items); ?>,
        totals: {
            subtotal: <?php echo $subtotal; ?>,
            tax: <?php echo $tax; ?>,
            total: <?php echo $total; ?>
        },
        payment: {
            method: 'paypal',
            transaction_id: paypalDetails.id,
            status: 'paid'
        }
    };
    
    submitOrder(orderData);
}

function processOrderWithoutPayPal() {
    const paymentMethod = $('input[name="payment_method"]:checked').val();
    
    let orderData;
    if (paymentMethod === 'wire_transfer') {
        orderData = {
            customer: getCustomerData(),
            items: <?php echo json_encode($cart_items); ?>,
            totals: {
                subtotal: <?php echo $subtotal; ?>,
                tax: <?php echo $tax; ?>,
                total: <?php echo $total; ?>
            },
            payment: {
                method: 'wire_transfer',
                status: 'pending'
            }
        };
    } else {
        orderData = {
            customer: getCustomerData(),
            items: <?php echo json_encode($cart_items); ?>,
            totals: {
                subtotal: <?php echo $subtotal; ?>,
                tax: <?php echo $tax; ?>,
                total: <?php echo $total; ?>
            },
            payment: {
                method: 'pickup',
                status: 'pending'
            }
        };
    }
    
    submitOrder(orderData);
}

function getCustomerData() {
    return {
        first_name: $('#firstName').val(),
        last_name: $('#lastName').val(),
        email: $('#email').val(),
        phone: $('#phone').val(),
        pickup_date: $('#pickupDate').val(),
        pickup_time: $('#pickupTime').val(),
        special_instructions: $('#specialInstructions').val()
    };
}

function submitOrder(orderData) {
    // Mostrar loading
    $('#placeOrderBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>' + translations.processing).prop('disabled', true);
    
    $.ajax({
        url: 'ajax/orders.ajax.php',
        method: 'POST',
        data: { action: 'create_order', order_data: orderData },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Limpiar carrito
                clearCart();
                
                // Redirigir a confirmación
                window.location.href = `order-success.php?order_id=${response.order_id}`;
            } else {
                showNotification(translations.error_processing_order + response.message, 'error');
                $('#placeOrderBtn').html('<i class="fas fa-check me-2"></i>' + translations.confirm_order).prop('disabled', false);
            }
        },
        error: function() {
            showNotification(translations.connection_error, 'error');
            $('#placeOrderBtn').html('<i class="fas fa-check me-2"></i>' + translations.confirm_order).prop('disabled', false);
        }
    });
}

function clearCart() {
    localStorage.removeItem('horchata_cart');
    sessionStorage.removeItem('horchata_cart');
}

function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 100px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}
    
    // Configurar PayPal solo si está habilitado
    if (paypalEnabled) {
        setupPayPal();
    }
    
    // Configurar formulario
    setupCheckoutForm();
    
    // Configurar método de pago
    setupPaymentMethod();
    
    // Configurar botón de confirmar pedido
    $('#placeOrderBtn').on('click', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            const paymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (paymentMethod === 'paypal' && paypalEnabled) {
                // No hacer nada, PayPal maneja su propio flujo
                return;
            } else {
                // Procesar pedido (wire_transfer o pickup)
                processOrderWithoutPayPal();
            }
        }
    });
        }); // Cerrar jQuery(document).ready
    } // Cerrar initCheckout
    
    // Iniciar la función
    initCheckout();
})(); // Cerrar IIFE
</script>

<!-- Estilos adicionales para checkout -->
<style>
.checkout-form .card {
    border: none;
    box-shadow: var(--shadow);
    border-radius: 15px;
}

.checkout-form .card-header {
    background: linear-gradient(135deg, var(--primary-color), #f4d03f);
    color: white;
    border-radius: 15px 15px 0 0 !important;
}

.order-summary .card {
    position: sticky;
    top: 100px;
    border: none;
    box-shadow: var(--shadow);
    border-radius: 15px;
}

.order-item {
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.order-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.payment-methods .form-check {
    padding: 15px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.payment-methods .form-check:hover {
    border-color: var(--primary-color);
    background: rgba(212, 175, 55, 0.05);
}

.payment-methods .form-check-input:checked + .form-check-label {
    color: var(--primary-color);
}

.payment-methods .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.security-notice {
    text-align: center;
    padding: 10px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

@media (max-width: 768px) {
    .order-summary .card {
        position: relative;
        top: 0;
    }
}
</style>

<?php
// Incluir footer
include 'includes/footer.php';
?>
