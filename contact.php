<?php
/**
 * Horchata Mexican Food - Página de Contacto
 * Información de contacto y formulario
 */

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Configurar página
$page_title = __('contact');
$page_styles = ['assets/css/contact.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Contact Hero Section -->
<section class="contact-hero-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="contact-hero-title mb-4">
                    <i class="fas fa-phone me-3"></i><?php echo __('contact_us_title'); ?>
                </h1>
                <p class="contact-hero-description lead">
                    <?php echo __('we_are_here'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information Section -->
<section class="contact-info-section py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Location -->
            <div class="col-lg-4 col-md-6">
                <div class="contact-card text-center h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-map-marker-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="contact-card-title"><?php echo __('location'); ?></h5>
                    <p class="contact-card-text"><?php echo __('address_full'); ?></p>
                </div>
            </div>
            
            <!-- Phone -->
            <div class="col-lg-4 col-md-6">
                <div class="contact-card text-center h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-phone fa-3x text-primary"></i>
                    </div>
                    <h5 class="contact-card-title"><?php echo __('phone'); ?></h5>
                    <p class="contact-card-text">
                        <?php $phone = getSetting('restaurant_phone', '+1 (310) 204-2659'); $tel = 'tel:' . preg_replace('/[^\d+]/', '', $phone); ?>
                        <a href="<?php echo htmlspecialchars($tel); ?>" class="contact-link"><?php echo htmlspecialchars($phone); ?></a>
                    </p>
                </div>
            </div>
            
            <!-- Hours -->
            <div class="col-lg-4 col-md-6">
                <div class="contact-card text-center h-100">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                    <h5 class="contact-card-title"><?php echo __('hours'); ?></h5>
                    <p class="contact-card-text"><?php echo __('hours_full'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-form-container">
                    <div class="text-center mb-5">
                        <h2 class="form-title"><?php echo __('contact_form_title'); ?></h2>
                        <p class="form-description">
                            <?php echo __('contact_form_desc'); ?>
                        </p>
                    </div>
                    
                    <form id="contactForm" class="contact-form" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contactName" class="form-label"><?php echo __('full_name'); ?></label>
                                <input type="text" class="form-control" id="contactName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label"><?php echo __('email_address'); ?></label>
                                <input type="email" class="form-control" id="contactEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactPhone" class="form-label"><?php echo __('phone_optional'); ?></label>
                                <input type="tel" class="form-control" id="contactPhone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="contactSubject" class="form-label"><?php echo __('subject'); ?></label>
                                <select class="form-select" id="contactSubject" name="subject" required>
                                    <option value=""><?php echo __('select_subject'); ?></option>
                                    <option value="general"><?php echo __('general_inquiry'); ?></option>
                                    <option value="reservation"><?php echo __('reservation'); ?></option>
                                    <option value="complaint"><?php echo __('complaint'); ?></option>
                                    <option value="feedback"><?php echo __('feedback'); ?></option>
                                    <option value="other"><?php echo __('other'); ?></option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contactMessage" class="form-label"><?php echo __('message'); ?></label>
                                <textarea class="form-control" id="contactMessage" name="message" rows="5" required placeholder="<?php echo __('message_placeholder'); ?>"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="contactNewsletter" name="newsletter">
                                    <label class="form-check-label" for="contactNewsletter">
                                        <?php echo __('newsletter_opt_in'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i><?php echo __('send_message'); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mb-4"><?php echo __('our_location'); ?></h3>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3306.123456789!2d-118.3965!3d34.0123!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c2b8b8b8b8b8b8%3A0x1234567890abcdef!2s10814%20Jefferson%20Blvd%2C%20Culver%20City%2C%20CA%2090232!5e0!3m2!1sen!2sus!4v1234567890"
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Info Section -->
<section class="additional-info-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="info-card">
                    <h4 class="info-card-title">
                        <i class="fas fa-utensils me-2"></i><?php echo __('pickup_service'); ?>
                    </h4>
                    <p class="info-card-text">
                        <?php echo __('pickup_service_desc'); ?>
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="info-card">
                    <h4 class="info-card-title">
                        <i class="fas fa-heart me-2"></i><?php echo __('quality_commitment'); ?>
                    </h4>
                    <p class="info-card-text">
                        <?php echo __('quality_commitment_desc'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Esperar a que jQuery esté disponible
(function() {
    function initContactForm() {
        if (typeof jQuery === 'undefined') {
            // jQuery aún no está cargado, esperar un poco más
            setTimeout(initContactForm, 50);
            return;
        }
        
        // jQuery está disponible, ejecutar código
        jQuery(document).ready(function($) {
    // Manejar envío del formulario de contacto
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        // Obtener datos del formulario
        const formData = {
            name: $('#contactName').val().trim(),
            email: $('#contactEmail').val().trim(),
            phone: $('#contactPhone').val().trim(),
            subject: $('#contactSubject').val().trim(),
            message: $('#contactMessage').val().trim(),
            newsletter: $('#contactNewsletter').is(':checked') ? 'on' : ''
        };
        
        // Validar campos
        if (!formData.name || !formData.email || !formData.subject || !formData.message) {
            showNotification('<?php echo __('please_complete_fields'); ?>', 'error');
            return;
        }
        
        // Validar email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showNotification('<?php echo __('invalid_email'); ?>', 'error');
            return;
        }
        
        // Deshabilitar botón mientras se envía
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i><?php echo __('sending'); ?>...');
        
        // Enviar datos via AJAX
        $.ajax({
            url: 'ajax/contact.ajax.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    showNotification(response.message || 'Mensaje enviado exitosamente', 'success');
                    
                    // Limpiar formulario
                    $('#contactForm')[0].reset();
                    
                    // Ocultar formulario y mostrar mensaje de confirmación
                    $('.contact-form-container').html(
                        '<div class="alert alert-success text-center">' +
                        '<i class="fas fa-check-circle fa-3x mb-3"></i>' +
                        '<h4><?php echo __('message_sent_title'); ?></h4>' +
                        '<p><?php echo __('message_sent_text'); ?></p>' +
                        '</div>'
                    );
                } else {
                    showNotification(response.message || 'Error al enviar el mensaje', 'error');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showNotification('<?php echo __('connection_error'); ?>', 'error');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Función para mostrar notificaciones
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
        }); // Cerrar jQuery(document).ready
    } // Cerrar initContactForm
    
    // Iniciar la función
    initContactForm();
})(); // Cerrar IIFE
</script>

<?php include 'includes/footer.php'; ?>
