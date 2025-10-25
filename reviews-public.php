<?php
/**
 * Horchata Mexican Food - Reseñas Públicas
 * Muestra reseñas del sitio y widget de Yelp
 */

// Incluir configuración
require_once 'includes/db_connect.php';

// Configurar página
$page_title = 'Reseñas';
$page_scripts = ['assets/js/reviews-public.js'];
$page_styles = ['assets/css/reviews-public.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Reviews Hero Section -->
<section class="reviews-hero-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="reviews-hero-title mb-4">
                    <i class="fas fa-star me-3"></i>Lo que Dicen Nuestros Clientes
                </h1>
                <p class="reviews-hero-description lead">
                    Descubre las experiencias auténticas de nuestros clientes y únete a la comunidad 
                    de amantes de la comida mexicana tradicional.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Tabs Section -->
<section class="reviews-tabs-section py-5">
    <div class="container">
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs reviews-tabs mb-4" id="reviewsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="site-reviews-tab" data-bs-toggle="tab" data-bs-target="#site-reviews" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i>Reseñas del Sitio
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="yelp-reviews-tab" data-bs-toggle="tab" data-bs-target="#yelp-reviews" type="button" role="tab">
                    <i class="fab fa-yelp me-2"></i>Reseñas de Yelp
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reviewsTabsContent">
            <!-- Site Reviews Tab -->
            <div class="tab-pane fade show active" id="site-reviews" role="tabpanel">
                <div class="row g-4" id="siteReviewsContainer">
                    <!-- Reseñas del sitio se cargarán aquí via AJAX -->
                    <div class="col-12 text-center">
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary-custom" role="status">
                                <span class="visually-hidden">Cargando reseñas...</span>
                            </div>
                            <p class="mt-3 text-muted">Recopilando las mejores experiencias</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yelp Reviews Tab -->
            <div class="tab-pane fade" id="yelp-reviews" role="tabpanel">
                <div class="yelp-integration">
                    <!-- Yelp Widget Placeholder -->
                    <div class="yelp-widget-container">
                        <div class="yelp-widget-header">
                            <h3 class="yelp-widget-title">
                                <i class="fab fa-yelp me-2"></i>Reseñas en Yelp
                            </h3>
                            <p class="yelp-widget-description">
                                Ve todas nuestras reseñas y calificaciones en Yelp
                            </p>
                        </div>
                        
                        <!-- Yelp Widget (se implementará con el código oficial) -->
                        <div class="yelp-widget-placeholder">
                            <div class="yelp-placeholder-content">
                                <i class="fab fa-yelp fa-3x text-warning mb-3"></i>
                                <h4>Widget de Yelp</h4>
                                <p class="text-muted">
                                    Aquí se mostrará el widget oficial de Yelp con las reseñas más recientes.
                                </p>
                                <div class="yelp-actions">
                                    <a href="https://www.yelp.com/biz/horchata-mexican-food-culver-city" target="_blank" class="btn btn-warning">
                                        <i class="fab fa-yelp me-2"></i>Ver en Yelp
                                    </a>
                                    <button class="btn btn-outline-primary" onclick="loadYelpWidget()">
                                        <i class="fas fa-sync me-2"></i>Cargar Widget
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Review Stats Section -->
<section class="review-stats-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <h3 class="stat-number">4.8</h3>
                    <p class="stat-label">Calificación Promedio</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-comments fa-2x text-primary"></i>
                    </div>
                    <h3 class="stat-number" id="totalReviews">0</h3>
                    <p class="stat-label">Reseñas Totales</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-thumbs-up fa-2x text-success"></i>
                    </div>
                    <h3 class="stat-number">98%</h3>
                    <p class="stat-label">Clientes Satisfechos</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-heart fa-2x text-danger"></i>
                    </div>
                    <h3 class="stat-number">1000+</h3>
                    <p class="stat-label">Clientes Felices</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="cta-title mb-4">¿Tienes una Experiencia que Compartir?</h2>
                <p class="cta-description mb-4">
                    Si has visitado nuestro restaurante, nos encantaría conocer tu opinión. 
                    Tu feedback nos ayuda a mejorar y servir mejor a nuestra comunidad.
                </p>
                <div class="cta-buttons">
                    <a href="https://www.yelp.com/biz/horchata-mexican-food-culver-city" target="_blank" class="btn btn-warning btn-lg me-3">
                        <i class="fab fa-yelp me-2"></i>Reseñar en Yelp
                    </a>
                    <a href="#contact" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contactarnos
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
