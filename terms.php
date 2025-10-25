<?php
/**
 * Horchata Mexican Food - Términos y Condiciones
 */

// Incluir configuración
require_once 'includes/init.php';

// Configurar página
$page_title = __('terms_conditions_title');
$page_styles = ['assets/css/terms.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Terms Section -->
<section class="terms-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="terms-content">
                    <h1 class="terms-title mb-4">
                        <i class="fas fa-file-contract me-3"></i><?php echo __('terms_conditions_title'); ?>
                    </h1>
                    
                    <div class="terms-intro mb-5">
                        <p class="lead">
                            <?php echo __('terms_intro'); ?>
                        </p>
                    </div>
                    
                    <div class="terms-sections">
                        <!-- Section 1 -->
                        <div class="terms-section-item mb-5">
                            <h2 class="section-title">
                                <span class="section-number">1</span>
                                <?php echo __('website_use'); ?>
                            </h2>
                            <div class="section-content">
                                <p>
                                    <?php echo __('website_use_description'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Section 2 -->
                        <div class="terms-section-item mb-5">
                            <h2 class="section-title">
                                <span class="section-number">2</span>
                                <?php echo __('intellectual_property'); ?>
                            </h2>
                            <div class="section-content">
                                <p>
                                    <?php echo __('intellectual_property_description'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Section 3 -->
                        <div class="terms-section-item mb-5">
                            <h2 class="section-title">
                                <span class="section-number">3</span>
                                <?php echo __('disclaimer'); ?>
                            </h2>
                            <div class="section-content">
                                <p>
                                    <?php echo __('disclaimer_description'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="terms-footer mt-5">
                        <div class="contact-info">
                            <h3><?php echo __('contact_information'); ?></h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span>10814 Jefferson Blvd, Culver City, CA</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <i class="fas fa-phone me-2"></i>
                                        <span>+1 (310) 204-2659</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <i class="fas fa-envelope me-2"></i>
                                        <span>contact@horchatamexicanfood.com</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <i class="fas fa-clock me-2"></i>
                                        <span>Lun-Sáb: 8:30 AM - 9:00 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
