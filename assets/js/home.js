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
    
    // Configurar categorías
    setupCategories();
    
    // Configurar reseñas
    setupReviews();
    
    // Configurar testimonios
    setupTestimonials();
    
    // Configurar animaciones avanzadas
    setupAdvancedAnimations();
    
    // Configurar scroll suave
    setupSmoothScroll();
    
    // Configurar parallax
    setupParallax();
    
    // Configurar efectos de entrada
    setupScrollAnimations();
    
    // Configurar scroll behavior
    setupScrollBehavior();
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
            featured: 1,
            limit: 6
        },
        dataType: 'json',
        success: function(response) {
            console.log('🏠 Home: Respuesta de productos destacados:', response);
            if (response.success) {
                console.log('🏠 Home: Productos cargados:', response.data);
                displayFeaturedProducts(response.data);
            } else {
                console.error('❌ Home: Error en respuesta:', response.message);
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
    console.log('🏠 Home: Mostrando productos destacados:', products);
    const container = $('#featuredProducts');
    if (container.length === 0) return;
    
    if (!products || products.length === 0) {
        container.html('<div class="col-12 text-center"><p class="text-muted">No hay productos destacados disponibles</p></div>');
        return;
    }
    
    let html = '';
    products.forEach(function(product) {
        console.log('🏠 Home: Procesando producto:', product);
        html += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card" onclick="window.location.href='product.php?id=${product.id}'">
                    <div class="product-image" style="background-image: url('${product.image || 'assets/images/placeholder.jpg'}')"></div>
                    <div class="product-info">
                        <h5 class="product-title">${product.name || 'Producto'}</h5>
                        <p class="product-description">${product.description || ''}</p>
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

/**
 * Configurar categorías
 */
function setupCategories() {
    console.log('🏠 Home: Configurando categorías...');
    loadCategories();
}

/**
 * Cargar categorías
 */
function loadCategories() {
    console.log('🏠 Home: Cargando categorías...');
    
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Home: Respuesta de categorías:', response);
            if (response.success) {
                displayCategories(response.data);
            } else {
                console.error('❌ Home: Error en categorías:', response.message);
                showCategoriesError('Error al cargar categorías');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Home: Error AJAX categorías:', error);
            console.error('❌ Home: Status:', status);
            console.error('❌ Home: Response:', xhr.responseText);
            showCategoriesError('Error de conexión al cargar categorías');
        }
    });
}

/**
 * Mostrar categorías
 */
function displayCategories(categories) {
    console.log('🏠 Home: Mostrando categorías:', categories);
    
    const container = $('#categoriesContainer');
    if (container.length === 0) {
        console.error('❌ Home: Container #categoriesContainer no encontrado');
        return;
    }
    
    let html = '';
    
    categories.forEach((category, index) => {
        console.log(`🏠 Home: Procesando categoría ${index}:`, category);
        
        // Mapeo de categorías a imágenes reales
        const categoryImages = {
            1: 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=400&h=300&fit=crop&crop=center', // Breakfast
            2: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&crop=center', // Burritos
            3: 'https://images.unsplash.com/photo-1565299507177-b0ac66773828?w=400&h=300&fit=crop&crop=center', // Specials
            4: 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=400&h=300&fit=crop&crop=center', // Seafood
            5: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop&crop=center', // Special Burritos
            6: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&crop=center', // Combinations
            7: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&crop=center', // Tacos
            8: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop&crop=center', // Desserts
            9: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop&crop=center', // Nachos
            10: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&crop=center' // Salads
        };

        // Iconos específicos para cada categoría
        const categoryIcons = {
            1: 'fas fa-sun', // Breakfast
            2: 'fas fa-bread-slice', // Burritos
            3: 'fas fa-star', // Specials
            4: 'fas fa-fish', // Seafood
            5: 'fas fa-fire', // Special Burritos
            6: 'fas fa-utensils', // Combinations
            7: 'fas fa-taco', // Tacos
            8: 'fas fa-ice-cream', // Desserts
            9: 'fas fa-cheese', // Nachos
            10: 'fas fa-leaf' // Salads
        };

        const categoryImage = categoryImages[category.id] || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop&crop=center';
        const categoryIcon = categoryIcons[category.id] || 'fas fa-utensils';

        html += `
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="category-card text-center" onclick="window.location.href='menu.php?category=${category.id}'">
                    <div class="category-image mb-3">
                        <img src="${categoryImage}" alt="${category.name}" class="img-fluid rounded-3">
                    </div>
                    <div class="category-icon mb-3">
                        <i class="${categoryIcon} fa-2x text-primary"></i>
                    </div>
                    <h5 class="category-title">${category.name}</h5>
                    <p class="category-description">${category.description || ''}</p>
                    <div class="category-count">
                        <span class="badge bg-primary">${category.product_count} productos</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    console.log('🏠 Home: HTML generado para categorías:', html);
    container.html(html);
    console.log('✅ Home: Categorías mostradas');
}

/**
 * Mostrar error de categorías
 */
function showCategoriesError(message) {
    const container = $('#categoriesContainer');
    if (container.length === 0) return;
    
    container.html(`
        <div class="col-12 text-center">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5>Error al Cargar Categorías</h5>
                <p>${message}</p>
                <button class="btn btn-primary" onclick="loadCategories()">
                    <i class="fas fa-refresh me-2"></i>Reintentar
                </button>
            </div>
        </div>
    `);
}

/**
 * Configurar reseñas
 */
function setupReviews() {
    console.log('🏠 Home: Configurando reseñas...');
    loadReviews();
}

/**
 * Cargar reseñas
 */
function loadReviews() {
    console.log('🏠 Home: Cargando reseñas...');
    
    $.ajax({
        url: 'ajax/reviews.ajax.php',
        method: 'GET',
        data: { 
            limit: 6
        },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Home: Respuesta de reseñas:', response);
            if (response.success) {
                displayReviews(response.data);
            } else {
                console.error('❌ Home: Error en reseñas:', response.message);
                showReviewsError('Error al cargar reseñas');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Home: Error AJAX reseñas:', error);
            console.error('❌ Home: Status:', status);
            console.error('❌ Home: Response:', xhr.responseText);
            showReviewsError('Error de conexión al cargar reseñas');
        }
    });
}

/**
 * Mostrar reseñas
 */
function displayReviews(reviews) {
    console.log('🏠 Home: Mostrando reseñas:', reviews);
    
    const container = $('#reviewsContainer');
    if (container.length === 0) {
        console.error('❌ Home: Container #reviewsContainer no encontrado');
        return;
    }
    
    let html = '';
    
    if (reviews.length === 0) {
        html = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-star fa-2x mb-3"></i>
                    <h5>No hay reseñas disponibles</h5>
                    <p>Las reseñas aparecerán aquí cuando los clientes las dejen.</p>
                </div>
            </div>
        `;
    } else {
        reviews.forEach((review, index) => {
            console.log(`🏠 Home: Procesando reseña ${index}:`, review);
            
            // Generar estrellas
            const stars = generateStars(review.rating);
            
            html += `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-rating">
                                ${stars}
                            </div>
                            <div class="review-date">
                                ${formatDate(review.created_at)}
                            </div>
                        </div>
                        <div class="review-content">
                            <p class="review-text">"${review.comment}"</p>
                        </div>
                        <div class="review-footer">
                            <div class="review-author">
                                <strong>${review.customer_name}</strong>
                            </div>
                            <div class="review-verified">
                                <i class="fas fa-check-circle text-success"></i>
                                <small class="text-muted">Verificada</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    console.log('🏠 Home: HTML generado para reseñas:', html);
    container.html(html);
    console.log('✅ Home: Reseñas mostradas');
}

/**
 * Generar estrellas para la calificación
 */
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let stars = '';
    
    // Estrellas llenas
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star text-warning"></i>';
    }
    
    // Media estrella
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    
    // Estrellas vacías
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star text-warning"></i>';
    }
    
    return stars;
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return date.toLocaleDateString('es-ES', options);
}

/**
 * Mostrar error de reseñas
 */
function showReviewsError(message) {
    const container = $('#reviewsContainer');
    if (container.length === 0) return;
    
    container.html(`
        <div class="col-12 text-center">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5>Error al Cargar Reseñas</h5>
                <p>${message}</p>
                <button class="btn btn-primary" onclick="loadReviews()">
                    <i class="fas fa-refresh me-2"></i>Reintentar
                </button>
            </div>
        </div>
    `);
}

/**
 * Configurar animaciones avanzadas
 */
function setupAdvancedAnimations() {
    console.log('🏠 Home: Configurando animaciones avanzadas...');
    
    // Agregar clases de animación a elementos
    $('.product-card').addClass('hover-lift shine-effect');
    $('.category-card').addClass('hover-lift shine-effect');
    $('.review-card').addClass('hover-lift');
    
    // Efectos de ripple en botones
    $('.btn').addClass('ripple-effect');
    
    // Animaciones de entrada escalonadas
    $('.product-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    $('.category-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    $('.review-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
}

/**
 * Configurar efectos de parallax
 */
function setupParallax() {
    console.log('🏠 Home: Configurando parallax...');
    
    // Deshabilitar parallax para evitar superposición
    // Solo mantener animaciones flotantes estáticas
    $('.floating-card').each(function(index) {
        $(this).css('animation', `float ${3 + index}s ease-in-out infinite`);
    });
}

/**
 * Configurar animaciones de scroll
 */
function setupScrollAnimations() {
    console.log('🏠 Home: Configurando animaciones de scroll...');
    
    // Intersection Observer para animaciones de entrada
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Animación específica según el elemento
                if (entry.target.classList.contains('product-card')) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                } else if (entry.target.classList.contains('category-card')) {
                    entry.target.style.animation = 'scaleIn 0.6s ease-out forwards';
                } else if (entry.target.classList.contains('review-card')) {
                    entry.target.style.animation = 'slideInFromBottom 0.6s ease-out forwards';
                }
            }
        });
    }, observerOptions);
    
    // Observar elementos
    $('.product-card, .category-card, .review-card').each(function() {
        observer.observe(this);
    });
}

/**
 * Configurar efectos de mouse
 */
function setupMouseEffects() {
    console.log('🏠 Home: Configurando efectos de mouse...');
    
    // Efecto de seguimiento del mouse en tarjetas
    $('.product-card, .category-card, .review-card').on('mousemove', function(e) {
        const card = $(this);
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;
        
        card.css('transform', `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`);
    });
    
    $('.product-card, .category-card, .review-card').on('mouseleave', function() {
        $(this).css('transform', 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)');
    });
}

/**
 * Configurar animaciones de texto
 */
function setupTextAnimations() {
    console.log('🏠 Home: Configurando animaciones de texto...');
    
    // Animación de escritura para títulos
    $('.hero-title').each(function() {
        const text = $(this).text();
        $(this).html('');
        
        let i = 0;
        const typeWriter = setInterval(function() {
            if (i < text.length) {
                $(this).html($(this).html() + text.charAt(i));
                i++;
            } else {
                clearInterval(typeWriter);
            }
        }.bind(this), 100);
    });
}

/**
 * Configurar efectos de partículas
 */
function setupParticleEffects() {
    console.log('🏠 Home: Configurando efectos de partículas...');
    
    // Crear partículas flotantes en el hero
    const heroSection = $('.hero-section');
    
    for (let i = 0; i < 20; i++) {
        const particle = $('<div class="particle"></div>');
        particle.css({
            position: 'absolute',
            width: Math.random() * 4 + 2 + 'px',
            height: Math.random() * 4 + 2 + 'px',
            background: 'rgba(212, 175, 55, 0.3)',
            borderRadius: '50%',
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animation: `float ${Math.random() * 3 + 2}s ease-in-out infinite`,
            animationDelay: Math.random() * 2 + 's'
        });
        
        heroSection.append(particle);
    }
}

/**
 * Configurar comportamiento de scroll
 */
function setupScrollBehavior() {
    console.log('🏠 Home: Configurando comportamiento de scroll...');
    
    // Asegurar que el scroll funcione correctamente
    $('html, body').css({
        'scroll-behavior': 'smooth',
        'overflow-x': 'hidden'
    });
    
    // Detectar cuando el hero sale de vista y deshabilitar efectos
    $(window).scroll(function() {
        const scrolled = $(window).scrollTop();
        const windowHeight = $(window).height();
        const heroHeight = $('.hero-section').outerHeight();
        
        // Cuando el hero sale completamente de vista
        if (scrolled > heroHeight) {
            $('.hero-section').addClass('hero-out-of-view');
            // Deshabilitar todos los efectos de parallax
            $('.hero-background').css('transform', 'none');
            $('.floating-card').css('transform', 'none');
        } else {
            $('.hero-section').removeClass('hero-out-of-view');
        }
        
        // Asegurar que las secciones tengan z-index correcto
        $('.featured-section, .categories-section, .reviews-section, .about-section').css('z-index', '2');
    });
    
    // Smooth scroll para enlaces internos
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800, 'easeInOutCubic');
        }
    });
}

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está disponible');
        return;
    }
    
    initHomePage();
    
    // Configurar efectos adicionales
    setupMouseEffects();
    setupTextAnimations();
    setupParticleEffects();
});
