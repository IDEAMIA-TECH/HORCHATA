/**
 * Reviews JavaScript
 * Horchata Mexican Food - Sistema de reseñas
 */

$(document).ready(function() {
    console.log('⭐ Reviews: Página cargada');
    
    // Inicializar funcionalidades
    initReviews();
});

function initReviews() {
    console.log('⭐ Reviews: Inicializando...');
    
    // Configurar sistema de calificaciones
    setupRatingSystem();
    
    // Configurar formulario
    setupReviewForm();
    
    // Configurar animaciones
    setupAnimations();
    
    // Configurar validaciones
    setupValidations();
}

/**
 * Configurar sistema de calificaciones
 */
function setupRatingSystem() {
    console.log('⭐ Reviews: Configurando sistema de calificaciones...');
    
    // Calificación principal
    $('.star-rating').on('click', 'i', function() {
        const rating = parseInt($(this).data('value'));
        const starRating = $(this).parent();
        const aspect = starRating.data('aspect');
        
        console.log('⭐ Reviews: Calificación seleccionada:', rating, 'Aspecto:', aspect);
        
        // Actualizar estrellas visuales
        starRating.find('i').each(function(index) {
            if (index < rating) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
        
        // Actualizar input hidden
        if (aspect) {
            starRating.siblings('input[name="' + aspect + '"]').val(rating);
        } else {
            $('#rating').val(rating);
            updateRatingText(rating);
        }
        
        // Animación de selección
        $(this).addClass('selected');
        setTimeout(() => {
            $(this).removeClass('selected');
        }, 200);
    });
    
    // Hover effects para estrellas
    $('.star-rating').on('mouseenter', 'i', function() {
        const rating = parseInt($(this).data('value'));
        const starRating = $(this).parent();
        
        starRating.find('i').each(function(index) {
            if (index < rating) {
                $(this).addClass('hover');
            } else {
                $(this).removeClass('hover');
            }
        });
    });
    
    $('.star-rating').on('mouseleave', 'i', function() {
        $(this).parent().find('i').removeClass('hover');
    });
}

/**
 * Actualizar texto de calificación
 */
function updateRatingText(rating) {
    const texts = {
        1: 'Muy malo',
        2: 'Malo',
        3: 'Regular',
        4: 'Bueno',
        5: 'Excelente'
    };
    
    $('#ratingText').text(texts[rating] || 'Selecciona una calificación');
}

/**
 * Configurar formulario de reseña
 */
function setupReviewForm() {
    console.log('⭐ Reviews: Configurando formulario...');
    
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        console.log('⭐ Reviews: Enviando reseña...');
        
        if (validateReviewForm()) {
            submitReview();
        } else {
            showNotification('Por favor, completa todos los campos requeridos', 'error');
        }
    });
}

/**
 * Validar formulario de reseña
 */
function validateReviewForm() {
    let isValid = true;
    
    // Verificar calificación principal
    const mainRating = parseInt($('#rating').val());
    if (mainRating === 0) {
        showNotification('Por favor, selecciona una calificación general', 'error');
        isValid = false;
    }
    
    // Verificar que al menos un aspecto esté calificado
    const aspects = ['food_quality', 'preparation_time', 'presentation', 'service'];
    let hasAspectRating = false;
    
    aspects.forEach(aspect => {
        const rating = parseInt($(`input[name="${aspect}"]`).val());
        if (rating > 0) {
            hasAspectRating = true;
        }
    });
    
    if (!hasAspectRating) {
        showNotification('Por favor, califica al menos un aspecto específico', 'error');
        isValid = false;
    }
    
    // Verificar recomendación
    const recommendation = $('input[name="recommend"]:checked').length;
    if (recommendation === 0) {
        showNotification('Por favor, indica si recomendarías nuestro restaurante', 'error');
        isValid = false;
    }
    
    return isValid;
}

/**
 * Enviar reseña
 */
function submitReview() {
    console.log('⭐ Reviews: Enviando reseña al servidor...');
    
    // Mostrar loading
    showLoadingModal();
    
    // Recopilar datos del formulario
    const formData = {
        order_id: $('input[name="order_id"]').val(),
        token: $('input[name="token"]').val(),
        rating: parseInt($('#rating').val()),
        food_quality: parseInt($('input[name="food_quality"]').val()),
        preparation_time: parseInt($('input[name="preparation_time"]').val()),
        presentation: parseInt($('input[name="presentation"]').val()),
        service: parseInt($('input[name="service"]').val()),
        comments: $('#comments').val().trim(),
        recommend: $('input[name="recommend"]:checked').val()
    };
    
    console.log('⭐ Reviews: Datos a enviar:', formData);
    
    $.ajax({
        url: 'ajax/reviews.ajax.php',
        method: 'POST',
        data: {
            action: 'submit_review',
            review_data: JSON.stringify(formData)
        },
        dataType: 'json',
        success: function(response) {
            console.log('⭐ Reviews: Respuesta del servidor:', response);
            hideLoadingModal();
            
            if (response.success) {
                showSuccessMessage();
            } else {
                showNotification('Error al enviar la reseña: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Reviews: Error AJAX:', error);
            hideLoadingModal();
            showNotification('Error de conexión: ' + error, 'error');
        }
    });
}

/**
 * Mostrar mensaje de éxito
 */
function showSuccessMessage() {
    const successHtml = `
        <div class="review-already-submitted">
            <div class="text-center">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h2 class="mb-3">¡Gracias por tu Reseña!</h2>
                <p class="lead mb-4">
                    Tu reseña ha sido enviada exitosamente. 
                    Nos ayuda mucho a mejorar nuestro servicio.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php" class="btn btn-primary-custom">
                        <i class="fas fa-home me-2"></i>Volver al Inicio
                    </a>
                    <a href="menu.php" class="btn btn-outline-primary-custom">
                        <i class="fas fa-utensils me-2"></i>Hacer Otro Pedido
                    </a>
                </div>
            </div>
        </div>
    `;
    
    $('.review-form-container').html(successHtml);
    
    // Animación de entrada
    $('.success-icon').addClass('animate__animated animate__bounceIn');
}

/**
 * Configurar animaciones
 */
function setupAnimations() {
    console.log('⭐ Reviews: Configurando animaciones...');
    
    // Animación de entrada para elementos
    $('.rating-section, .aspects-section, .comments-section, .recommendation-section').each(function(index) {
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
    
    // Animación del icono
    $('.review-icon').addClass('animate__animated animate__bounceIn');
}

/**
 * Configurar validaciones
 */
function setupValidations() {
    console.log('⭐ Reviews: Configurando validaciones...');
    
    // Validación en tiempo real para comentarios
    $('#comments').on('input', function() {
        const length = $(this).val().length;
        const maxLength = 500;
        
        if (length > maxLength) {
            $(this).val($(this).val().substring(0, maxLength));
        }
        
        // Mostrar contador de caracteres
        if (!$(this).siblings('.char-counter').length) {
            $(this).after('<div class="char-counter form-text"></div>');
        }
        
        $(this).siblings('.char-counter').text(`${length}/${maxLength} caracteres`);
    });
}

/**
 * Mostrar modal de carga
 */
function showLoadingModal() {
    $('#loadingModal').modal('show');
}

/**
 * Ocultar modal de carga
 */
function hideLoadingModal() {
    $('#loadingModal').modal('hide');
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
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        $('.alert').fadeOut(() => {
            $('.alert').remove();
        });
    }, 5000);
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

// Exportar funciones para uso global
window.Reviews = {
    showNotification,
    formatDate,
    showLoadingModal,
    hideLoadingModal
};
