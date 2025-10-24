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

<!-- JavaScript específico para la página de menú -->
<script>
$(document).ready(function() {
    // Cargar categorías para filtros
    loadCategoryFilters();
    
    // Cargar menú completo
    loadMenuContent();
    
    // Configurar búsqueda
    setupSearch();
    
    // Configurar filtros de categoría
    setupCategoryFilters();
});

function loadCategoryFilters() {
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCategoryFilters(response.data);
            }
        }
    });
}

function displayCategoryFilters(categories) {
    const container = $('#categoryFilters');
    let html = '';
    
    categories.forEach(category => {
        html += `
            <button class="btn btn-outline-primary category-filter" data-category="${category.id}">
                <i class="fas fa-utensils me-2"></i>${category.name}
            </button>
        `;
    });
    
    container.html(html);
}

function loadMenuContent(categoryId = null, searchTerm = '') {
    showLoading();
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { 
            category_id: categoryId,
            search: searchTerm,
            limit: 100
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.data.length > 0) {
                    displayMenuContent(response.data);
                } else {
                    showEmptyState();
                }
            } else {
                showError('Error al cargar el menú');
            }
        },
        error: function() {
            showError('Error de conexión');
        },
        complete: function() {
            hideLoading();
        }
    });
}

function displayMenuContent(products) {
    // Agrupar productos por categoría
    const groupedProducts = {};
    
    products.forEach(product => {
        const categoryId = product.category_id;
        if (!groupedProducts[categoryId]) {
            groupedProducts[categoryId] = {
                category_name: product.category_name,
                products: []
            };
        }
        groupedProducts[categoryId].products.push(product);
    });
    
    // Generar HTML
    let html = '';
    
    Object.keys(groupedProducts).forEach(categoryId => {
        const category = groupedProducts[categoryId];
        
        html += `
            <div class="menu-category mb-5" data-category-id="${categoryId}">
                <h2 class="category-title mb-4">${category.category_name}</h2>
                <div class="row">
        `;
        
        category.products.forEach(product => {
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
        
        html += `
                </div>
            </div>
        `;
    });
    
    $('#menuContent').html(html).show();
    $('#emptyState').hide();
}

function setupSearch() {
    let searchTimeout;
    
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val().trim();
        
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                loadMenuContent(null, searchTerm);
            }
        }, 500);
    });
    
    $('#searchBtn').on('click', function() {
        const searchTerm = $('#searchInput').val().trim();
        loadMenuContent(null, searchTerm);
    });
    
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const searchTerm = $(this).val().trim();
            loadMenuContent(null, searchTerm);
        }
    });
}

function setupCategoryFilters() {
    $(document).on('click', '.category-filter', function() {
        // Actualizar estado activo
        $('.category-filter').removeClass('active');
        $(this).addClass('active');
        
        // Obtener categoría seleccionada
        const categoryId = $(this).data('category');
        
        // Limpiar búsqueda
        $('#searchInput').val('');
        
        // Cargar productos de la categoría
        if (categoryId === 'all') {
            loadMenuContent();
        } else {
            loadMenuContent(categoryId);
        }
    });
}

function showLoading() {
    $('#loadingState').show();
    $('#menuContent').hide();
    $('#emptyState').hide();
}

function hideLoading() {
    $('#loadingState').hide();
}

function showEmptyState() {
    $('#emptyState').show();
    $('#menuContent').hide();
}

function showError(message) {
    $('#emptyState').html(`
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4 class="text-warning">Error</h4>
        <p class="text-muted">${message}</p>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-refresh me-2"></i>Reintentar
        </button>
    `).show();
    $('#menuContent').hide();
}

function clearSearch() {
    $('#searchInput').val('');
    $('.category-filter').removeClass('active');
    $('.category-filter[data-category="all"]').addClass('active');
    loadMenuContent();
}

// Funciones globales para uso en otros archivos
window.MenuPage = {
    loadMenuContent,
    displayMenuContent,
    clearSearch
};
</script>

<!-- Estilos adicionales para la página de menú -->
<style>
.category-filter {
    transition: all 0.3s ease;
    border-radius: 25px;
    padding: 8px 20px;
}

.category-filter:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.category-filter.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.menu-category {
    margin-bottom: 60px;
}

.category-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    color: var(--dark-color);
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
    display: inline-block;
}

#searchInput {
    border-radius: 25px;
    border: 2px solid var(--border-color);
    padding: 12px 20px;
    transition: all 0.3s ease;
}

#searchInput:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
}

#searchBtn {
    border-radius: 0 25px 25px 0;
    border: 2px solid var(--primary-color);
    border-left: none;
}

@media (max-width: 768px) {
    .category-filter {
        margin-bottom: 10px;
    }
    
    .category-title {
        font-size: 1.5rem;
    }
}
</style>

<?php
// Incluir footer
include 'includes/footer.php';
?>
