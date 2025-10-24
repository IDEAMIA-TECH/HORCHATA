/**
 * Product Page JavaScript
 * Horchata Mexican Food - Página de Detalle de Producto
 */

$(document).ready(function() {
    // Inicializar funcionalidades de la página de producto
    initProductPage();
});

function initProductPage() {
    // Configurar selector de cantidad
    setupQuantitySelector();
    
    // Configurar botón de agregar al carrito
    setupAddToCart();
    
    // Configurar botón de favoritos
    setupWishlist();
    
    // Configurar galería de imágenes
    setupImageGallery();
    
    // Configurar acordeón de información adicional
    setupAccordion();
    
    // Configurar productos relacionados
    setupRelatedProducts();
}

/**
 * Configurar selector de cantidad
 */
function setupQuantitySelector() {
    $('.quantity-btn').on('click', function() {
        const action = $(this).data('action');
        const input = $('#productQuantity');
        let value = parseInt(input.val());
        
        if (action === 'increase' && value < 10) {
            input.val(value + 1);
        } else if (action === 'decrease' && value > 1) {
            input.val(value - 1);
        }
        
        // Actualizar botón de agregar al carrito
        updateAddToCartButton();
    });
    
    $('#productQuantity').on('change', function() {
        let value = parseInt($(this).val());
        if (value < 1) $(this).val(1);
        if (value > 10) $(this).val(10);
        
        // Actualizar botón de agregar al carrito
        updateAddToCartButton();
    });
}

/**
 * Configurar botón de agregar al carrito
 */
function setupAddToCart() {
    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const productPrice = parseFloat($(this).data('product-price'));
        const productImage = $(this).data('product-image');
        const quantity = parseInt($('#productQuantity').val());
        
        // Agregar producto al carrito con la cantidad especificada
        addMultipleToCart(productId, productName, productPrice, productImage, quantity);
        
        // Mostrar feedback visual
        showCartFeedback($(this));
        
        // Mostrar notificación
        const message = quantity === 1 
            ? `${productName} agregado al carrito`
            : `${quantity} ${productName} agregados al carrito`;
        showNotification(message, 'success');
    });
}

/**
 * Agregar múltiples cantidades al carrito
 */
function addMultipleToCart(productId, productName, productPrice, productImage, quantity) {
    let cart = getCartItems();
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: quantity
        });
    }
    
    saveCartToStorage(cart);
    updateCartDisplay();
}

/**
 * Actualizar texto del botón de agregar al carrito
 */
function updateAddToCartButton() {
    const quantity = parseInt($('#productQuantity').val());
    const button = $('.add-to-cart-btn');
    const originalText = button.data('original-text') || button.html();
    
    if (!button.data('original-text')) {
        button.data('original-text', originalText);
    }
    
    if (quantity > 1) {
        button.html(`<i class="fas fa-shopping-cart me-2"></i>Agregar ${quantity} al Carrito`);
    } else {
        button.html(button.data('original-text'));
    }
}

/**
 * Configurar botón de favoritos
 */
function setupWishlist() {
    $('#wishlistBtn').on('click', function() {
        const productId = $('.add-to-cart-btn').data('product-id');
        const productName = $('.add-to-cart-btn').data('product-name');
        
        // Obtener favoritos del localStorage
        let wishlist = getWishlist();
        const isInWishlist = wishlist.some(item => item.id === productId);
        
        if (isInWishlist) {
            // Remover de favoritos
            wishlist = wishlist.filter(item => item.id !== productId);
            $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).html('<i class="fas fa-heart me-2"></i>Favoritos');
            showNotification('Removido de favoritos', 'info');
        } else {
            // Agregar a favoritos
            wishlist.push({
                id: productId,
                name: productName,
                added_at: new Date().toISOString()
            });
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $(this).html('<i class="fas fa-heart me-2"></i>En Favoritos');
            showNotification('Agregado a favoritos', 'success');
        }
        
        // Guardar favoritos
        saveWishlist(wishlist);
    });
    
    // Verificar si el producto está en favoritos
    checkWishlistStatus();
}

/**
 * Obtener lista de favoritos
 */
function getWishlist() {
    const wishlist = localStorage.getItem('horchata_wishlist');
    return wishlist ? JSON.parse(wishlist) : [];
}

/**
 * Guardar lista de favoritos
 */
function saveWishlist(wishlist) {
    localStorage.setItem('horchata_wishlist', JSON.stringify(wishlist));
}

/**
 * Verificar estado de favoritos
 */
function checkWishlistStatus() {
    const productId = $('.add-to-cart-btn').data('product-id');
    const wishlist = getWishlist();
    const isInWishlist = wishlist.some(item => item.id === productId);
    
    if (isInWishlist) {
        $('#wishlistBtn').removeClass('btn-outline-primary').addClass('btn-primary')
            .html('<i class="fas fa-heart me-2"></i>En Favoritos');
    }
}

/**
 * Configurar galería de imágenes
 */
function setupImageGallery() {
    // Por ahora, solo configurar zoom en la imagen principal
    $('#mainProductImage').on('click', function() {
        // Implementar modal de imagen ampliada
        showImageModal($(this).attr('src'), $(this).attr('alt'));
    });
}

/**
 * Mostrar modal de imagen ampliada
 */
function showImageModal(imageSrc, imageAlt) {
    const modal = $(`
        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${imageAlt}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <img src="${imageSrc}" alt="${imageAlt}" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(modal);
    $('#imageModal').modal('show');
    
    // Limpiar modal al cerrar
    $('#imageModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Configurar acordeón de información adicional
 */
function setupAccordion() {
    // Configurar acordeón de Bootstrap
    $('.accordion-button').on('click', function() {
        const target = $(this).data('bs-target');
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        
        if (!isExpanded) {
            // Scroll suave al acordeón
            setTimeout(() => {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 500);
            }, 300);
        }
    });
}

/**
 * Configurar productos relacionados
 */
function setupRelatedProducts() {
    // Agregar hover effects a productos relacionados
    $('.related-products .product-card').hover(
        function() {
            $(this).addClass('shadow-hover-custom');
        },
        function() {
            $(this).removeClass('shadow-hover-custom');
        }
    );
}

/**
 * Mostrar feedback visual al agregar al carrito
 */
function showCartFeedback(button) {
    const originalText = button.html();
    button.html('<i class="fas fa-check me-2"></i>Agregado').addClass('btn-success');
    
    setTimeout(() => {
        button.html(originalText).removeClass('btn-success');
    }, 2000);
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
    }, 3000);
}

/**
 * Compartir producto en redes sociales
 */
function shareProduct(platform) {
    const productName = $('.product-title').text();
    const productUrl = window.location.href;
    const shareText = `¡Mira este delicioso ${productName} en Horchata Mexican Food!`;
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(productUrl)}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + productUrl)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Exportar funciones para uso global
window.ProductPage = {
    addMultipleToCart,
    showCartFeedback,
    showNotification,
    shareProduct
};
