<?php
/**
 * Horchata Mexican Food - Página de Detalle de Producto
 * Diseño inspirado en Trattoria la Pasta
 */

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Obtener ID del producto
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: menu.php');
    exit;
}

// Obtener información del producto
$product = fetchOne("
    SELECT p.*, c.name_en as category_name, c.name_es as category_name_es
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.is_available = 1
", [$product_id]);

if (!$product) {
    header('Location: menu.php');
    exit;
}

// Obtener productos relacionados
$related_products = fetchAll("
    SELECT p.*, c.name_en as category_name
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.id != ? AND p.is_available = 1
    ORDER BY p.is_featured DESC, p.name_en ASC
    LIMIT 4
", [$product['category_id'], $product_id]);

// Configurar página
$page_title = $product['name_' . getCurrentLanguage()];
$page_scripts = ['assets/js/product.js'];

// Incluir header
include 'includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="py-3 bg-light" style="margin-top: 76px;">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php"><?php echo __('home'); ?></a></li>
            <li class="breadcrumb-item"><a href="menu.php"><?php echo __('menu'); ?></a></li>
            <li class="breadcrumb-item"><a href="menu.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name_' . getCurrentLanguage()]; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name_' . getCurrentLanguage()]; ?></li>
        </ol>
    </div>
</nav>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="product-image-container">
                    <div class="main-image">
                        <img src="<?php echo $product['image'] ? str_replace('../', '', $product['image']) : 'assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name_en']); ?>" 
                             class="img-fluid rounded-3 shadow-lg" 
                             id="mainProductImage">
                    </div>
                    
                    <!-- Image Gallery (si hay múltiples imágenes) -->
                    <div class="image-gallery mt-3" id="imageGallery" style="display: none;">
                        <div class="row g-2">
                            <!-- Thumbnails se cargarán aquí si hay múltiples imágenes -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Information -->
            <div class="col-lg-6">
                <div class="product-info">
                    <!-- Category Badge -->
                    <div class="mb-3">
                        <span class="badge bg-primary-custom fs-6 px-3 py-2">
                            <i class="fas fa-utensils me-1"></i><?php echo $product['category_name_' . getCurrentLanguage()]; ?>
                        </span>
                    </div>
                    
                    <!-- Product Title -->
                    <h1 class="product-title mb-3"><?php echo htmlspecialchars($product['name_' . getCurrentLanguage()]); ?></h1>
                    
                    <!-- Price -->
                    <div class="product-price mb-4">
                        <span class="h2 text-primary-custom">$<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    
                    <!-- Description -->
                    <div class="product-description mb-4">
                        <p class="lead"><?php echo htmlspecialchars($product['description_' . getCurrentLanguage()]); ?></p>
                    </div>
                    
                    <!-- Product Features (opcional - mostrar solo si hay datos específicos) -->
                    <?php if (!empty($product['preparation_time']) || !empty($product['spicy_level']) || !empty($product['dietary_info'])): ?>
                    <div class="product-features mb-4">
                        <div class="row">
                            <?php if (!empty($product['preparation_time'])): ?>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span><?php echo __('preparation_time'); ?>: <?php echo $product['preparation_time']; ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($product['spicy_level'])): ?>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-fire text-primary me-2"></i>
                                    <span><?php echo __('spicy_level'); ?>: <?php echo $product['spicy_level']; ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-leaf text-primary me-2"></i>
                                    <span><?php echo __('fresh_ingredients'); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-heart text-primary me-2"></i>
                                    <span><?php echo __('traditional_recipe'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Default features -->
                    <div class="product-features mb-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span><?php echo __('preparation_time_default'); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-fire text-primary me-2"></i>
                                    <span><?php echo __('spicy_level_default'); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-leaf text-primary me-2"></i>
                                    <span><?php echo __('fresh_ingredients'); ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-heart text-primary me-2"></i>
                                    <span><?php echo __('traditional_recipe'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quantity and Add to Cart -->
                    <div class="add-to-cart-section">
                        <div class="row align-items-center mb-4">
                            <div class="col-auto">
                                <label class="form-label fw-bold"><?php echo __('quantity'); ?>:</label>
                            </div>
                            <div class="col-auto">
                                <div class="quantity-selector d-flex align-items-center">
                                    <button class="btn btn-outline-secondary quantity-btn" data-action="decrease">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control text-center quantity-input" 
                                           value="1" min="1" max="10" id="productQuantity">
                                    <button class="btn btn-outline-secondary quantity-btn" data-action="increase">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button class="btn btn-primary btn-lg flex-md-fill add-to-cart-btn" 
                                    data-product-id="<?php echo $product['id']; ?>"
                                    data-product-name="<?php echo htmlspecialchars($product['name_' . getCurrentLanguage()]); ?>"
                                    data-product-price="<?php echo $product['price']; ?>"
                                    data-product-image="<?php echo $product['image']; ?>"
                                    data-bs-toggle="modal" data-bs-target="#customizeModal">
                                <i class="fas fa-shopping-cart me-2"></i><?php echo __('add_to_cart'); ?>
                            </button>
                            <button class="btn btn-outline-primary btn-lg flex-md-fill" id="wishlistBtn">
                                <i class="fas fa-heart me-2"></i><?php echo __('favorites'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Customize Modal -->
                    <div class="modal fade" id="customizeModal" tabindex="-1" aria-labelledby="customizeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="customizeModalLabel">
                                        <i class="fas fa-cog me-2"></i><?php echo __('customize_order'); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold"><?php echo __('special_instructions'); ?></label>
                                        <textarea class="form-control" id="specialInstructions" rows="3" 
                                                  placeholder="<?php echo __('special_instructions_placeholder'); ?>"></textarea>
                                        <small class="text-muted"><?php echo __('special_instructions_help'); ?></small>
                                    </div>
                                    
                                    <div class="mb-3" id="extrasSection" style="display: none;">
                                        <label class="form-label fw-bold"><?php echo __('extras'); ?></label>
                                        <div id="extrasContainer">
                                            <!-- Los extras se cargarán dinámicamente según la categoría -->
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <?php echo __('cancel'); ?>
                                    </button>
                                    <button type="button" class="btn btn-primary" id="confirmAddToCart">
                                        <i class="fas fa-check me-1"></i><?php echo __('add_to_cart'); ?>
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

<!-- Related Products Section -->
<?php if (!empty($related_products)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header">
            <h2><?php echo __('related_products'); ?></h2>
            <p><?php echo __('related_products_description'); ?></p>
        </div>
        
        <div class="row" id="relatedProducts">
            <?php foreach ($related_products as $related): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo $related['image'] ? str_replace('../', '', $related['image']) : 'assets/images/placeholder.jpg'; ?>')"></div>
                        <div class="product-info">
                        <h5 class="product-title"><?php echo htmlspecialchars($related['name_' . getCurrentLanguage()]); ?></h5>
                        <p class="product-description"><?php echo htmlspecialchars(substr($related['description_' . getCurrentLanguage()], 0, 100)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$<?php echo number_format($related['price'], 2); ?></span>
                            <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i><?php echo __('view'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Estilos adicionales para la página de producto -->
<style>
.product-image-container {
    position: sticky;
    top: 100px;
}

.main-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.main-image img:hover {
    transform: scale(1.02);
}

.product-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark-color);
    line-height: 1.2;
}

.product-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.quantity-selector {
    border: 2px solid var(--border-color);
    border-radius: 25px;
    overflow: hidden;
}

.quantity-btn {
    border: none;
    background: transparent;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: var(--primary-color);
    color: white;
}

.quantity-input {
    border: none;
    width: 60px;
    text-align: center;
    font-weight: 600;
}

.quantity-input:focus {
    box-shadow: none;
    border: none;
}

.feature-item {
    font-size: 0.9rem;
    color: var(--text-light);
}

.product-additional-info .accordion-button {
    font-weight: 600;
    color: var(--dark-color);
}

.product-additional-info .accordion-button:not(.collapsed) {
    background-color: var(--primary-color);
    color: white;
}

.related-products .product-card {
    height: 100%;
}

@media (max-width: 768px) {
    .product-title {
        font-size: 2rem;
    }
    
    .product-price {
        font-size: 1.5rem;
    }
    
    .main-image img {
        height: 300px;
    }
}
</style>

<?php
// Incluir footer
include 'includes/footer.php';
?>

<!-- JavaScript específico para la página de producto (después de footer para que jQuery esté cargado) -->
<script>
// Pasar traducciones al JavaScript
window.translations = {
    guacamole: '<?php echo __("guacamole"); ?>',
    sour_cream: '<?php echo __("sour_cream"); ?>',
    cheese: '<?php echo __("cheese"); ?>'
};

$(document).ready(function() {
    console.log('jQuery loaded, initializing product page');
    
    // Configurar selector de cantidad
    setupQuantitySelector();
    
    // Configurar botón de agregar al carrito
    setupAddToCart();
    
    // Configurar botón de favoritos
    setupWishlist();
    
    // Cargar extras según la categoría del producto
    loadExtrasForCategory();
    
    // Delegar evento al botón de confirmar en el modal (para asegurar que funciona)
    $(document).on('click', '#confirmAddToCart', function() {
        console.log('Confirm Add to Cart clicked (delegated)');
        handleConfirmAddToCart();
    });
});

function loadExtrasForCategory() {
    // Obtener la categoría del producto desde el PHP
    const categoryName = '<?php echo htmlspecialchars($product["category_name"] ?? ""); ?>';
    const productId = <?php echo $product['id'] ?? 0; ?>;
    console.log('Product category:', categoryName);
    console.log('Product ID:', productId);
    
    const extrasContainer = $('#extrasContainer');
    const extrasSection = $('#extrasSection');
    
    // Limpiar extras existentes
    extrasContainer.empty();
    
    // Solo cargar extras desde la base de datos si hay productId
    if (productId > 0) {
        loadExtrasFromDatabase(productId);
    } else {
        // Si no hay productId, no mostrar extras
        console.log('No product ID available, hiding extras section');
        extrasSection.hide();
    }
}

function loadExtrasFromDatabase(productId) {
    $.ajax({
        url: 'ajax/products.ajax.php',
        type: 'GET',
        data: {
            action: 'get_product_extras',
            product_id: productId
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                displayExtras(response.data);
            } else {
                // Si no hay extras en BD, no mostrar nada (no usar sistema legacy)
                console.log('No extras found in database for this product');
                const extrasSection = $('#extrasSection');
                extrasSection.hide();
            }
        },
        error: function() {
            console.log('Error loading extras from database');
            const extrasSection = $('#extrasSection');
            extrasSection.hide();
        }
    });
}

function loadExtrasLegacy(categoryName) {
    const extrasContainer = $('#extrasContainer');
    const extrasSection = $('#extrasSection');
    
    // Definir extras según categoría (sistema anterior)
    let extras = [];
    
    if (categoryName.includes('Burritos') || categoryName.includes('Breakfast Plates') || categoryName.includes('Daily Specials') || categoryName.includes('Seafood') || categoryName.includes('Combinations') || categoryName.includes('Salads & Burgers')) {
        // A) BURRITOS, TORTAS Y PLATILLOS: Guacamole, Crema y Queso +$2.50
        extras = [
            { id: 'extraGuacamole', name: window.translations.guacamole, price: 2.50 },
            { id: 'extraSourCream', name: window.translations.sour_cream, price: 2.50 },
            { id: 'extraCheese', name: window.translations.cheese, price: 2.50 }
        ];
    } else if (categoryName.includes('Tacos')) {
        // B) SOFT TACOS Y HARD SHELL TACOS: Guacamole +$1.00
        extras = [
            { id: 'extraGuacamole', name: window.translations.guacamole, price: 1.00 }
        ];
    }
    
    displayExtras(extras);
}

function displayExtras(extras) {
    const extrasContainer = $('#extrasContainer');
    const extrasSection = $('#extrasSection');
    
    // Si hay extras para esta categoría, mostrarlos
    console.log('Extras found:', extras.length);
    if (extras.length > 0) {
        extras.forEach(extra => {
            extrasContainer.append(`
                <div class="form-check">
                    <input class="form-check-input extra-checkbox" type="checkbox" id="${extra.id}" value="${extra.id}" data-price="${extra.price}">
                    <label class="form-check-label" for="${extra.id}">
                        ${extra.name} (+ $${extra.price.toFixed(2)})
                    </label>
                </div>
            `);
        });
        extrasSection.show();
        console.log('Extras section shown');
    } else {
        extrasSection.hide();
        console.log('No extras for this category, section hidden');
    }
}

function handleConfirmAddToCart() {
    console.log('Current Product:', currentProduct);
    
    if (!currentProduct) {
        console.log('No current product');
        return;
    }
    
    const quantity = parseInt($('#productQuantity').val());
    console.log('Quantity:', quantity);
    
    // Obtener personalizaciones
    const specialInstructions = $('#specialInstructions').val();
    const extras = [];
    
    // Obtener extras seleccionados dinámicamente
    $('.extra-checkbox:checked').each(function() {
        const extraName = $(this).next('label').text().split(' (+')[0]; // Obtener nombre sin precio
        const extraPrice = parseFloat($(this).data('price'));
        extras.push({ name: extraName, price: extraPrice });
    });
    
    console.log('Customizations:', { specialInstructions, extras });
    
    // Calcular precio adicional
    let extrasPrice = 0;
    extras.forEach(extra => extrasPrice += extra.price);
    
    // Agregar producto al carrito con personalización
    for (let i = 0; i < quantity; i++) {
        const productData = {
            id: currentProduct.id,
            name: currentProduct.name,
            price: currentProduct.price + extrasPrice,
            image: currentProduct.image,
            customizations: {
                specialInstructions: specialInstructions,
                extras: extras
            }
        };
        
        console.log('Adding to cart:', productData);
        addToCartWithCustomization(productData);
    }
    
    // Cerrar modal
    $('#customizeModal').modal('hide');
    
    // Mostrar notificación
    // Detectar idioma actual
    const htmlLang = document.documentElement.lang || 'es';
    const currentLang = (htmlLang === 'en' || htmlLang === 'es') ? htmlLang : 'es';
    
    let addedText;
    if (quantity === 1) {
        addedText = (window.translations && window.translations.addedToCart) 
            ? window.translations.addedToCart 
            : (currentLang === 'en' ? 'added to cart' : 'agregado al carrito');
    } else {
        addedText = (window.translations && window.translations.addedToCartPlural) 
            ? window.translations.addedToCartPlural 
            : (currentLang === 'en' ? 'added to cart' : 'agregados al carrito');
    }
    showNotification(`${quantity} ${currentProduct.name} ${addedText}`, 'success');
    
    // Resetear modal
    $('#specialInstructions').val('');
    $('.extra-checkbox').prop('checked', false);
}

function setupQuantitySelector() {
    $('.quantity-btn').on('click', function() {
        const action = $(this).data('action');
        const input = $('#productQuantity');
        let value = parseInt(input.val());
        
        if (action === 'increase' && value < 10) {
            input.val(value + 1);
        } else if (action === 'decrease' && value > 1) {
            input.val(value - 1);
        }
    });
    
    $('#productQuantity').on('change', function() {
        let value = parseInt($(this).val());
        if (value < 1) $(this).val(1);
        if (value > 10) $(this).val(10);
    });
}

let currentProduct = null;

function setupAddToCart() {
    $('.add-to-cart-btn').on('click', function(e) {
        // Guardar información del producto para el modal
        currentProduct = {
            id: $(this).data('product-id'),
            name: $(this).data('product-name'),
            price: parseFloat($(this).data('product-price')),
            image: $(this).data('product-image')
        };
        
        console.log('Add to Cart button clicked, currentProduct set:', currentProduct);
        
        // NO hacer nada más - dejar que Bootstrap maneje el modal
    });
}

function addToCartWithCustomization(productData) {
    let cart = getCartItems();
    
    // Generar un ID único que incluya las personalizaciones
    const customId = productData.id + '_' + JSON.stringify(productData.customizations);
    
    const existingItem = cart.find(item => 
        item.id === productData.id && 
        JSON.stringify(item.customizations) === JSON.stringify(productData.customizations)
    );
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productData.id,
            customId: customId,
            name: productData.name,
            price: productData.price,
            image: productData.image,
            quantity: 1,
            customizations: productData.customizations
        });
    }
    
    saveCartToStorage(cart);
    updateCartDisplay();
}

function setupWishlist() {
    $('#wishlistBtn').on('click', function() {
        // Implementar funcionalidad de favoritos
        $(this).toggleClass('btn-outline-primary btn-primary');
        
        if ($(this).hasClass('btn-primary')) {
            showNotification('Agregado a favoritos', 'success');
        } else {
            showNotification('Removido de favoritos', 'info');
        }
    });
}

// Funciones del carrito (ya definidas en main.js)
function addToCart(productId, productName, productPrice, productImage) {
    let cart = getCartItems();
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: 1
        });
    }
    
    saveCartToStorage(cart);
    updateCartDisplay();
}

function showCartFeedback(button) {
    const originalText = button.html();
    // Detectar idioma actual
    const htmlLang = document.documentElement.lang || 'es';
    const currentLang = (htmlLang === 'en' || htmlLang === 'es') ? htmlLang : 'es';
    
    const addedText = (window.translations && window.translations.addedToCart) 
        ? window.translations.addedToCart 
        : (currentLang === 'en' ? 'added to cart' : 'agregado al carrito');
    button.html(`<i class="fas fa-check me-2"></i>${addedText}`).addClass('btn-success');
    
    setTimeout(() => {
        button.html(originalText).removeClass('btn-success');
    }, 2000);
}

function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 100px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 3000);
}
</script>
