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
    
    // Leer parámetros de la URL
    const urlParams = getUrlParameters();
    console.log('🔍 Menu: Parámetros de URL:', urlParams);
    
    // Cargar categorías para filtros
    console.log('🔍 Menu: Cargando categorías...');
    loadCategoryFilters(urlParams.category);
    
    // Cargar menú con parámetros de URL
    console.log('🔍 Menu: Cargando menú...');
    loadMenuContent(urlParams.category);
    
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
 * Obtener parámetros de la URL
 */
function getUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = {};
    
    for (const [key, value] of urlParams) {
        params[key] = value;
    }
    
    console.log('🔍 Menu: Parámetros extraídos:', params);
    return params;
}

/**
 * Cargar filtros de categoría
 */
function loadCategoryFilters(activeCategory = null) {
    console.log('🔍 Menu: Cargando filtros de categoría...', activeCategory);
    
    $.ajax({
        url: 'ajax/categories.ajax.php',
        method: 'GET',
        data: { action: 'get_categories', limit: 20 },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Categorías cargadas:', response);
            if (response.success) {
                displayCategoryFilters(response.data, activeCategory);
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
function displayCategoryFilters(categories, activeCategory = null) {
    console.log('🔍 Menu: displayCategoryFilters llamada con:', categories, 'Categoría activa:', activeCategory);
    
    const container = $('#categoryFilters');
    let html = '';
    
    categories.forEach((category, index) => {
        console.log(`🔍 Menu: Procesando categoría ${index}:`, category);
        
        // Determinar si esta categoría debe estar activa
        const isActive = activeCategory && category.id === activeCategory;
        const activeStyle = isActive ? 'background: #d4af37; color: #ffffff; transform: translateY(-3px); box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);' : 'background: #ffffff; color: #1a1a1a;';
        const activeClass = isActive ? ' active' : '';
        
        // Usar el nombre correcto según el idioma
        const categoryName = getLanguage() === 'en' ? category.name : category.name_es;
        
        html += `
            <button class="category-filter-btn${activeClass}" data-category="${category.id}" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 12px 10px; border: none; border-radius: 12px; ${activeStyle} transition: all 0.3s ease; cursor: pointer; width: 100px; flex-shrink: 0; box-shadow: ${isActive ? '0 4px 12px rgba(212, 175, 55, 0.4);' : '0 2px 8px rgba(0, 0, 0, 0.1);'};">
                <div class="category-image" style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; margin-bottom: 6px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <img src="${category.image}" alt="${categoryName}" style="width: 100%; height: 100%; object-fit: cover; transition: all 0.3s ease;">
                </div>
                <div class="category-name" style="font-size: 0.7rem; font-weight: 600; text-align: center; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.3px; color: ${isActive ? '#ffffff' : '#1a1a1a'}; white-space: normal; word-wrap: break-word; hyphens: auto;">
                    ${categoryName}
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
    console.log('🔍 Menu: Estados de UI actualizados');
    
    const data = {};
    if (categoryId && categoryId !== 'all') {
        data.category_id = categoryId;
        console.log('🔍 Menu: Filtro de categoría aplicado:', data);
    } else {
        console.log('🔍 Menu: Cargando todos los productos');
    }
    
    console.log('🔍 Menu: Enviando petición AJAX con datos:', data);
    
    $.ajax({
        url: 'ajax/products.ajax.php',
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            console.log('✅ Menu: Productos cargados:', response);
            console.log('✅ Menu: Número de productos:', response.data ? response.data.length : 0);
            if (response.success) {
                displayMenuContent(response.data, categoryId);
            } else {
                console.error('❌ Menu: Error al cargar productos:', response.message);
                showEmptyState();
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Menu: Error AJAX al cargar productos:', error);
            console.error('❌ Menu: Status:', status);
            console.error('❌ Menu: Response:', xhr.responseText);
            showEmptyState();
        }
    });
}

/**
 * Mostrar contenido del menú
 */
function displayMenuContent(products, categoryId = null) {
    console.log('🔍 Menu: displayMenuContent llamada con:', products, 'CategoryId:', categoryId);
    
    if (!products || products.length === 0) {
        showEmptyState();
        return;
    }
    
    let html = '';
    
    // Si se está filtrando por una categoría específica, mostrar solo esa categoría
    if (categoryId && categoryId !== 'all') {
        console.log('🔍 Menu: Mostrando solo categoría filtrada:', categoryId);
        // Obtener el nombre de la categoría del primer producto
        const categoryName = products[0]?.category_name || 'Categoría';
        
        html += `
            <div class="menu-category">
                <div class="category-header">
                    <h2 class="category-title">${categoryName}</h2>
                    <p class="category-description">${products.length} platillo${products.length !== 1 ? 's' : ''} disponible${products.length !== 1 ? 's' : ''}</p>
                </div>
                <div class="category-products">
        `;
        
        products.forEach(product => {
            html += `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${product.image ? product.image.replace('../', '') : 'assets/images/placeholder.jpg'}" alt="${product.name}">
                    </div>
                    <div class="product-content">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${product.description || ''}</p>
                        <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                        <div class="product-actions">
                            <a href="product.php?id=${product.id}" class="btn btn-primary view-details-btn">
                                <i class="fas fa-eye me-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    } else {
        console.log('🔍 Menu: Mostrando todas las categorías');
        
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
        
        Object.values(categories).forEach(category => {
            html += `
                <div class="menu-category">
                    <div class="category-header">
                        <h2 class="category-title">${category.name}</h2>
                        <p class="category-description">${category.products.length} platillo${category.products.length !== 1 ? 's' : ''}</p>
                    </div>
                    <div class="category-products">
            `;
            
            category.products.forEach(product => {
                html += `
                    <div class="product-card">
                        <div class="product-image">
                            <img src="${product.image ? product.image.replace('../', '') : 'assets/images/placeholder.jpg'}" alt="${product.name}">
                        </div>
                        <div class="product-content">
                            <h3 class="product-title">${product.name}</h3>
                            <p class="product-description">${product.description || ''}</p>
                            <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                            <div class="product-actions">
                                <a href="product.php?id=${product.id}" class="btn btn-primary view-details-btn">
                                    <i class="fas fa-eye me-2"></i>Ver Detalles
                                </a>
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
    }
    
    $('#menuContent').html(html);
    $('#loadingState').hide();
    $('#menuContent').show();
    
    // Botón de agregar al carrito removido en menú; solo ver detalles
    
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
 * Actualizar URL con parámetros
 */
function updateUrl(categoryId) {
    const url = new URL(window.location);
    
    if (categoryId && categoryId !== 'all') {
        url.searchParams.set('category', categoryId);
    } else {
        url.searchParams.delete('category');
    }
    
    // Actualizar URL sin recargar la página
    window.history.pushState({}, '', url);
    console.log('🔍 Menu: URL actualizada:', url.toString());
}

/**
 * Configurar filtros de categoría
 */
function setupCategoryFilters() {
    console.log('🔍 Menu: Configurando filtros de categoría...');
    
    $(document).on('click', '.category-filter-btn', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('category');
        console.log('🔍 Menu: Categoría seleccionada:', categoryId);
        console.log('🔍 Menu: Elemento clickeado:', $(this));
        
        // Actualizar estado activo
        $('.category-filter-btn').removeClass('active');
        $(this).addClass('active');
        console.log('🔍 Menu: Estado activo actualizado');
        
        // Limpiar búsqueda
        $('#searchInput').val('');
        console.log('🔍 Menu: Búsqueda limpiada');
        
        // Actualizar URL sin recargar la página
        updateUrl(categoryId);
        console.log('🔍 Menu: URL actualizada');
        
        // Cargar productos de la categoría
        console.log('🔍 Menu: Iniciando carga de productos para categoría:', categoryId);
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
    const addedText = window.translations ? window.translations.addedToCart : 'agregado al carrito';
    const notification = $(`
        <div class="alert alert-success position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>${productName}</strong> ${addedText}
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