/**
 * Menu Page JavaScript - Versión Limpia y Funcional
 * Horchata Mexican Food - Página de Menú
 */

$(document).ready(function() {
    console.log('🍽️ Menu: Iniciando página...');
    
    // Verificar que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('❌ Menu: jQuery no está disponible');
        return;
    }
    
    console.log('✅ Menu: jQuery disponible');
    
    // Cargar categorías para filtros
    console.log('🔍 Menu: Cargando categorías...');
    loadCategoryFilters();
    
    // Cargar menú completo
    console.log('🔍 Menu: Cargando menú...');
    loadMenuContent();
    
    // Configurar búsqueda
    console.log('🔍 Menu: Configurando búsqueda...');
    setupSearch();
    
    // Configurar filtros de categoría
    console.log('🔍 Menu: Configurando filtros...');
    setupCategoryFilters();
    
    // Configurar carrito
    console.log('🔍 Menu: Configurando carrito...');
    setupCart();
    
    console.log('✅ Menu: Inicialización completa');
});

/**
 * Cargar filtros de categoría
 */
function loadCategoryFilters() {
    console.log('🔍 Menu: Cargando filtros de categoría...');
    
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        data: { action: 'get_categories', limit: 20 },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Categorías cargadas:', response);
            if (response.success) {
                displayCategoryFilters(response.data);
            } else {
                console.error('❌ Menu: Error al cargar categorías:', response.message);
                showCategoriesError();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error AJAX al cargar categorías:', error);
            showCategoriesError();
        }
    });
}

/**
 * Mostrar filtros de categoría
 */
function displayCategoryFilters(categories) {
    console.log('🔍 Menu: displayCategoryFilters llamada con:', categories);
    
    const container = $('#categoryFilters');
    let html = '';
    
    categories.forEach((category, index) => {
        console.log(`🔍 Menu: Procesando categoría ${index}:`, category);
        
        html += `
            <button class="category-filter-btn" data-category="${category.id}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px 12px; border: none; border-radius: 12px; background: #ffffff; color: #1a1a1a; transition: all 0.3s ease; cursor: pointer; min-width: 80px; max-width: 100px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); position: relative; overflow: hidden; flex-shrink: 0;">
                <div class="category-icon" style="font-size: 1.5rem; margin-bottom: 6px; transition: all 0.3s ease; color: ${category.color};">
                    <i class="${category.icon}"></i>
                </div>
                <div class="category-name" style="font-size: 0.7rem; font-weight: 600; text-align: center; line-height: 1.1; text-transform: uppercase; letter-spacing: 0.3px; color: #1a1a1a;">
                    ${category.name_es}
                </div>
            </button>
        `;
    });
    
    console.log('🔍 Menu: HTML generado:', html);
    container.html(html);
    console.log('✅ Menu: Filtros de categoría mostrados');
}

/**
 * Mostrar error de categorías
 */
function showCategoriesError() {
    const container = $('#categoryFilters');
    container.html('<p class="text-muted">Error al cargar categorías</p>');
}

/**
 * Cargar contenido del menú
 */
function loadMenuContent(categoryId = null) {
    console.log('🔍 Menu: Cargando contenido del menú...', categoryId);
    
    // Mostrar loading
    $('#loadingState').show();
    $('#menuContent').hide();
    $('#emptyState').hide();
    
    const data = {};
    if (categoryId && categoryId !== 'all') {
        data.category_id = categoryId;
    }
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Productos cargados:', response);
            if (response.success) {
                displayMenuContent(response.data);
            } else {
                console.error('❌ Menu: Error al cargar productos:', response.message);
                showEmptyState();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error AJAX al cargar productos:', error);
            showEmptyState();
        }
    });
}

/**
 * Mostrar contenido del menú
 */
function displayMenuContent(products) {
    console.log('🔍 Menu: displayMenuContent llamada con:', products);
    
    if (!products || products.length === 0) {
        showEmptyState();
        return;
    }
    
    // Agrupar productos por categoría
    const categories = {};
    products.forEach(product => {
        if (!categories[product.category_id]) {
            categories[product.category_id] = {
                id: product.category_id,
                name: product.category_name,
                products: []
            };
        }
        categories[product.category_id].products.push(product);
    });
    
    let html = '';
    Object.values(categories).forEach(category => {
        html += `
            <div class="menu-category">
                <div class="category-header">
                    <h2 class="category-title">${category.name}</h2>
                </div>
                <div class="category-products">
        `;
        
        category.products.forEach(product => {
            html += `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${product.image || 'assets/images/placeholder.jpg'}" alt="${product.name}">
                    </div>
                    <div class="product-content">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${product.description || ''}</p>
                        <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                        <div class="product-actions">
                            <button class="add-to-cart-btn" onclick="addToCart(${product.id}, '${product.name}', ${parseFloat(product.price)}, '${product.image || 'assets/images/placeholder.jpg'}')">
                                <i class="fas fa-plus me-2"></i>Agregar al Carrito
                            </button>
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
    
    $('#menuContent').html(html);
    $('#loadingState').hide();
    $('#menuContent').show();
    console.log('✅ Menu: Contenido del menú mostrado');
}

/**
 * Mostrar estado vacío
 */
function showEmptyState() {
    $('#loadingState').hide();
    $('#menuContent').hide();
    $('#emptyState').show();
}

/**
 * Configurar búsqueda
 */
function setupSearch() {
    console.log('🔍 Menu: Configurando búsqueda...');
    
    $('#searchBtn').on('click', function() {
        const searchTerm = $('#searchInput').val().trim();
        if (searchTerm) {
            searchProducts(searchTerm);
        }
    });
    
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const searchTerm = $(this).val().trim();
            if (searchTerm) {
                searchProducts(searchTerm);
            }
        }
    });
}

/**
 * Buscar productos
 */
function searchProducts(searchTerm) {
    console.log('🔍 Menu: Buscando productos:', searchTerm);
    
    $('#loadingState').show();
    $('#menuContent').hide();
    $('#emptyState').hide();
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: { search: searchTerm },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Resultados de búsqueda:', response);
            if (response.success) {
                if (response.data && response.data.length > 0) {
                    displayMenuContent(response.data);
                } else {
                    showEmptyState();
                }
            } else {
                showEmptyState();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error en búsqueda:', error);
            showEmptyState();
        }
    });
}

/**
 * Configurar filtros de categoría
 */
function setupCategoryFilters() {
    console.log('🔍 Menu: Configurando filtros de categoría...');
    
    $(document).on('click', '.category-filter-btn', function() {
        const categoryId = $(this).data('category');
        console.log('🔍 Menu: Categoría seleccionada:', categoryId);
        
        // Actualizar estado activo
        $('.category-filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Limpiar búsqueda
        $('#searchInput').val('');
        
        // Cargar productos de la categoría
        loadMenuContent(categoryId);
    });
}

/**
 * Configurar carrito
 */
function setupCart() {
    console.log('🔍 Menu: Configurando carrito...');
    
    // Usar las funciones del carrito global si están disponibles
    if (typeof window.HorchataCart !== 'undefined') {
        console.log('✅ Menu: Carrito global disponible');
    } else {
        console.log('⚠️ Menu: Carrito global no disponible');
    }
}

/**
 * Agregar al carrito
 */
function addToCart(productId, productName, productPrice, productImage) {
    console.log('🛒 Menu: Agregando al carrito:', productId, productName, productPrice);
    
    if (typeof window.HorchataCart !== 'undefined' && window.HorchataCart.addToCart) {
        window.HorchataCart.addToCart(productId, productName, productPrice, productImage);
        
        // Mostrar notificación
        showAddToCartNotification(productName);
    } else {
        console.error('❌ Menu: Función addToCart no disponible');
    }
}

/**
 * Mostrar notificación de agregado al carrito
 */
function showAddToCartNotification(productName) {
    // Crear notificación temporal
    const notification = $(`
        <div class="alert alert-success position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>${productName}</strong> agregado al carrito
        </div>
    `);
    
    $('body').append(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 3000);
}

/**
 * Limpiar búsqueda
 */
function clearSearch() {
    $('#searchInput').val('');
    $('.category-filter-btn').removeClass('active');
    $('.category-filter-btn[data-category="all"]').addClass('active');
    loadMenuContent();
}

// Exportar funciones para uso global
window.MenuPage = {
    loadMenuContent,
    displayMenuContent,
    clearSearch,
    addToCart
};