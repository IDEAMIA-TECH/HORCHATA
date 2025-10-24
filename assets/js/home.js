/**
 * Home Page JavaScript
 * Horchata Mexican Food - Página Principal
 */

$(document).ready(function() {
    // Inicializar funcionalidades de la página principal
    initHomePage();
});

function initHomePage() {
    // Configurar hero section
    setupHeroSection();
    
    // Configurar productos destacados
    setupFeaturedProducts();
    
    // Configurar testimonios
    setupTestimonials();
    
    // Configurar animaciones
    setupAnimations();
    
    // Configurar scroll suave
    setupSmoothScroll();
}

/**
 * Configurar hero section
 */
function setupHeroSection() {
    // Auto-rotar imágenes del hero si hay múltiples
    const heroImages = $('.hero-image');
    if (heroImages.length > 1) {
        let currentImage = 0;
        setInterval(function() {
            heroImages.removeClass('active');
            currentImage = (currentImage + 1) % heroImages.length;
            heroImages.eq(currentImage).addClass('active');
        }, 5000);
    }
    
    // Parallax effect para hero
    $(window).scroll(function() {
        const scrolled = $(window).scrollTop();
        const parallax = $('.hero-section');
        const speed = scrolled * 0.5;
        parallax.css('transform', 'translateY(' + speed + 'px)');
    });
}

/**
 * Configurar productos destacados
 */
function setupFeaturedProducts() {
    // Cargar productos destacados dinámicamente
    loadFeaturedProducts();
    
    // Configurar hover effects
    $('.product-card').hover(
        function() {
            $(this).addClass('hover-effect');
        },
        function() {
            $(this).removeClass('hover-effect');
        }
    );
}

/**
 * Cargar productos destacados
 */
function loadFeaturedProducts() {
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { 
            action: 'get_featured',
            limit: 6
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayFeaturedProducts(response.data);
            }
        },
        error: function() {
            console.log('Error al cargar productos destacados');
        }
    });
}

/**
 * Mostrar productos destacados
 */
function displayFeaturedProducts(products) {
    const container = $('#featured-products');
    if (container.length === 0) return;
    
    let html = '';
    products.forEach(function(product) {
        html += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card" onclick="window.location.href='product.php?id=${product.id}'">
                    <div class="product-image" style="background-image: url('${product.image || 'assets/images/placeholder.jpg'}')"></div>
                    <div class="product-info">
                        <h5 class="product-title">${product.name_en}</h5>
                        <p class="product-description">${product.description_en || ''}</p>
                        <div class="product-price">
                            <span class="price">$${parseFloat(product.price).toFixed(2)}</span>
                        </div>
                        <button class="btn btn-primary btn-sm add-to-cart" data-product-id="${product.id}">
                            <i class="fas fa-shopping-cart me-1"></i>Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
    
    // Configurar botones de agregar al carrito
    $('.add-to-cart').on('click', function(e) {
        e.stopPropagation();
        const productId = $(this).data('product-id');
        addToCart(productId, 1);
    });
}

/**
 * Configurar testimonios
 */
function setupTestimonials() {
    // Auto-rotar testimonios
    const testimonials = $('.testimonial-item');
    if (testimonials.length > 1) {
        let currentTestimonial = 0;
        setInterval(function() {
            testimonials.removeClass('active');
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            testimonials.eq(currentTestimonial).addClass('active');
        }, 4000);
    }
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
 * Configurar scroll suave
 */
function setupSmoothScroll() {
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
        
        // Mostrar notificación
        showNotification('Producto agregado al carrito', 'success');
        
        // Actualizar contador del carrito
        updateCartDisplay();
    }
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

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está disponible');
        return;
    }
    
    initHomePage();
});
