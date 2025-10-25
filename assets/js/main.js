/**
 * Horchata Mexican Food - Main JavaScript
 * Funcionalidades principales del sitio
 */

$(document).ready(function() {
    // Inicializar funcionalidades
    initCart();
    initLanguageSwitcher();
    initSmoothScrolling();
    initAnimations();
    
    // Cargar carrito desde localStorage
    loadCartFromStorage();
});

/**
 * Sistema de Carrito de Compras
 */
function initCart() {
    // Agregar producto al carrito
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const productPrice = parseFloat($(this).data('product-price'));
        const productImage = $(this).data('product-image');
        
        addToCart(productId, productName, productPrice, productImage);
        
        // Mostrar feedback visual
        showCartFeedback($(this));
    });
    
    // Actualizar cantidad en carrito
    $(document).on('click', '.quantity-btn', function() {
        const action = $(this).data('action');
        const productId = $(this).data('product-id');
        
        if (action === 'increase') {
            increaseQuantity(productId);
        } else if (action === 'decrease') {
            decreaseQuantity(productId);
        }
        
        updateCartDisplay();
    });
    
    // Remover producto del carrito
    $(document).on('click', '.remove-from-cart', function() {
        const productId = $(this).data('product-id');
        removeFromCart(productId);
    });
    
    // Proceder al checkout
    $(document).on('click', '#checkoutBtn', function() {
        console.log('🛒 Checkout: Botón clickeado');
        
        const cartItems = getCartItems();
        console.log('🛒 Checkout: Items en carrito:', cartItems);
        
        if (cartItems.length > 0) {
            console.log('🛒 Checkout: Enviando datos al servidor...');
            
            // Enviar datos del carrito al servidor
            $.ajax({
                url: 'ajax/cart.ajax.php',
                method: 'POST',
                data: {
                    action: 'prepare_checkout',
                    cart_data: JSON.stringify(cartItems)
                },
                success: function(response) {
                    console.log('🛒 Checkout: Respuesta del servidor:', response);
                    
                    if (response.success) {
                        console.log('🛒 Checkout: Redirigiendo a checkout.php');
                        window.location.href = 'checkout.php';
                    } else {
                        console.error('❌ Checkout: Error del servidor:', response.message);
                        alert('Error al procesar el carrito: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Checkout: Error AJAX:', error);
                    console.log('🛒 Checkout: Fallback - redirigiendo directamente');
                    // Fallback: redirigir directamente
                    window.location.href = 'checkout.php';
                }
            });
        } else {
            console.log('🛒 Checkout: Carrito vacío');
            alert('Tu carrito está vacío. Agrega algunos productos antes de proceder al pago.');
        }
    });
}

/**
 * Agregar producto al carrito
 */
function addToCart(productId, productName, productPrice, productImage) {
    console.log('🛒 Main: Agregando al carrito:', {productId, productName, productPrice, productImage});
    
    // Validar datos
    if (!productId || !productName) {
        console.error('❌ Main: Datos del producto inválidos');
        return;
    }
    
    const price = parseFloat(productPrice) || 0;
    const image = productImage || 'assets/images/placeholder.jpg';
    
    let cart = getCartItems();
    const existingItem = cart.find(item => item.id == productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            image: image,
            quantity: 1
        });
    }
    
    console.log('🛒 Main: Carrito actualizado:', cart);
    
    saveCartToStorage(cart);
    updateCartDisplay();
    
    // Mostrar notificación
    showNotification('Producto agregado al carrito', 'success');
}

/**
 * Aumentar cantidad de producto
 */
function increaseQuantity(productId) {
    let cart = getCartItems();
    const item = cart.find(item => item.id === productId);
    
    if (item) {
        item.quantity += 1;
        saveCartToStorage(cart);
    }
}

/**
 * Disminuir cantidad de producto
 */
function decreaseQuantity(productId) {
    let cart = getCartItems();
    const itemIndex = cart.findIndex(item => item.id === productId);
    
    if (itemIndex !== -1) {
        if (cart[itemIndex].quantity > 1) {
            cart[itemIndex].quantity -= 1;
        } else {
            cart.splice(itemIndex, 1);
        }
        saveCartToStorage(cart);
    }
}

/**
 * Remover producto del carrito
 */
function removeFromCart(productId) {
    let cart = getCartItems();
    cart = cart.filter(item => item.id !== productId);
    saveCartToStorage(cart);
    updateCartDisplay();
    
    showNotification('Producto removido del carrito', 'info');
}

/**
 * Obtener items del carrito
 */
function getCartItems() {
    const cart = localStorage.getItem('horchata_cart');
    return cart ? JSON.parse(cart) : [];
}

/**
 * Guardar carrito en localStorage
 */
function saveCartToStorage(cart) {
    localStorage.setItem('horchata_cart', JSON.stringify(cart));
}

/**
 * Cargar carrito desde localStorage
 */
function loadCartFromStorage() {
    updateCartDisplay();
}

/**
 * Actualizar display del carrito
 */
function updateCartDisplay() {
    const cart = getCartItems();
    const cartCount = cart.reduce((total, item) => total + (item.quantity || 0), 0);
    const cartTotal = cart.reduce((total, item) => {
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        return total + (price * quantity);
    }, 0);
    
    // Actualizar contador
    $('#cartCount').text(cartCount);
    
    // Actualizar total
    $('#cartTotal').text('$' + cartTotal.toFixed(2));
    
    // Habilitar/deshabilitar botón de checkout
    $('#checkoutBtn').prop('disabled', cartCount === 0);
    
    // Actualizar items en el offcanvas
    updateCartOffcanvas(cart);
}

/**
 * Actualizar contenido del offcanvas del carrito
 */
function updateCartOffcanvas(cart) {
    const cartItemsContainer = $('#cartItems');
    
    if (cart.length === 0) {
        cartItemsContainer.html(`
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Tu carrito está vacío</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    cart.forEach(item => {
        const price = parseFloat(item.price) || 0;
        const quantity = parseInt(item.quantity) || 0;
        const name = item.name || 'Producto';
        const image = item.image || 'assets/images/placeholder.jpg';
        const id = item.id || 0;
        
        html += `
            <div class="cart-item">
                <img src="${image}" alt="${name}" class="cart-item-image">
                <div class="cart-item-info">
                    <div class="cart-item-name">${name}</div>
                    <div class="cart-item-price">$${price.toFixed(2)}</div>
                </div>
                <div class="cart-quantity">
                    <button class="quantity-btn" data-action="decrease" data-product-id="${id}">-</button>
                    <span class="mx-2">${quantity}</span>
                    <button class="quantity-btn" data-action="increase" data-product-id="${id}">+</button>
                </div>
                <button class="btn btn-sm btn-outline-danger remove-from-cart" data-product-id="${id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });
    
    cartItemsContainer.html(html);
}

/**
 * Cambio de idioma
 */
function initLanguageSwitcher() {
    // Manejar cambio de idioma
    $('.dropdown-item[href*="lang="]').on('click', function(e) {
        e.preventDefault();
        const lang = $(this).attr('href').split('lang=')[1];
        
        // Guardar preferencia de idioma
        localStorage.setItem('horchata_language', lang);
        
        // Recargar página con nuevo idioma
        const url = new URL(window.location);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    });
    
    // Aplicar idioma guardado solo si no hay parámetro lang en la URL
    const savedLang = localStorage.getItem('horchata_language');
    if (savedLang && !window.location.search.includes('lang=')) {
        const url = new URL(window.location);
        url.searchParams.set('lang', savedLang);
        window.location.href = url.toString();
    }
}

/**
 * Scroll suave
 */
function initSmoothScrolling() {
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });
}

/**
 * Animaciones
 */
function initAnimations() {
    // Animación de entrada para elementos
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observar elementos con clase 'animate-on-scroll'
    $('.animate-on-scroll').each(function() {
        observer.observe(this);
    });
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
 * Cargar productos dinámicamente
 */
function loadProducts(categoryId = null) {
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { category_id: categoryId },
        dataType: 'json',
        beforeSend: function() {
            $('#productsContainer').addClass('loading');
        },
        success: function(response) {
            if (response.success) {
                displayProducts(response.data);
            } else {
                showNotification('Error al cargar productos', 'error');
            }
        },
        error: function() {
            showNotification('Error de conexión', 'error');
        },
        complete: function() {
            $('#productsContainer').removeClass('loading');
        }
    });
}

/**
 * Mostrar productos en el DOM
 */
function displayProducts(products) {
    const container = $('#productsContainer');
    let html = '';
    
    products.forEach(product => {
        html += `
            <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll">
                <div class="product-card">
                    <div class="product-image" style="background-image: url('${product.image}')"></div>
                    <div class="product-info">
                        <h5 class="product-title">${product.name}</h5>
                        <p class="product-description">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$${product.price}</span>
                            <button class="btn btn-primary btn-sm add-to-cart"
                                    data-product-id="${product.id}"
                                    data-product-name="${product.name}"
                                    data-product-price="${product.price}"
                                    data-product-image="${product.image}">
                                <i class="fas fa-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

/**
 * Filtrar productos por categoría
 */
function filterProductsByCategory(categoryId) {
    loadProducts(categoryId);
}

/**
 * Buscar productos
 */
function searchProducts(query) {
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { search: query },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProducts(response.data);
            }
        }
    });
}

// Funciones auxiliares para el carrito
function getCartItems() {
    const cart = localStorage.getItem('horchata_cart');
    return cart ? JSON.parse(cart) : [];
}

function saveCartToStorage(cart) {
    localStorage.setItem('horchata_cart', JSON.stringify(cart));
}

function updateCartDisplay() {
    const cart = getCartItems();
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    
    // Actualizar contador
    $('#cartCount').text(cartCount);
    
    // Actualizar total
    $('#cartTotal').text('$' + cartTotal.toFixed(2));
    
    // Habilitar/deshabilitar botón de checkout
    $('#checkoutBtn').prop('disabled', cartCount === 0);
    
    // Actualizar items en el offcanvas
    updateCartOffcanvas(cart);
}

// Exportar funciones para uso global
window.HorchataCart = {
    addToCart,
    removeFromCart,
    getCartItems,
    saveCartToStorage,
    updateCartDisplay,
    loadProducts,
    displayProducts,
    filterProductsByCategory,
    searchProducts
};
