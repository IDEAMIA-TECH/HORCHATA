/**
 * Reviews Public Page JavaScript
 * Horchata Mexican Food - Página de Reseñas Públicas
 */

$(document).ready(function() {
    // Inicializar funcionalidades de la página de reseñas
    initReviewsPublicPage();
});

function initReviewsPublicPage() {
    console.log('🌟 Reviews Public: Inicializando página de reseñas públicas...');
    
    // Cargar reseñas del sitio
    loadSiteReviews();
    
    // Configurar tabs
    setupTabs();
    
    // Configurar animaciones
    setupAnimations();
    
    // Configurar Yelp integration
    setupYelpIntegration();
}

/**
 * Cargar reseñas del sitio
 */
function loadSiteReviews() {
    console.log('🌟 Reviews Public: Cargando reseñas del sitio...');
    
    $.ajax({
        url: 'ajax/reviews.ajax.php',
        method: 'GET',
        data: { 
            action: 'get_public_reviews',
            limit: 12
        },
        dataType: 'json',
        success: function(response) {
            console.log('🌟 Reviews Public: Respuesta de reseñas:', response);
            if (response.success) {
                displaySiteReviews(response.data);
                updateReviewStats(response.stats);
            } else {
                showSiteReviewsError(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Reviews Public: Error al cargar reseñas:', error);
            showSiteReviewsError('Error al cargar las reseñas');
        }
    });
}

/**
 * Mostrar reseñas del sitio
 */
function displaySiteReviews(reviews) {
    console.log('🌟 Reviews Public: Mostrando reseñas:', reviews);
    const container = $('#siteReviewsContainer');
    
    if (!reviews || reviews.length === 0) {
        container.html(`
            <div class="col-12 text-center">
                <div class="empty-state">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay reseñas disponibles</h4>
                    <p class="text-muted">Sé el primero en dejar una reseña</p>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '';
    reviews.forEach(function(review) {
        html += `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            ${getInitials(review.customer_name || 'Cliente')}
                        </div>
                        <div class="review-info">
                            <h5>${review.customer_name || 'Cliente Anónimo'}</h5>
                            <div class="review-date">${formatDate(review.created_at)}</div>
                        </div>
                    </div>
                    
                    <div class="review-rating">
                        <div class="review-stars">
                            ${generateStars(review.rating)}
                        </div>
                        <span class="review-rating-text">${review.rating}/5</span>
                    </div>
                    
                    <div class="review-content">
                        ${review.comments || 'Sin comentarios adicionales'}
                    </div>
                    
                    <div class="review-aspects">
                        ${generateAspects(review)}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

/**
 * Actualizar estadísticas de reseñas
 */
function updateReviewStats(stats) {
    console.log('🌟 Reviews Public: Actualizando estadísticas:', stats);
    
    if (stats) {
        $('#totalReviews').text(stats.total_reviews || 0);
    }
}

/**
 * Mostrar error en reseñas del sitio
 */
function showSiteReviewsError(message) {
    const container = $('#siteReviewsContainer');
    container.html(`
        <div class="col-12 text-center">
            <div class="error-state">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4 class="text-warning">Error al cargar reseñas</h4>
                <p class="text-muted">${message}</p>
                <button class="btn btn-primary" onclick="loadSiteReviews()">
                    <i class="fas fa-refresh me-2"></i>Intentar de nuevo
                </button>
            </div>
        </div>
    `);
}

/**
 * Configurar tabs
 */
function setupTabs() {
    console.log('🌟 Reviews Public: Configurando tabs...');
    
    // Configurar eventos de tabs
    $('#reviewsTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        console.log('🌟 Reviews Public: Tab activado:', target);
        
        if (target === '#yelp-reviews') {
            // Cargar widget de Yelp cuando se active el tab
            loadYelpWidget();
        }
    });
}

/**
 * Configurar integración con Yelp
 */
function setupYelpIntegration() {
    console.log('🌟 Reviews Public: Configurando integración con Yelp...');
    
    // Aquí se puede agregar la lógica para cargar el widget oficial de Yelp
    // Por ahora, mostramos un placeholder
}

/**
 * Cargar widget de Yelp
 */
function loadYelpWidget() {
    console.log('🌟 Reviews Public: Cargando widget de Yelp...');
    
    // Aquí se implementaría la carga del widget oficial de Yelp
    // Por ahora, mostramos un mensaje informativo
    const placeholder = $('.yelp-widget-placeholder');
    placeholder.html(`
        <div class="yelp-widget-info">
            <i class="fab fa-yelp fa-3x text-warning mb-3"></i>
            <h4>Widget de Yelp</h4>
            <p class="text-muted mb-3">
                Para implementar el widget oficial de Yelp, sigue estos pasos:
            </p>
            <ol class="text-start text-muted">
                <li>Ve a <a href="https://biz.yelp.com" target="_blank">Yelp for Business</a></li>
                <li>Inicia sesión con la cuenta del negocio</li>
                <li>Ve a 'Marketing' → 'Review Widget'</li>
                <li>Copia el código del widget</li>
                <li>Pega el código aquí</li>
            </ol>
            <div class="mt-3">
                <a href="https://www.yelp.com/biz/horchata-mexican-food-culver-city" target="_blank" class="btn btn-warning">
                    <i class="fab fa-yelp me-2"></i>Ver en Yelp
                </a>
            </div>
        </div>
    `);
}

/**
 * Configurar animaciones
 */
function setupAnimations() {
    console.log('🌟 Reviews Public: Configurando animaciones...');
    
    // Animaciones de entrada para las tarjetas de reseñas
    $('.review-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
}

/**
 * Generar estrellas
 */
function generateStars(rating) {
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return stars;
}

/**
 * Generar aspectos de la reseña
 */
function generateAspects(review) {
    const aspects = [];
    
    if (review.food_quality) {
        aspects.push(`<span class="review-aspect">Comida: ${review.food_quality}/5</span>`);
    }
    
    if (review.service) {
        aspects.push(`<span class="review-aspect">Servicio: ${review.service}/5</span>`);
    }
    
    if (review.presentation) {
        aspects.push(`<span class="review-aspect">Presentación: ${review.presentation}/5</span>`);
    }
    
    if (review.recommend) {
        aspects.push(`<span class="review-aspect">Recomienda: Sí</span>`);
    }
    
    return aspects.join('');
}

/**
 * Obtener iniciales del nombre
 */
function getInitials(name) {
    return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2);
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
