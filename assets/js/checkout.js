/**
 * Checkout JavaScript
 * Horchata Mexican Food - Sistema de Checkout con PayPal
 */

$(document).ready(function() {
    // Inicializar funcionalidades de checkout
    initCheckout();
});

function initCheckout() {
    // Configurar fecha mínima
    setupDateRestrictions();
    
    // Configurar validaciones
    setupFormValidation();
    
    // PayPal se configura en checkout.php para evitar duplicación
    // setupPayPalIntegration(); // COMENTADO - PayPal se maneja en checkout.php
    
    // Configurar método de pago (solo para mostrar/ocultar elementos, no renderizar PayPal)
    setupPaymentMethodToggle();
    
    // El envío de formulario se maneja en checkout.php para evitar conflictos
    // setupFormSubmission(); // COMENTADO - checkout.php maneja el submit
}

/**
 * Configurar restricciones de fecha
 */
function setupDateRestrictions() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Establecer fecha mínima (mañana)
    const minDate = tomorrow.toISOString().split('T')[0];
    $('#pickupDate').attr('min', minDate);
    
    // Establecer fecha por defecto (mañana)
    $('#pickupDate').val(minDate);
    
    // Configurar horarios disponibles
    setupAvailableTimes();
}

/**
 * Configurar horarios disponibles
 */
function setupAvailableTimes() {
    const pickupDate = $('#pickupDate');
    const pickupTime = $('#pickupTime');
    
    pickupDate.on('change', function() {
        const selectedDate = new Date($(this).val());
        const today = new Date();
        const isToday = selectedDate.toDateString() === today.toDateString();
        
        // Limpiar opciones
        pickupTime.html('<option value="">Seleccionar hora</option>');
        
        // Horarios disponibles
        const availableTimes = [
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
            '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
            '20:00', '20:30', '21:00'
        ];
        
        // Si es hoy, filtrar horarios pasados
        if (isToday) {
            const currentHour = today.getHours();
            const currentMinute = today.getMinutes();
            const currentTime = currentHour * 100 + currentMinute;
            
            availableTimes.forEach(time => {
                const [hour, minute] = time.split(':');
                const timeValue = parseInt(hour) * 100 + parseInt(minute);
                
                if (timeValue > currentTime + 30) { // 30 minutos de anticipación
                    pickupTime.append(`<option value="${time}">${formatTime(time)}</option>`);
                }
            });
        } else {
            // Para días futuros, mostrar todos los horarios
            availableTimes.forEach(time => {
                pickupTime.append(`<option value="${time}">${formatTime(time)}</option>`);
            });
        }
    });
    
    // Trigger change para cargar horarios iniciales
    pickupDate.trigger('change');
}

/**
 * Formatear hora para mostrar
 */
function formatTime(time) {
    const [hour, minute] = time.split(':');
    const hour24 = parseInt(hour);
    const hour12 = hour24 === 0 ? 12 : hour24 > 12 ? hour24 - 12 : hour24;
    const ampm = hour24 >= 12 ? 'PM' : 'AM';
    return `${hour12}:${minute} ${ampm}`;
}

/**
 * Configurar validaciones del formulario
 */
function setupFormValidation() {
    // Validación en tiempo real
    $('input[required], select[required]').on('blur', function() {
        validateField($(this));
    });
    
    // Validación de email
    $('#email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            showFieldError($(this), 'Por favor, ingresa un email válido');
        } else {
            clearFieldError($(this));
        }
    });
    
    // Validación de teléfono
    $('#phone').on('blur', function() {
        const phone = $(this).val();
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        
        if (phone && !phoneRegex.test(phone)) {
            showFieldError($(this), 'Por favor, ingresa un teléfono válido');
        } else {
            clearFieldError($(this));
        }
    });
}

/**
 * Validar campo individual
 */
function validateField(field) {
    const value = field.val().trim();
    const isRequired = field.prop('required');
    
    if (isRequired && !value) {
        showFieldError(field, 'Este campo es requerido');
        return false;
    } else {
        clearFieldError(field);
        return true;
    }
}

/**
 * Mostrar error en campo
 */
function showFieldError(field, message) {
    field.addClass('is-invalid');
    
    let feedback = field.siblings('.invalid-feedback');
    if (feedback.length === 0) {
        feedback = $(`<div class="invalid-feedback">${message}</div>`);
        field.after(feedback);
    } else {
        feedback.text(message);
    }
}

/**
 * Limpiar error de campo
 */
function clearFieldError(field) {
    field.removeClass('is-invalid');
    field.siblings('.invalid-feedback').remove();
}

/**
 * Configurar integración con PayPal
 */
function setupPayPalIntegration() {
    console.log('💳 Checkout: Configurando PayPal...');
    
    // Verificar si PayPal SDK está disponible
    if (typeof paypal !== 'undefined' && paypal.Buttons) {
        console.log('✅ Checkout: PayPal SDK disponible');
        
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'gold',
                shape: 'rect',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: getOrderTotal(),
                            currency_code: 'USD'
                        },
                        description: 'Pedido en Horchata Mexican Food'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Procesar pedido con PayPal
                    processPayPalOrder(details);
                });
            },
            onError: function(err) {
                showNotification('Error con PayPal: ' + err.message, 'error');
            },
            onCancel: function(data) {
                showNotification('Pago cancelado', 'warning');
            }
        }).render('#paypal-button-container');
    } else {
        console.warn('⚠️ Checkout: PayPal SDK no disponible');
        // Ocultar opción PayPal si no está disponible
        $('input[value="paypal"]').closest('.form-check').hide();
        $('input[value="pickup"]').prop('checked', true);
        showNotification('PayPal no disponible. Usando pago al recoger.', 'warning');
    }
}

/**
 * Configurar toggle de método de pago
 * NOTA: Esta función está deshabilitada porque checkout.php maneja el toggle de métodos de pago
 * para evitar conflictos con Wire Transfer y PayPal
 */
function setupPaymentMethodToggle() {
    // Comentado para evitar conflictos con checkout.php
    // El manejo de métodos de pago se realiza en checkout.php
    /*
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
    */
}

/**
 * Configurar envío de formulario
 */
function setupFormSubmission() {
    $('#placeOrderBtn').on('click', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            const paymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (paymentMethod === 'pickup') {
                processPickupOrder();
            }
        }
    });
}

/**
 * Validar formulario completo
 */
function validateForm() {
    let isValid = true;
    const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'pickupDate', 'pickupTime'];
    
    requiredFields.forEach(fieldId => {
        const field = $(`#${fieldId}`);
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showNotification('Por favor, completa todos los campos requeridos', 'error');
        $('html, body').animate({
            scrollTop: $('.is-invalid').first().offset().top - 100
        }, 500);
    }
    
    return isValid;
}

/**
 * Procesar pedido con PayPal
 */
function processPayPalOrder(paypalDetails) {
    const orderData = {
        customer: getCustomerData(),
        items: getCartItems(),
        totals: getOrderTotals(),
        payment: {
            method: 'paypal',
            transaction_id: paypalDetails.id,
            status: 'paid'
        }
    };
    
    submitOrder(orderData);
}

/**
 * Procesar pedido para pickup
 */
function processPickupOrder() {
    console.log('🛒 Checkout: Procesando pedido para pickup...');
    
    const orderData = {
        customer: getCustomerData(),
        items: getCartItems(),
        totals: getOrderTotals(),
        payment: {
            method: 'pickup',
            status: 'pending'
        }
    };
    
    console.log('🛒 Checkout: Datos del pedido:', orderData);
    submitOrder(orderData);
}

/**
 * Obtener datos del cliente
 */
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

/**
 * Obtener items del carrito
 */
function getCartItems() {
    console.log('🛒 Checkout: Obteniendo items del carrito...');
    
    try {
        // Obtener del localStorage
        const cart = localStorage.getItem('horchata_cart');
        const items = cart ? JSON.parse(cart) : [];
        
        console.log('🛒 Checkout: Items encontrados:', items);
        
        if (!Array.isArray(items)) {
            console.warn('⚠️ Checkout: Carrito no es un array, retornando array vacío');
            return [];
        }
        
        return items;
    } catch (error) {
        console.error('❌ Checkout: Error al obtener carrito:', error);
        return [];
    }
}

/**
 * Obtener totales de la orden
 */
function getOrderTotals() {
    const subtotal = parseFloat($('.order-totals .d-flex:first span:last').text().replace('$', ''));
    const tax = parseFloat($('.order-totals .d-flex:nth-child(2) span:last').text().replace('$', ''));
    const total = parseFloat($('.order-totals .d-flex:last strong:last').text().replace('$', ''));
    
    return { subtotal, tax, total };
}

/**
 * Obtener total de la orden para PayPal
 */
function getOrderTotal() {
    const total = parseFloat($('.order-totals .d-flex:last strong:last').text().replace('$', ''));
    return total.toFixed(2);
}

/**
 * Enviar orden al servidor
 */
function submitOrder(orderData) {
    console.log('🛒 Checkout: Enviando orden al servidor...', orderData);
    
    // Mostrar loading
    showLoadingState();
    
    $.ajax({
        url: 'ajax/orders.ajax.php',
        method: 'POST',
        data: { 
            action: 'create_order', 
            order_data: JSON.stringify(orderData) 
        },
        dataType: 'json',
        success: function(response) {
            console.log('🛒 Checkout: Respuesta del servidor:', response);
            
            if (response.success) {
                // Limpiar carrito
                clearCart();
                
                // Redirigir a confirmación
                window.location.href = `order-success.php?order_id=${response.order_id}`;
            } else {
                showNotification('Error al procesar el pedido: ' + response.message, 'error');
                hideLoadingState();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Checkout: Error AJAX:', error);
            console.error('❌ Checkout: Status:', status);
            console.error('❌ Checkout: Response:', xhr.responseText);
            
            showNotification('Error de conexión: ' + error, 'error');
            hideLoadingState();
        }
    });
}

/**
 * Mostrar estado de carga
 */
function showLoadingState() {
    $('#placeOrderBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...').prop('disabled', true);
    $('.checkout-form').addClass('loading');
}

/**
 * Ocultar estado de carga
 */
function hideLoadingState() {
    $('#placeOrderBtn').html('<i class="fas fa-check me-2"></i>Confirmar Pedido').prop('disabled', false);
    $('.checkout-form').removeClass('loading');
}

/**
 * Limpiar carrito
 */
function clearCart() {
    localStorage.removeItem('horchata_cart');
    sessionStorage.removeItem('horchata_cart');
}

/**
 * Mostrar notificación
 */
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

// Exportar funciones para uso global
window.CheckoutPage = {
    validateForm,
    processPayPalOrder,
    processPickupOrder,
    submitOrder,
    showNotification
};
