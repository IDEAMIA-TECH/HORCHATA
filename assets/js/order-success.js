/**
 * Order Success JavaScript
 * Horchata Mexican Food - Página de confirmación de pedido
 */

$(document).ready(function() {
    console.log('✅ Order Success: Página cargada');
    
    // Inicializar funcionalidades
    initOrderSuccess();
});

function initOrderSuccess() {
    console.log('✅ Order Success: Inicializando...');
    
    // Limpiar carrito del localStorage
    clearCart();
    
    // Actualizar contador del carrito
    if (typeof updateCartDisplay === 'function') {
        updateCartDisplay();
    }
    
    // Configurar botón de impresión
    setupPrintButton();
    
    // Configurar botón de nuevo pedido
    setupNewOrderButton();
    
    // Configurar animaciones
    setupAnimations();
    
    // Configurar notificaciones
    setupNotifications();
    
    // Configurar funciones de compartir
    setupShareFunctions();
}

/**
 * Configurar botón de impresión
 */
function setupPrintButton() {
    $('#printOrderBtn').on('click', function(e) {
        e.preventDefault();
        console.log('🖨️ Order Success: Imprimiendo orden...');
        printOrder();
    });
}

/**
 * Función de impresión
 */
function printOrder() {
    console.log('🖨️ Order Success: Generando recibo para impresión...');
    
    // Crear ventana de impresión
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    // Obtener datos del pedido del DOM
    const orderNumber = $('.order-number').text() || 'N/A';
    const orderDate = $('.order-date').text() || 'N/A';
    const customerName = $('.customer-name').text() || 'N/A';
    const customerEmail = $('.customer-email').text() || 'N/A';
    const pickupDate = $('.pickup-date').text() || 'N/A';
    const orderStatus = $('.order-status').text() || 'N/A';
    const paymentMethod = $('.payment-method').text() || 'N/A';
    const paymentStatus = $('.payment-status').text() || 'N/A';
    const specialInstructions = $('.special-instructions').text() || 'Ninguna';
    
    // Obtener items del pedido
    const orderItems = [];
    $('.order-item').each(function() {
        const name = $(this).find('.item-name').text();
        const quantity = $(this).find('.item-quantity').text();
        const price = $(this).find('.item-price').text();
        if (name && quantity && price) {
            orderItems.push({ name, quantity, price });
        }
    });
    
    // Obtener totales
    const subtotal = $('.subtotal').text() || '$0.00';
    const tax = $('.tax').text() || '$0.00';
    const total = $('.total').text() || '$0.00';
    
    // Generar HTML del recibo
    const receiptHTML = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recibo - ${orderNumber}</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background: white;
                    padding: 20px;
                }
                
                .receipt {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border: 2px solid #000;
                    padding: 30px;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 20px;
                }
                
                .restaurant-name {
                    font-size: 28px;
                    font-weight: bold;
                    color: #d4af37;
                    margin-bottom: 10px;
                }
                
                .receipt-title {
                    font-size: 24px;
                    font-weight: bold;
                    color: #28a745;
                    margin-bottom: 10px;
                }
                
                .confirmation-text {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 20px;
                }
                
                .section {
                    margin-bottom: 25px;
                }
                
                .section-title {
                    font-size: 18px;
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                
                .order-details {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                    margin-bottom: 20px;
                }
                
                .detail-item {
                    margin-bottom: 10px;
                }
                
                .detail-label {
                    font-weight: bold;
                    color: #666;
                    font-size: 14px;
                }
                
                .detail-value {
                    color: #333;
                    font-size: 16px;
                }
                
                .order-number {
                    font-size: 20px;
                    font-weight: bold;
                    color: #d4af37;
                }
                
                .order-date {
                    font-size: 16px;
                    color: #333;
                }
                
                .customer-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                }
                
                .pickup-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                }
                
                .order-items {
                    margin-bottom: 20px;
                }
                
                .item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 0;
                    border-bottom: 1px solid #eee;
                }
                
                .item:last-child {
                    border-bottom: none;
                }
                
                .item-name {
                    font-weight: bold;
                    color: #333;
                }
                
                .item-quantity {
                    color: #666;
                    font-size: 14px;
                }
                
                .item-price {
                    font-weight: bold;
                    color: #333;
                }
                
                .totals {
                    border-top: 2px solid #000;
                    padding-top: 15px;
                    margin-top: 20px;
                }
                
                .total-line {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 8px;
                }
                
                .total-line.final {
                    font-size: 18px;
                    font-weight: bold;
                    color: #d4af37;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                    margin-top: 10px;
                }
                
                .payment-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                }
                
                .special-instructions {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    color: #666;
                    font-size: 14px;
                }
                
                @media print {
                    body {
                        padding: 0;
                    }
                    
                    .receipt {
                        border: none;
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <!-- Header -->
                <div class="header">
                    <div class="restaurant-name">Horchata Mexican Food</div>
                    <div class="receipt-title">¡Pedido Confirmado!</div>
                    <div class="confirmation-text">
                        Gracias por tu pedido. Te hemos enviado un correo de confirmación con todos los detalles.
                    </div>
                </div>
                
                <!-- Detalles del Pedido -->
                <div class="section">
                    <div class="section-title">Detalles del Pedido</div>
                    <div class="order-details">
                        <div class="detail-item">
                            <div class="detail-label">Número de Pedido</div>
                            <div class="detail-value order-number">${orderNumber}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Fecha de Pedido</div>
                            <div class="detail-value order-date">${orderDate}</div>
                        </div>
                    </div>
                    
                    <div class="customer-info">
                        <div class="detail-item">
                            <div class="detail-label">Cliente</div>
                            <div class="detail-value">${customerName}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">${customerEmail}</div>
                        </div>
                    </div>
                    
                    <div class="pickup-info">
                        <div class="detail-item">
                            <div class="detail-label">Pickup</div>
                            <div class="detail-value">${pickupDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">${orderStatus}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Items del Pedido -->
                <div class="section">
                    <div class="section-title">Items del Pedido</div>
                    <div class="order-items">
                        ${orderItems.map(item => `
                            <div class="item">
                                <div>
                                    <div class="item-name">${item.name}</div>
                                    <div class="item-quantity">Cantidad: ${item.quantity}</div>
                                </div>
                                <div class="item-price">${item.price}</div>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div class="totals">
                        <div class="total-line">
                            <span>Subtotal:</span>
                            <span>${subtotal}</span>
                        </div>
                        <div class="total-line">
                            <span>Impuestos:</span>
                            <span>${tax}</span>
                        </div>
                        <div class="total-line final">
                            <span>Total:</span>
                            <span>${total}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Información de Pago -->
                <div class="section">
                    <div class="section-title">Información de Pago</div>
                    <div class="payment-info">
                        <div class="detail-item">
                            <div class="detail-label">Método de Pago</div>
                            <div class="detail-value">${paymentMethod}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Estado del Pago</div>
                            <div class="detail-value">${paymentStatus}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Instrucciones Especiales -->
                <div class="special-instructions">
                    <div class="detail-label">Instrucciones Especiales</div>
                    <div class="detail-value">${specialInstructions}</div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <p>¡Gracias por elegir Horchata Mexican Food!</p>
                    <p>Para consultas: info@horchatamexicanfood.com</p>
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(receiptHTML);
    printWindow.document.close();
    
    // Esperar a que se cargue el contenido y luego imprimir
    printWindow.onload = function() {
        printWindow.print();
    };
}

/**
 * Configurar botón de nuevo pedido
 */
function setupNewOrderButton() {
    $('#newOrderBtn').on('click', function(e) {
        e.preventDefault();
        console.log('🛒 Order Success: Iniciando nuevo pedido...');
        
        // Limpiar carrito
        clearCart();
        
        // Redirigir al menú
        window.location.href = 'menu.php';
    });
}

/**
 * Configurar animaciones
 */
function setupAnimations() {
    // Animación de entrada para elementos
    $('.success-animation').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        });
        
        setTimeout(() => {
            $(this).animate({
                'opacity': '1'
            }, 500).css('transform', 'translateY(0)');
        }, index * 200);
    });
    
    // Animación del icono de éxito
    $('.success-icon').addClass('animate__animated animate__bounceIn');
    
    // Animación de los items de la orden
    $('.order-item').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateX(-20px)'
        });
        
        setTimeout(() => {
            $(this).animate({
                'opacity': '1'
            }, 300).css('transform', 'translateX(0)');
        }, 1000 + (index * 100));
    });
}

/**
 * Configurar notificaciones
 */
function setupNotifications() {
    // Mostrar notificación de éxito
    showSuccessNotification();
    
    // Configurar notificaciones de seguimiento
    setupTrackingNotifications();
}

/**
 * Mostrar notificación de éxito
 */
function showSuccessNotification() {
    const notification = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>¡Pedido confirmado!</strong> Tu pedido ha sido procesado exitosamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.container').prepend(notification);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

/**
 * Configurar notificaciones de seguimiento
 */
function setupTrackingNotifications() {
    // Notificación de tiempo estimado
    setTimeout(() => {
        showNotification('Tu pedido estará listo en aproximadamente 20-30 minutos', 'info');
    }, 3000);
    
    // Notificación de contacto
    setTimeout(() => {
        showNotification('Te contactaremos cuando tu pedido esté listo para recoger', 'info');
    }, 8000);
}

/**
 * Limpiar carrito
 */
function clearCart() {
    console.log('🛒 Order Success: Limpiando carrito...');
    
    try {
        localStorage.removeItem('horchata_cart');
        sessionStorage.removeItem('horchata_cart');
        
        // También limpiar en el servidor si es posible
        $.ajax({
            url: 'ajax/cart.ajax.php',
            method: 'POST',
            data: { action: 'clear_cart' },
            success: function(response) {
                console.log('✅ Order Success: Carrito limpiado en servidor');
            },
            error: function(xhr, status, error) {
                console.warn('⚠️ Order Success: No se pudo limpiar carrito en servidor:', error);
            }
        });
    } catch (error) {
        console.error('❌ Order Success: Error al limpiar carrito:', error);
    }
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
    };
    
    const iconClass = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };
    
    const notification = `
        <div class="alert ${alertClass[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="${iconClass[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(notification);
    
    // Auto-dismiss después de 4 segundos
    setTimeout(() => {
        $('.alert').fadeOut(() => {
            $('.alert').remove();
        });
    }, 4000);
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Formatear moneda
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Configurar funciones de compartir
 */
function setupShareFunctions() {
    // Configurar botones de compartir
    $('.share-btn').on('click', function(e) {
        e.preventDefault();
        const platform = $(this).data('platform');
        shareOrder(platform);
    });
}

/**
 * Función para compartir en redes sociales
 */
function shareOrder(platform) {
    const orderNumber = $('.order-number').text() || 'N/A';
    const shareText = `¡Acabo de hacer un pedido en Horchata Mexican Food! Pedido #${orderNumber}`;
    const shareUrl = window.location.href;
    
    let shareLink = '';
    
    switch(platform) {
        case 'facebook':
            shareLink = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
            break;
        case 'twitter':
            shareLink = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
            break;
        case 'whatsapp':
            shareLink = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
            break;
    }
    
    if (shareLink) {
        window.open(shareLink, '_blank', 'width=600,height=400');
    }
}

// Exportar funciones para uso global
window.OrderSuccess = {
    clearCart,
    showNotification,
    formatDate,
    formatCurrency,
    printOrder,
    shareOrder
};
