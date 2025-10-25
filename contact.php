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
                        <a href="tel:+13102042659" class="contact-link"><?php echo __('phone_number'); ?></a>
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
                        <h2 class="form-title">Envíanos un Mensaje</h2>
                        <p class="form-description">
                            ¿Tienes alguna pregunta o sugerencia? Nos encantaría escucharte.
                        </p>
                    </div>
                    
                    <form id="contactForm" class="contact-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contactName" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="contactName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="contactEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contactPhone" class="form-label">Teléfono (Opcional)</label>
                                <input type="tel" class="form-control" id="contactPhone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="contactSubject" class="form-label">Asunto</label>
                                <select class="form-select" id="contactSubject" name="subject" required>
                                    <option value="">Selecciona un asunto</option>
                                    <option value="general">Consulta General</option>
                                    <option value="reservation">Reservación</option>
                                    <option value="complaint">Queja o Sugerencia</option>
                                    <option value="feedback">Comentarios</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contactMessage" class="form-label">Mensaje</label>
                                <textarea class="form-control" id="contactMessage" name="message" rows="5" required placeholder="Escribe tu mensaje aquí..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="contactNewsletter" name="newsletter">
                                    <label class="form-check-label" for="contactNewsletter">
                                        Quiero recibir noticias y promociones por correo electrónico
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
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
                <h3 class="text-center mb-4">Nuestra Ubicación</h3>
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
                        <i class="fas fa-utensils me-2"></i>Servicio Pickup
                    </h4>
                    <p class="info-card-text">
                        Ofrecemos servicio de pickup para que puedas disfrutar de nuestros platillos 
                        auténticos en la comodidad de tu hogar.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="info-card">
                    <h4 class="info-card-title">
                        <i class="fas fa-heart me-2"></i>Compromiso con la Calidad
                    </h4>
                    <p class="info-card-text">
                        Utilizamos solo ingredientes frescos y recetas tradicionales para brindarte 
                        la mejor experiencia culinaria mexicana.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
