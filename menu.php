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
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8 mx-auto text-center">
                <div class="menu-hero-content">
                    <h1 class="menu-hero-title">
                        Nuestro <span class="text-primary-custom">Menú</span>
                    </h1>
                    <p class="menu-hero-description">
                        Descubre la auténtica cocina mexicana con nuestros platillos tradicionales, 
                        preparados con ingredientes frescos y recetas familiares.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Navigation -->
<section class="category-navigation" style="background: #f8f9fa; padding: 2rem 0; border-bottom: 1px solid #e5e5e5;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="category-filters-container" style="display: flex; flex-direction: row; flex-wrap: nowrap; justify-content: center; gap: 0.5rem; align-items: center; overflow-x: auto; padding: 0 1rem; white-space: nowrap;">
                    <button class="category-filter-btn active" data-category="all" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px 12px; border: none; border-radius: 12px; background: #d4af37; color: #ffffff; transition: all 0.3s ease; cursor: pointer; min-width: 80px; max-width: 100px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); position: relative; overflow: hidden; flex-shrink: 0;">
                        <div class="category-icon" style="font-size: 1.5rem; margin-bottom: 6px; transition: all 0.3s ease; color: #ffffff;">
                            <i class="fas fa-th"></i>
                        </div>
                        <div class="category-name" style="font-size: 0.7rem; font-weight: 600; text-align: center; line-height: 1.1; text-transform: uppercase; letter-spacing: 0.3px; color: #ffffff;">
                            Todos
                        </div>
                    </button>
                    <div id="categoryFilters" style="display: flex; flex-direction: row; flex-wrap: nowrap; gap: 0.5rem; align-items: center;">
                        <!-- Categorías se cargarán aquí via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="search-container">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar platillos...">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
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
