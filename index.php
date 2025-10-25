<?php
/**
 * Horchata Mexican Food - Página Principal
 * Diseño inspirado en Trattoria la Pasta
 */

// Incluir configuración
require_once 'includes/db_connect.php';

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
                                <i class="fas fa-star me-2"></i>Auténtica Cocina Mexicana
                            </span>
                        </div>
                                <h1 class="hero-title animate-on-scroll">
                                    Sabores que <span class="text-primary-custom">Conquistan</span> el Corazón
                                </h1>
                                <p class="hero-description animate-on-scroll">
                                    Descubre la magia de la cocina mexicana tradicional con ingredientes frescos, 
                                    recetas auténticas y el sabor que solo Horchata Mexican Food puede ofrecerte.
                                </p>
                        <div class="hero-stats mb-4">
                            <div class="row">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">8+</h3>
                                        <p class="stat-label">Años</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">1000+</h3>
                                        <p class="stat-label">Clientes</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">150+</h3>
                                        <p class="stat-label">Platillos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hero-buttons animate-on-scroll">
                            <a href="menu.php" class="btn btn-primary-custom btn-lg me-3 shadow-lg">
                                <i class="fas fa-utensils me-2"></i>Explorar Menú
                            </a>
                            <a href="#featured" class="btn btn-outline-light btn-lg shadow-lg">
                                <i class="fas fa-star me-2"></i>Especialidades
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-container">
                        <div class="hero-image-main">
                            <img src="assets/images/hero-dish.jpg" alt="Plato mexicano" class="img-fluid rounded-4 shadow-xl">
                        </div>
                        <div class="hero-floating-cards">
                            <div class="floating-card card-1 hover-3d pulse-effect">
                                <i class="fas fa-fire text-warning"></i>
                                <span>Picante</span>
                            </div>
                            <div class="floating-card card-2 hover-3d pulse-effect">
                                <i class="fas fa-leaf text-success"></i>
                                <span>Fresco</span>
                            </div>
                            <div class="floating-card card-3 hover-3d pulse-effect">
                                <i class="fas fa-heart text-danger"></i>
                                <span>Hecho con Amor</span>
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
            <h2 class="section-title">Nuestras Especialidades</h2>
            <p class="section-description">
                Los platillos más populares de nuestro menú, preparados con amor y tradición mexicana
            </p>
        </div>
        
        <div class="row g-4" id="featuredProducts">
            <!-- Productos destacados se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando especialidades...</span>
                    </div>
                    <p class="mt-3 text-muted">Preparando los mejores sabores para ti</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="menu.php" class="btn btn-primary-custom btn-lg px-5 py-3 shadow-lg">
                <i class="fas fa-utensils me-2"></i>Explorar Menú Completo
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
                    <h2 class="mb-4">Nuestra Historia</h2>
                    <p class="lead">
                        Desde 2015, Horchata Mexican Food ha sido el hogar de la auténtica 
                        cocina mexicana en nuestra comunidad.
                    </p>
                    <p>
                        Utilizamos ingredientes frescos y recetas tradicionales que han sido 
                        transmitidas de generación en generación. Cada platillo es preparado 
                        con amor y dedicación para brindarte una experiencia culinaria única.
                    </p>
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="text-primary">8+</h3>
                                <p class="text-muted">Años de experiencia</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item text-center">
                                <h3 class="text-primary">1000+</h3>
                                <p class="text-muted">Clientes satisfechos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="assets/images/restaurant-interior.jpg" alt="Interior del restaurante" class="img-fluid rounded-3 shadow">
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
                    <i class="fas fa-utensils me-2"></i>Categorías
                </span>
            </div>
            <h2 class="section-title">Nuestras Categorías</h2>
            <p class="section-description">
                Explora la variedad de sabores auténticos que tenemos para ofrecerte
            </p>
        </div>
        
        <div class="row g-4" id="categoriesContainer">
            <!-- Categorías se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando categorías...</span>
                    </div>
                    <p class="mt-3 text-muted">Organizando nuestros sabores para ti</p>
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
                    <i class="fas fa-star me-2"></i>Testimonios
                </span>
            </div>
            <h2 class="section-title">Lo que Dicen Nuestros Clientes</h2>
            <p class="section-description">
                Reseñas verificadas de clientes que han disfrutado de nuestros platillos auténticos
            </p>
        </div>
        
        <div class="row g-4" id="reviewsContainer">
            <!-- Reseñas se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="loading-spinner">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Cargando testimonios...</span>
                    </div>
                    <p class="mt-3 text-muted">Recopilando las mejores experiencias</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="reviews.php" class="btn btn-primary-custom btn-lg px-5 py-3 shadow-lg">
                <i class="fas fa-star me-2"></i>Ver Todas las Reseñas
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container">
        <div class="section-header">
            <h2>Contáctanos</h2>
            <p>Estamos aquí para servirte. ¡Visítanos o haz tu pedido!</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                    </div>
                    <h5>Ubicación</h5>
                    <p class="text-muted">123 Main Street<br>Ciudad, Estado 12345</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-phone fa-2x text-primary"></i>
                    </div>
                    <h5>Teléfono</h5>
                    <p class="text-muted">(555) 123-4567</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-card text-center p-4">
                    <div class="contact-icon mb-3">
                        <i class="fas fa-clock fa-2x text-primary"></i>
                    </div>
                    <h5>Horarios</h5>
                    <p class="text-muted">Lun-Jue: 9:00 AM - 9:00 PM<br>Vie-Sáb: 9:00 AM - 10:00 PM<br>Dom: 10:00 AM - 8:00 PM</p>
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
