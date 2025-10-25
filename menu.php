<?php
/**
 * Horchata Mexican Food - Página de Menú
 * Diseño inspirado en Trattoria la Pasta
 */

// Incluir configuración
require_once 'includes/db_connect.php';

// Configurar página
$page_title = 'Menú';
$page_scripts = ['assets/js/menu.js'];
$page_styles = ['assets/css/menu.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="menu-hero-section">
    <div class="menu-hero-background">
        <div class="menu-hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="menu-hero-content">
                        <div class="menu-hero-badge mb-4">
                            <span class="badge bg-white text-dark px-3 py-2">
                                <i class="fas fa-utensils me-2"></i>Menú Completo
                            </span>
                        </div>
                        <h1 class="menu-hero-title animate-on-scroll">
                            Sabores <span class="text-primary-custom">Auténticos</span> de México
                        </h1>
                        <p class="menu-hero-description animate-on-scroll">
                            Explora nuestra colección de platillos tradicionales, preparados con ingredientes frescos 
                            y las recetas familiares que han pasado de generación en generación.
                        </p>
                        <div class="menu-hero-stats mb-4">
                            <div class="row">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">150+</h3>
                                        <p class="stat-label">Platillos</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">10</h3>
                                        <p class="stat-label">Categorías</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <h3 class="stat-number">100%</h3>
                                        <p class="stat-label">Auténtico</p>
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

<!-- Menu Navigation -->
<section class="menu-navigation-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="menu-navigation-wrapper">
                    <!-- Category Filters -->
                    <div class="menu-filters mb-4">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <button class="category-filter active" data-category="all">
                                <div class="category-icon">
                                    <i class="fas fa-th"></i>
                                </div>
                                <div class="category-name">Todos</div>
                            </button>
                            <div id="categoryFilters">
                                <!-- Categorías se cargarán aquí via AJAX -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="menu-search-wrapper">
                        <div class="row">
                            <div class="col-lg-6 mx-auto">
                                <div class="search-container">
                                    <div class="search-input-group">
                                        <input type="text" class="form-control search-input" id="searchInput" placeholder="Buscar platillos deliciosos...">
                                        <button class="btn btn-primary-custom search-btn" type="button" id="searchBtn">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="search-suggestions">
                                        <span class="suggestion-tag">Tacos</span>
                                        <span class="suggestion-tag">Burritos</span>
                                        <span class="suggestion-tag">Quesadillas</span>
                                        <span class="suggestion-tag">Enchiladas</span>
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

<!-- Menu Content -->
<section class="menu-content-section">
    <div class="container">
        <!-- Loading State -->
        <div id="loadingState" class="menu-loading-state">
            <div class="loading-animation">
                <div class="loading-spinner">
                    <i class="fas fa-utensils fa-2x"></i>
                </div>
                <h4 class="loading-title">Preparando tu menú...</h4>
                <p class="loading-description">Cargando nuestros deliciosos platillos</p>
                <div class="loading-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        
        <!-- Menu Categories -->
        <div id="menuContent" class="menu-content-wrapper" style="display: none;">
            <!-- Contenido del menú se cargará aquí -->
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="menu-empty-state" style="display: none;">
            <div class="empty-animation">
                <div class="empty-icon">
                    <i class="fas fa-search fa-3x"></i>
                </div>
                <h4 class="empty-title">¡Ups! No encontramos ese platillo</h4>
                <p class="empty-description">
                    No se encontraron platillos con esos términos. 
                    Intenta con otros ingredientes o explora nuestras categorías.
                </p>
                <div class="empty-actions">
                    <button class="btn btn-primary-custom" onclick="clearSearch()">
                        <i class="fas fa-refresh me-2"></i>Limpiar Búsqueda
                    </button>
                    <button class="btn btn-outline-primary-custom" onclick="showAllCategories()">
                        <i class="fas fa-th me-2"></i>Ver Todas las Categorías
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Features -->
<section class="menu-features-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h5 class="feature-title">Platillos Picantes</h5>
                    <p class="feature-description">
                        Nuestros platillos más picantes para los amantes del chile
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h5 class="feature-title">Ingredientes Frescos</h5>
                    <p class="feature-description">
                        Utilizamos solo los ingredientes más frescos y de calidad
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h5 class="feature-title">Hecho con Amor</h5>
                    <p class="feature-description">
                        Cada platillo se prepara con el amor de nuestras recetas familiares
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript se carga desde assets/js/menu.js -->
<!-- Estilos se cargan desde assets/css/menu.css -->

<?php
// Incluir footer
include 'includes/footer.php';
?>
