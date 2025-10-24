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

<!-- JavaScript específico para la página de inicio -->
<script>
$(document).ready(function() {
    // Cargar productos destacados
    loadFeaturedProducts();
    
    // Cargar categorías
    loadCategories();
    
    // Cargar reseñas
    loadReviews();
});

function loadFeaturedProducts() {
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { featured: 1, limit: 6 },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayFeaturedProducts(response.data);
            }
        }
    });
}

function displayFeaturedProducts(products) {
    const container = $('#featuredProducts');
    let html = '';
    
    products.forEach(product => {
        html += `
            <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll">
                <div class="product-card">
                    <div class="product-image" style="background-image: url('${product.image}')" onclick="window.location.href='product.php?id=${product.id}'"></div>
                    <div class="product-info">
                        <h5 class="product-title">
                            <a href="product.php?id=${product.id}" class="text-decoration-none text-dark">${product.name}</a>
                        </h5>
                        <p class="product-description">${product.description}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$${product.price}</span>
                            <div class="btn-group" role="group">
                                <a href="product.php?id=${product.id}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                                <button class="btn btn-primary btn-sm add-to-cart"
                                        data-product-id="${product.id}"
                                        data-product-name="${product.name}"
                                        data-product-price="${product.price}"
                                        data-product-image="${product.image}">
                                    <i class="fas fa-plus me-1"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function loadCategories() {
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCategories(response.data);
            }
        }
    });
}

function displayCategories(categories) {
    const container = $('#categoriesContainer');
    let html = '';
    
    categories.forEach(category => {
        html += `
            <div class="col-lg-3 col-md-6 mb-4 animate-on-scroll">
                <div class="category-card text-center p-4 h-100">
                    <div class="category-icon mb-3">
                        <i class="fas fa-utensils fa-3x text-primary"></i>
                    </div>
                    <h5 class="category-title">${category.name}</h5>
                    <p class="text-muted">${category.description}</p>
                    <a href="menu.php?category=${category.id}" class="btn btn-outline-primary btn-sm">
                        Ver Platillos
                    </a>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function loadReviews() {
    $.ajax({
        url: 'ajax/reviews.ajax.php',
        method: 'GET',
        data: { limit: 3, approved: 1 },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayReviews(response.data);
            }
        }
    });
}

function displayReviews(reviews) {
    const container = $('#reviewsContainer');
    let html = '';
    
    reviews.forEach(review => {
        const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
        html += `
            <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll">
                <div class="review-card p-4 h-100">
                    <div class="review-rating mb-3">
                        <span class="text-warning">${stars}</span>
                    </div>
                    <p class="review-comment">"${review.comment}"</p>
                    <div class="review-author">
                        <strong>${review.customer_name}</strong>
                        <small class="text-muted d-block">${formatDate(review.created_at)}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
</script>

<?php
// Incluir footer
include 'includes/footer.php';
?>
