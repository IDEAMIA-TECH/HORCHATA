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
<section class="hero-section" style="min-height: 40vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content">
                    <h1 class="animate-on-scroll">Nuestro Menú</h1>
                    <p class="animate-on-scroll">
                        Descubre la auténtica cocina mexicana con nuestros platillos tradicionales, 
                        preparados con ingredientes frescos y recetas familiares.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Categories Filter -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <button class="btn btn-outline-primary category-filter active" data-category="all">
                        <i class="fas fa-th me-2"></i>Todos
                    </button>
                    <div id="categoryFilters">
                        <!-- Categorías se cargarán aquí via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar -->
<section class="py-3 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar platillos...">
                    <button class="btn btn-outline-primary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Content -->
<section class="py-5">
    <div class="container">
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando menú...</span>
            </div>
            <p class="mt-3 text-muted">Cargando nuestro delicioso menú...</p>
        </div>
        
        <!-- Menu Categories -->
        <div id="menuContent" style="display: none;">
            <!-- Contenido del menú se cargará aquí -->
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5" style="display: none;">
            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No se encontraron platillos</h4>
            <p class="text-muted">Intenta con otros términos de búsqueda o explora nuestras categorías.</p>
            <button class="btn btn-primary" onclick="clearSearch()">
                <i class="fas fa-refresh me-2"></i>Limpiar Búsqueda
            </button>
        </div>
    </div>
</section>

<!-- JavaScript se carga desde assets/js/menu.js -->
<!-- Estilos se cargan desde assets/css/menu.css -->

<?php
// Incluir footer
include 'includes/footer.php';
?>
