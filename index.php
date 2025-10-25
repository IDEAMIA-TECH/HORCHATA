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
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="animate-on-scroll">Auténtica Comida Mexicana</h1>
                    <p class="animate-on-scroll">
                        Descubre los sabores tradicionales de México con nuestros platillos preparados 
                        con ingredientes frescos y recetas auténticas. Disfruta de una experiencia 
                        culinaria única con servicio pickup.
                    </p>
                    <div class="hero-buttons animate-on-scroll">
                        <a href="menu.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-utensils me-2"></i>Ver Menú
                        </a>
                        <a href="#featured" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-star me-2"></i>Especialidades
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center animate-on-scroll">
                    <img src="assets/images/hero-dish.jpg" alt="Plato mexicano" class="img-fluid rounded-3 shadow-lg" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section id="featured" class="py-5">
    <div class="container">
        <div class="section-header">
            <h2>Nuestras Especialidades</h2>
            <p>Los platillos más populares de nuestro menú, preparados con amor y tradición</p>
        </div>
        
        <div class="row" id="featuredProducts">
            <!-- Productos destacados se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="menu.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-utensils me-2"></i>Ver Menú Completo
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5 bg-light">
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
<section class="py-5">
    <div class="container">
        <div class="section-header">
            <h2>Nuestras Categorías</h2>
            <p>Explora la variedad de sabores que tenemos para ofrecerte</p>
        </div>
        
        <div class="row" id="categoriesContainer">
            <!-- Categorías se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Lo que Dicen Nuestros Clientes</h2>
            <p>Reseñas verificadas de clientes que han disfrutado de nuestros platillos</p>
        </div>
        
        <div class="row" id="reviewsContainer">
            <!-- Reseñas se cargarán aquí via AJAX -->
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="reviews.php" class="btn btn-outline-primary">
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
