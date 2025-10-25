<?php
/**
 * Horchata Mexican Food - Accesibilidad WCAG
 */

// Incluir configuración
require_once 'includes/init.php';

// Configurar página
$page_title = __('accessibility');
$page_styles = ['assets/css/accessibility.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Accessibility Section -->
<section class="accessibility-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accessibility-content">
                    <h1 class="accessibility-title mb-4">
                        <i class="fas fa-universal-access me-3"></i><?php echo __('accessibility_title'); ?>
                    </h1>
                    
                    <div class="accessibility-intro mb-5">
                        <p class="lead">
                            <?php echo __('accessibility_intro'); ?>
                        </p>
                    </div>
                    
                    <div class="accessibility-sections">
                        <!-- WCAG Commitment -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-heart me-2"></i><?php echo __('our_commitment'); ?>
                            </h2>
                            <div class="section-content">
                                <p>
                                    <?php echo __('our_commitment_description'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Measures Implemented -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-tools me-2"></i><?php echo __('measures_implemented'); ?>
                            </h2>
                            <div class="section-content">
                                <div class="measures-grid">
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-palette"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4><?php echo __('adequate_color_contrast'); ?></h4>
                                            <p><?php echo __('adequate_color_contrast_description'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4><?php echo __('alt_text_images'); ?></h4>
                                            <p><?php echo __('alt_text_images_description'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-keyboard"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4><?php echo __('screen_reader_navigation'); ?></h4>
                                            <p><?php echo __('screen_reader_navigation_description'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4><?php echo __('responsive_design'); ?></h4>
                                            <p><?php echo __('responsive_design_description'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Features -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-star me-2"></i><?php echo __('additional_features'); ?>
                            </h2>
                            <div class="section-content">
                                <ul class="features-list">
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('semantic_html5'); ?></li>
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('descriptive_links'); ?></li>
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('accessible_forms'); ?></li>
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('keyboard_navigation'); ?></li>
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('scalable_text'); ?></li>
                                    <li><i class="fas fa-check-circle me-2"></i><?php echo __('hierarchical_content'); ?></li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Contact for Accessibility Issues -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-envelope me-2"></i><?php echo __('report_accessibility_issues'); ?>
                            </h2>
                            <div class="section-content">
                                <p>
                                    <?php echo __('report_accessibility_description'); ?>
                                </p>
                                <div class="contact-methods">
                                    <div class="contact-method">
                                        <i class="fas fa-phone me-2"></i>
                                        <span>+1 (310) 204-2659</span>
                                    </div>
                                    <div class="contact-method">
                                        <i class="fas fa-envelope me-2"></i>
                                        <span>contact@horchatamexicanfood.com</span>
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
