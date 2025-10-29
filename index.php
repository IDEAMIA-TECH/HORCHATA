<?php
/**
 * Horchata Mexican Food - Página Principal
 * Diseño inspirado en Trattoria la Pasta
 */

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Configurar página
$page_title = 'Inicio';
$page_scripts = ['assets/js/home.js'];

// Incluir header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="hero-badge mb-4">
                            <span class="badge bg-primary-custom px-3 py-2">
                                <i class="fas fa-star me-2"></i><?php echo __('authentic_cooking'); ?>
                            </span>
                        </div>
                                <h1 class="hero-title animate-on-scroll">
                                    <?php echo __('flavors_conquer'); ?>
                                </h1>
                                <p class="hero-description animate-on-scroll">
                                    <?php echo __('discover_magic'); ?>
                                </p>
                        <div class="hero-stats mb-4">
                            <div class="row">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">8+</h3>
                                        <p class="stat-label"><?php echo __('years'); ?></p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">1000+</h3>
                                        <p class="stat-label"><?php echo __('clients'); ?></p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">150+</h3>
                                        <p class="stat-label"><?php echo __('dishes'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hero-buttons animate-on-scroll">
                            <a href="menu.php" class="btn btn-primary-custom btn-lg me-3 shadow-lg">
                                <i class="fas fa-utensils me-2"></i><?php echo __('explore_menu'); ?>
                            </a>
                            <a href="#featured" class="btn btn-outline-light btn-lg shadow-lg">
                                <i class="fas fa-star me-2"></i><?php echo __('specialties'); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container">
                        <div class="hero-image-main">
                            <img src="assets/images/hero/mexican-food-from-the-state-horchata-scaled-e1742425754315.jpg" alt="Plato mexicano" class="img-fluid rounded-4 shadow-xl">
                        </div>
                        <div class="hero-floating-cards">
                            <div class="floating-card card-1 hover-3d pulse-effect">
                                <i class="fas fa-fire text-warning"></i>
                                <span><?php echo __('spicy'); ?></span>
                            </div>
                            <div class="floating-card card-2 hover-3d pulse-effect">
                                <i class="fas fa-leaf text-success"></i>
                                <span><?php echo __('fresh'); ?></span>
                            </div>
                            <div class="floating-card card-3 hover-3d pulse-effect">
                                <i class="fas fa-heart text-danger"></i>
                                <span><?php echo __('made_with_love'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured" class="featured-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <div class="section-badge mb-3">
                <span class="badge bg-primary-custom px-3 py-2">
                    <i class="fas fa-star me-2"></i>Especialidades
                </span>
            </div>
            <h2 class="section-title"><?php echo __('our_specialties'); ?></h2>
            <p class="section-description">
                <?php echo __('popular_dishes_description'); ?>
            </p>
        </div>
        
        <div class="row g-4" id="featuredProducts">
            <!-- Productos destacados se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando especialidades...</span>
                    </div>
                    <p class="mt-3 text-muted"><?php echo __('loading_specialties'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="menu.php" class="btn btn-primary-custom btn-lg px-5 py-3 shadow-lg">
                <i class="fas fa-utensils me-2"></i><?php echo __('explore_full_menu'); ?>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content">
                    <h2 class="mb-4"><?php echo __('our_story'); ?></h2>
                    <p class="lead">
                        <?php echo __('since_2015'); ?>
                    </p>
                    <p>
                        <?php echo __('fresh_ingredients'); ?>
                    </p>
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="text-primary">8+</h3>
                                <p class="text-muted"><?php echo __('years_experience'); ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="text-primary">1000+</h3>
                                <p class="text-muted"><?php echo __('satisfied_clients'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image text-center">
                    <img src="assets/images/LOGO.JPG" alt="Horchata Mexican Food Logo" class="img-fluid rounded-3 shadow" style="max-height: 400px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <div class="section-badge mb-3">
                <span class="badge bg-primary-custom px-3 py-2">
                    <i class="fas fa-utensils me-2"></i><?php echo __('categories'); ?>
                </span>
            </div>
            <h2 class="section-title"><?php echo __('our_categories'); ?></h2>
            <p class="section-description">
                <?php echo __('explore_variety'); ?>
            </p>
        </div>
        
        <div class="row g-4" id="categoriesContainer">
            <!-- Categorías se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando categorías...</span>
                    </div>
                    <p class="mt-3 text-muted"><?php echo __('loading_categories'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <div class="section-badge mb-3">
                <span class="badge bg-primary-custom px-3 py-2">
                    <i class="fas fa-star me-2"></i><?php echo __('testimonials'); ?>
                </span>
            </div>
            <h2 class="section-title"><?php echo __('what_customers_say'); ?></h2>
            <p class="section-description">
                <?php echo __('verified_reviews'); ?>
            </p>
        </div>
        
        <div class="row g-4" id="reviewsContainer">
            <!-- Reseñas se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando testimonios...</span>
                    </div>
                    <p class="mt-3 text-muted"><?php echo __('loading_testimonials'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="reviews.php" class="btn btn-primary-custom btn-lg px-5 py-3 shadow-lg">
                <i class="fas fa-star me-2"></i><?php echo __('see_all_reviews'); ?>
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container">
        <div class="section-header">
            <h2><?php echo __('contact_us_title'); ?></h2>
            <p><?php echo __('we_are_here'); ?></p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                    </div>
                    <h5><?php echo __('location'); ?></h5>
                    <p class="text-muted"><?php echo __('address_full'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-phone fa-2x text-primary"></i>
                    </div>
                    <h5><?php echo __('phone'); ?></h5>
                    <p class="text-muted"><?php echo __('phone_number'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-clock fa-2x text-primary"></i>
                    </div>
                    <h5><?php echo __('hours'); ?></h5>
                    <p class="text-muted"><?php echo __('hours_full'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript se carga desde assets/js/home.js -->

<?php
// Incluir footer
include 'includes/footer.php';
?>
