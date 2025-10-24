<?php
/**
 * Horchata Mexican Food - Sistema de Checkout
 * Integración con PayPal
 */

// Incluir configuración
require_once 'includes/db_connect.php';

// Verificar que hay productos en el carrito
session_start();
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Si no hay productos, redirigir al menú
if (empty($cart_items)) {
    header('Location: menu.php');
    exit;
}

// Calcular totales
$subtotal = 0;
$tax_rate = 0.0825; // 8.25% de impuestos
$tax = 0;
$total = 0;

foreach ($cart_items as $item) {
    $item_total = $item['price'] * $item['quantity'];
    $subtotal += $item_total;
}

$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;

// Configurar página
$page_title = 'Checkout';
$page_scripts = [
    'https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=USD',
    'assets/js/checkout.js'
];

// Incluir header
include 'includes/header.php';
?>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="checkout-form">
                    <h2 class="mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>Finalizar Pedido
                    </h2>
                    
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="customerForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Correo Electrónico *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Teléfono *</label>
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
                                <i class="fas fa-clock me-2"></i>Información de Pickup
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pickupDate" class="form-label">Fecha de Pickup *</label>
                                    <input type="date" class="form-control" id="pickupDate" name="pickup_date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pickupTime" class="form-label">Hora de Pickup *</label>
                                    <select class="form-select" id="pickupTime" name="pickup_time" required>
                                        <option value="">Seleccionar hora</option>
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
                                <label for="specialInstructions" class="form-label">Instrucciones Especiales</label>
                                <textarea class="form-control" id="specialInstructions" name="special_instructions" rows="3" placeholder="Alergias, modificaciones, etc."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Método de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-methods">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal me-2 text-primary"></i>PayPal
                                        <small class="text-muted d-block">Paga de forma segura con PayPal</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pickup_payment" value="pickup">
                                    <label class="form-check-label" for="pickup_payment">
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i>Pagar al Recoger
                                        <small class="text-muted d-block">Efectivo, tarjeta o PayPal al recoger</small>
                                    </label>
                                </div>
                            </div>
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
                                <i class="fas fa-receipt me-2"></i>Resumen del Pedido
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div class="order-items mb-4">
                                <?php foreach ($cart_items as $item): ?>
                                <div class="order-item d-flex justify-content-between align-items-center mb-3">
                                    <div class="item-info">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">Cantidad: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div class="item-price">
                                        <strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Order Totals -->
                            <div class="order-totals">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Impuestos (8.25%):</span>
                                    <span>$<?php echo number_format($tax, 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                                </div>
                            </div>
                            
                            <!-- PayPal Button -->
                            <div id="paypal-button-container" class="mb-3" style="display: none;">
                                <!-- PayPal button will be rendered here -->
                            </div>
                            
                            <!-- Place Order Button -->
                            <button class="btn btn-primary btn-lg w-100" id="placeOrderBtn">
                                <i class="fas fa-check me-2"></i>Confirmar Pedido
                            </button>
                            
                            <!-- Security Notice -->
                            <div class="security-notice mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>
                                    Tu información está protegida con encriptación SSL
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
$(document).ready(function() {
    // Configurar fecha mínima (hoy)
    const today = new Date().toISOString().split('T')[0];
    $('#pickupDate').attr('min', today);
    
    // Configurar PayPal
    setupPayPal();
    
    // Configurar formulario
    setupCheckoutForm();
    
    // Configurar método de pago
    setupPaymentMethod();
});

function setupPayPal() {
    // PayPal SDK se carga desde el header
    if (typeof paypal !== 'undefined') {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>',
                            currency_code: 'USD'
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
                showNotification('Error con PayPal: ' + err.message, 'error');
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
            
            if (paymentMethod === 'paypal') {
                // Mostrar botón de PayPal
                $('#paypal-button-container').show();
                $('#placeOrderBtn').hide();
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
        
        if (paymentMethod === 'paypal') {
            $('#paypal-button-container').show();
            $('#placeOrderBtn').hide();
        } else {
            $('#paypal-button-container').hide();
            $('#placeOrderBtn').show();
        }
    });
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
        showNotification('Por favor, completa todos los campos requeridos', 'error');
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
    const orderData = {
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
    $('#placeOrderBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...').prop('disabled', true);
    
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
                showNotification('Error al procesar el pedido: ' + response.message, 'error');
                $('#placeOrderBtn').html('<i class="fas fa-check me-2"></i>Confirmar Pedido').prop('disabled', false);
            }
        },
        error: function() {
            showNotification('Error de conexión', 'error');
            $('#placeOrderBtn').html('<i class="fas fa-check me-2"></i>Confirmar Pedido').prop('disabled', false);
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
