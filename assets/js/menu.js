/**
 * Menu Page JavaScript
 * Horchata Mexican Food - Página de Menú Elegante
 */

$(document).ready(function() {
    console.log('🍽️ Menu: Iniciando página elegante...');
    
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('❌ Menu: jQuery no está disponible');
        return;
    }
    
    console.log('✅ Menu: jQuery disponible');
    
    // Configurar animaciones avanzadas
    console.log('🎨 Menu: Configurando animaciones avanzadas...');
    setupAdvancedAnimations();
    
    // Cargar categorías para filtros
    console.log('🔍 Menu: Cargando categorías...');
    loadCategoryFilters();
    
    // Cargar menú completo
    console.log('🔍 Menu: Cargando menú...');
    loadMenuContent();
    
    // Configurar búsqueda
    console.log('🔍 Menu: Configurando búsqueda...');
    setupSearch();
    
    // Configurar filtros de categoría
    console.log('🔍 Menu: Configurando filtros...');
    setupCategoryFilters();
    
    // Configurar carrito
    console.log('🔍 Menu: Configurando carrito...');
    setupCart();
    
    // Configurar efectos especiales
    console.log('✨ Menu: Configurando efectos especiales...');
    setupSpecialEffects();
    
    console.log('✅ Menu: Inicialización completa');
});

/**
 * Cargar filtros de categoría
 */
function loadCategoryFilters() {
    console.log('🔍 Menu: Cargando filtros de categoría...');
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Respuesta de categorías:', response);
            if (response.success) {
                displayCategoryFilters(response.data);
            } else {
                console.error('❌ Menu: Error en categorías:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error AJAX categorías:', error);
            console.error('❌ Menu: Status:', status);
            console.error('❌ Menu: Response:', xhr.responseText);
        }
    });
}

/**
 * Mostrar filtros de categoría
 */
function displayCategoryFilters(categories) {
    console.log('🔍 Menu: displayCategoryFilters llamada con:', categories);
    
    const container = $('#categoryFilters');
    let html = '';
    
    categories.forEach((category, index) => {
        console.log(`🔍 Menu: Procesando categoría ${index}:`, category);
        console.log(`🔍 Menu: category.id = ${category.id}`);
        console.log(`🔍 Menu: category.name = ${category.name}`);
        console.log(`🔍 Menu: category.name_es = ${category.name_es}`);
        
        html += `
            <button class="btn btn-outline-primary category-filter" data-category="${category.id}">
                <i class="fas fa-utensils me-2"></i>${category.name}
            </button>
        `;
    });
    
    console.log('🔍 Menu: HTML generado:', html);
    container.html(html);
    console.log('✅ Menu: Filtros de categoría mostrados');
}

/**
 * Cargar categorías
 */
function loadCategories() {
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        data: { action: 'get_all' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCategories(response.data);
            }
        },
        error: function() {
            console.log('Error al cargar categorías');
        }
    });
}

/**
 * Mostrar categorías
 */
function displayCategories(categories) {
    const container = $('#categoryFilter');
    let html = '<option value="">Todas las categorías</option>';
    
    categories.forEach(function(category) {
        html += `<option value="${category.id}">${category.name_en}</option>`;
    });
    
    container.html(html);
}

/**
 * Configurar búsqueda
 */
function setupSearch() {
    let searchTimeout;
    
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                loadMenuContent(null, searchTerm);
            }
        }, 500);
    });
    
    $('#searchBtn').on('click', function() {
        const searchTerm = $('#searchInput').val().trim();
        loadMenuContent(null, searchTerm);
    });
    
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const searchTerm = $(this).val().trim();
            loadMenuContent(null, searchTerm);
        }
    });
}

/**
 * Configurar filtros de categoría
 */
function setupCategoryFilters() {
    $(document).on('click', '.category-filter', function() {
        // Actualizar estado activo
        $('.category-filter').removeClass('active');
        $(this).addClass('active');
        
        // Obtener categoría seleccionada
        const categoryId = $(this).data('category');
        
        // Limpiar búsqueda
        $('#searchInput').val('');
        
        // Cargar productos de la categoría
        if (categoryId === 'all') {
            loadMenuContent();
        } else {
            loadMenuContent(categoryId);
        }
    });
}

/**
 * Cargar contenido del menú
 */
function loadMenuContent(categoryId = null, searchTerm = '') {
    console.log('🔍 Menu: Cargando contenido del menú...', { categoryId, searchTerm });
    showLoading();
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { 
            category_id: categoryId,
            search: searchTerm,
            limit: 100
        },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Respuesta de productos:', response);
            if (response.success) {
                if (response.data.length > 0) {
                    console.log('🍽️ Menu: Mostrando', response.data.length, 'productos');
                    displayMenuContent(response.data);
                } else {
                    console.log('📭 Menu: No hay productos, mostrando estado vacío');
                    showEmptyState();
                }
            } else {
                console.error('❌ Menu: Error en productos:', response.message);
                showError('Error al cargar el menú');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error AJAX productos:', error);
            console.error('❌ Menu: Status:', status);
            console.error('❌ Menu: Response:', xhr.responseText);
            showError('Error de conexión');
        },
        complete: function() {
            hideLoading();
        }
    });
}

/**
 * Mostrar contenido del menú
 */
function displayMenuContent(products) {
    // Agrupar productos por categoría
    const groupedProducts = {};
    
    products.forEach(product => {
        const categoryId = product.category_id;
        if (!groupedProducts[categoryId]) {
            groupedProducts[categoryId] = {
                category_name: product.category_name,
                products: []
            };
        }
        groupedProducts[categoryId].products.push(product);
    });
    
    // Generar HTML
    let html = '';
    
    Object.keys(groupedProducts).forEach(categoryId => {
        const category = groupedProducts[categoryId];
        
        html += `
            <div class="menu-category mb-5" data-category-id="${categoryId}">
                <h2 class="category-title mb-4">${category.category_name}</h2>
                <div class="row">
        `;
        
        category.products.forEach(product => {
            html += `
                <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll">
                    <div class="product-card">
                        <div class="product-image" style="background-image: url('${product.image || 'assets/images/placeholder.jpg'}')" onclick="window.location.href='product.php?id=${product.id}'"></div>
                        <div class="product-info">
                            <h5 class="product-title">
                                <a href="product.php?id=${product.id}" class="text-decoration-none text-dark">${product.name_en}</a>
                            </h5>
                            <p class="product-description">${product.description_en || ''}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                                <div class="btn-group" role="group">
                                    <a href="product.php?id=${product.id}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                    <button class="btn btn-primary btn-sm add-to-cart"
                                            data-product-id="${product.id}"
                                            data-product-name="${product.name_en}"
                                            data-product-price="${product.price}"
                                            data-product-image="${product.image}">
                                        <i class="fas fa-plus me-1"></i>Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    $('#menuContent').html(html).show();
    $('#emptyState').hide();
}

/**
 * Mostrar productos
 */
function displayProducts(products) {
    const container = $('#productsContainer');
    if (container.length === 0) return;
    
    let html = '';
    
    if (products.length === 0) {
        html = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron productos</h5>
                <p class="text-muted">Intenta con otros filtros o términos de búsqueda</p>
            </div>
        `;
    } else {
        products.forEach(function(product) {
            html += createProductCard(product);
        });
    }
    
    container.html(html);
    
    // Configurar botones de agregar al carrito
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).siblings('.quantity-input').val()) || 1;
        addToCart(productId, quantity);
    });
    
    // Configurar selectores de cantidad
    $('.quantity-input').on('change', function() {
        const quantity = parseInt($(this).val());
        if (quantity < 1) {
            $(this).val(1);
        }
    });
}

/**
 * Crear tarjeta de producto
 */
function createProductCard(product) {
    const featuredClass = product.is_featured ? 'featured-product' : '';
    const availabilityClass = product.is_available ? '' : 'unavailable';
    
    return `
        <div class="col-lg-4 col-md-6 mb-4 product-item ${featuredClass} ${availabilityClass}" data-category="${product.category_id}" data-price="${product.price}">
            <div class="product-card h-100">
                <div class="product-image" style="background-image: url('${product.image || 'assets/images/placeholder.jpg'}')" onclick="window.location.href='product.php?id=${product.id}'">
                    ${product.is_featured ? '<div class="featured-badge"><i class="fas fa-star"></i> Destacado</div>' : ''}
                    ${!product.is_available ? '<div class="unavailable-badge">No disponible</div>' : ''}
                </div>
                <div class="product-info">
                    <h5 class="product-title">
                        <a href="product.php?id=${product.id}" class="text-decoration-none text-dark">${product.name_en}</a>
                    </h5>
                    <p class="product-description">${product.description_en || ''}</p>
                    <div class="product-price">
                        <span class="price">$${parseFloat(product.price).toFixed(2)}</span>
                    </div>
                    <div class="product-actions">
                        <div class="quantity-selector mb-2">
                            <label class="form-label small">Cantidad:</label>
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(this, -1)">-</button>
                                <input type="number" class="form-control text-center quantity-input" value="1" min="1" max="10">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(this, 1)">+</button>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 add-to-cart" data-product-id="${product.id}" ${!product.is_available ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart me-1"></i>
                            ${product.is_available ? 'Agregar al Carrito' : 'No Disponible'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Filtrar productos por categoría
 */
function filterProductsByCategory(categoryId) {
    if (categoryId === '') {
        $('.product-item').show();
    } else {
        $('.product-item').hide();
        $(`.product-item[data-category="${categoryId}"]`).show();
    }
}

/**
 * Filtrar productos por precio
 */
function filterProductsByPrice(priceRange) {
    $('.product-item').each(function() {
        const price = parseFloat($(this).data('price'));
        let show = true;
        
        switch (priceRange) {
            case '0-10':
                show = price <= 10;
                break;
            case '10-15':
                show = price > 10 && price <= 15;
                break;
            case '15+':
                show = price > 15;
                break;
        }
        
        if (show) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

/**
 * Filtrar productos por disponibilidad
 */
function filterProductsByAvailability(availability) {
    if (availability === '') {
        $('.product-item').show();
    } else if (availability === 'available') {
        $('.product-item').hide();
        $('.product-item:not(.unavailable)').show();
    } else if (availability === 'unavailable') {
        $('.product-item').hide();
        $('.product-item.unavailable').show();
    }
}

/**
 * Buscar productos
 */
function searchProducts(query) {
    if (query.length < 2) {
        $('.product-item').show();
        return;
    }
    
    $('.product-item').each(function() {
        const productText = $(this).text().toLowerCase();
        const searchQuery = query.toLowerCase();
        
        if (productText.includes(searchQuery)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

/**
 * Cambiar cantidad
 */
function changeQuantity(button, change) {
    const input = $(button).siblings('.quantity-input');
    const currentValue = parseInt(input.val());
    const newValue = Math.max(1, Math.min(10, currentValue + change));
    input.val(newValue);
}

/**
 * Configurar carrito
 */
function setupCart() {
    // Actualizar display del carrito
    updateCartDisplay();
    
    // Configurar botón de carrito
    $('#cartBtn').on('click', function() {
        loadCartItems();
    });
    
    // Configurar botón de checkout
    $('#checkoutBtn').on('click', function() {
        window.location.href = 'checkout.php';
    });
}

/**
 * Agregar al carrito
 */
function addToCart(productId, quantity = 1) {
    // Usar la función global del carrito si está disponible
    if (typeof window.HorchataCart !== 'undefined' && window.HorchataCart.addToCart) {
        window.HorchataCart.addToCart(productId, quantity);
    } else {
        // Fallback: usar localStorage directamente
        const cart = JSON.parse(localStorage.getItem('horchata_cart') || '[]');
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                quantity: quantity,
                price: 0, // Se actualizará cuando se cargue el producto
                name: '',
                image: ''
            });
        }
        
        localStorage.setItem('horchata_cart', JSON.stringify(cart));
    }
    
    // Mostrar notificación
    showNotification('Producto agregado al carrito', 'success');
    
    // Actualizar display del carrito
    updateCartDisplay();
}

/**
 * Actualizar display del carrito
 */
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('horchata_cart') || '[]');
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    
    // Actualizar contador en el header
    $('#cartCount').text(cartCount);
    
    // Habilitar/deshabilitar botón de checkout
    const checkoutBtn = $('#checkoutBtn');
    if (checkoutBtn.length) {
        checkoutBtn.prop('disabled', cartCount === 0);
    }
}

/**
 * Cargar items del carrito
 */
function loadCartItems() {
    const cart = JSON.parse(localStorage.getItem('horchata_cart') || '[]');
    const container = $('#cartItems');
    
    if (cart.length === 0) {
        container.html(`
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Tu carrito está vacío</p>
            </div>
        `);
        return;
    }
    
    // Cargar detalles de productos
    loadCartProductDetails(cart);
}

/**
 * Cargar detalles de productos del carrito
 */
function loadCartProductDetails(cart) {
    const productIds = cart.map(item => item.id);
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { 
            action: 'get_by_ids',
            ids: productIds.join(',')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCartItems(cart, response.data);
            }
        },
        error: function() {
            console.log('Error al cargar detalles del carrito');
        }
    });
}

/**
 * Mostrar items del carrito
 */
function displayCartItems(cart, products) {
    const container = $('#cartItems');
    let html = '';
    let total = 0;
    
    cart.forEach(function(cartItem) {
        const product = products.find(p => p.id == cartItem.id);
        if (product) {
            const subtotal = product.price * cartItem.quantity;
            total += subtotal;
            
            html += `
                <div class="cart-item d-flex align-items-center mb-3">
                    <img src="${product.image || 'assets/images/placeholder.jpg'}" 
                         alt="${product.name_en}" 
                         class="rounded me-3" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${product.name_en}</h6>
                        <small class="text-muted">Cantidad: ${cartItem.quantity}</small>
                    </div>
                    <div class="text-end">
                        <strong>$${subtotal.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        }
    });
    
    container.html(html);
    $('#cartTotal').text('$' + total.toFixed(2));
}

/**
 * Configurar animaciones
 */
function setupAnimations() {
    // Animaciones al hacer scroll
    $(window).scroll(function() {
        $('.animate-on-scroll').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animated');
            }
        });
    });
}

/**
 * Mostrar loading
 */
function showLoading() {
    $('#productsContainer').html(`
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Cargando productos...</p>
        </div>
    `);
}

/**
 * Ocultar loading
 */
function hideLoading() {
    // El loading se reemplaza automáticamente por los productos
}

/**
 * Mostrar error
 */
function showError(message) {
    $('#productsContainer').html(`
        <div class="col-12 text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h5 class="text-danger">Error</h5>
            <p class="text-muted">${message}</p>
        </div>
    `);
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
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
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
 * Mostrar loading
 */
function showLoading() {
    $('#loadingState').show();
    $('#menuContent').hide();
    $('#emptyState').hide();
}

/**
 * Ocultar loading
 */
function hideLoading() {
    $('#loadingState').hide();
}

/**
 * Mostrar estado vacío
 */
function showEmptyState() {
    $('#emptyState').show();
    $('#menuContent').hide();
}

/**
 * Mostrar error
 */
function showError(message) {
    $('#emptyState').html(`
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4 class="text-warning">Error</h4>
        <p class="text-muted">${message}</p>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-refresh me-2"></i>Reintentar
        </button>
    `).show();
    $('#menuContent').hide();
}

/**
 * Limpiar búsqueda
 */
function clearSearch() {
    $('#searchInput').val('');
    $('.category-filter').removeClass('active');
    $('.category-filter[data-category="all"]').addClass('active');
    loadMenuContent();
}

/**
 * Configurar animaciones avanzadas
 */
function setupAdvancedAnimations() {
    console.log('🎨 Menu: Configurando animaciones avanzadas...');
    
    // Animación de entrada para elementos
    $('.animate-on-scroll').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    // Efecto hover para tarjetas de producto
    $('.product-card').hover(
        function() {
            $(this).addClass('hover-lift');
        },
        function() {
            $(this).removeClass('hover-lift');
        }
    );
    
    // Animación de entrada escalonada para categorías
    $('.menu-category').each(function(index) {
        $(this).css('animation-delay', (index * 0.2) + 's');
        $(this).addClass('fade-in-on-scroll');
    });
    
    // Efecto de escritura para títulos
    setupTypewriterEffect();
    
    // Animaciones de scroll
    setupScrollAnimations();
}

/**
 * Configurar efectos especiales
 */
function setupSpecialEffects() {
    console.log('✨ Menu: Configurando efectos especiales...');
    
    // Efecto de partículas en el hero
    setupParticleEffect();
    
    // Efectos de mouse
    setupMouseEffects();
    
    // Efectos de brillo
    setupShineEffects();
    
    // Efectos de ripple
    setupRippleEffects();
}

/**
 * Efecto de escritura para títulos
 */
function setupTypewriterEffect() {
    const title = $('.menu-hero-title');
    const text = title.text();
    title.text('');
    
    let i = 0;
    const typeWriter = setInterval(() => {
        if (i < text.length) {
            title.text(title.text() + text.charAt(i));
            i++;
        } else {
            clearInterval(typeWriter);
        }
    }, 100);
}

/**
 * Configurar animaciones de scroll
 */
function setupScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

/**
 * Efecto de partículas
 */
function setupParticleEffect() {
    const heroSection = $('.menu-hero-section');
    
    for (let i = 0; i < 20; i++) {
        const particle = $('<div class="particle"></div>');
        particle.css({
            position: 'absolute',
            width: Math.random() * 4 + 2 + 'px',
            height: Math.random() * 4 + 2 + 'px',
            background: '#d4af37',
            borderRadius: '50%',
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animation: `float ${Math.random() * 3 + 2}s ease-in-out infinite`,
            animationDelay: Math.random() * 2 + 's',
            opacity: Math.random() * 0.5 + 0.3
        });
        
        heroSection.append(particle);
    }
}

/**
 * Efectos de mouse
 */
function setupMouseEffects() {
    $('.product-card').on('mousemove', function(e) {
        const card = $(this);
        const rect = card[0].getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;
        
        card.css('transform', `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`);
    });
    
    $('.product-card').on('mouseleave', function() {
        $(this).css('transform', 'perspective(1000px) rotateX(0deg) rotateY(0deg)');
    });
}

/**
 * Efectos de brillo
 */
function setupShineEffects() {
    $('.product-card').on('mouseenter', function() {
        $(this).addClass('shine-effect');
    });
    
    $('.product-card').on('mouseleave', function() {
        $(this).removeClass('shine-effect');
    });
}

/**
 * Efectos de ripple
 */
function setupRippleEffects() {
    $('.category-filter, .add-to-cart-btn').on('click', function(e) {
        const button = $(this);
        const ripple = $('<span class="ripple"></span>');
        
        const rect = button[0].getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.css({
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px'
        });
        
        button.append(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

// Funciones globales para uso en otros archivos
window.MenuPage = {
    loadMenuContent,
    displayMenuContent,
    clearSearch
};

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está disponible');
        return;
    }
    
    // Las funciones ya están definidas arriba
});
