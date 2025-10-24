<?php
/**
 * Horchata Mexican Food - Página de Detalle de Producto
 * Diseño inspirado en Trattoria la Pasta
 */

// Incluir configuración
require_once 'includes/db_connect.php';

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
$page_title = $product['name_en'];
$page_scripts = ['assets/js/product.js'];

// Incluir header
include 'includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="py-3 bg-light">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="menu.php">Menú</a></li>
            <li class="breadcrumb-item"><a href="menu.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name_en']; ?></li>
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
                        <img src="<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>" 
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
                            <i class="fas fa-utensils me-1"></i><?php echo $product['category_name']; ?>
                        </span>
                    </div>
                    
                    <!-- Product Title -->
                    <h1 class="product-title mb-3"><?php echo htmlspecialchars($product['name_en']); ?></h1>
                    
                    <!-- Price -->
                    <div class="product-price mb-4">
                        <span class="h2 text-primary-custom">$<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    
                    <!-- Description -->
                    <div class="product-description mb-4">
                        <p class="lead"><?php echo htmlspecialchars($product['description_en']); ?></p>
                    </div>
                    
                    <!-- Product Features -->
                    <div class="product-features mb-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span>Tiempo de preparación: 15-20 min</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-fire text-primary me-2"></i>
                                    <span>Nivel de picante: Medio</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-leaf text-primary me-2"></i>
                                    <span>Ingredientes frescos</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="feature-item d-flex align-items-center mb-2">
                                    <i class="fas fa-heart text-primary me-2"></i>
                                    <span>Receta tradicional</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity and Add to Cart -->
                    <div class="add-to-cart-section">
                        <div class="row align-items-center mb-4">
                            <div class="col-auto">
                                <label class="form-label fw-bold">Cantidad:</label>
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
                                    data-product-name="<?php echo htmlspecialchars($product['name_en']); ?>"
                                    data-product-price="<?php echo $product['price']; ?>"
                                    data-product-image="<?php echo $product['image']; ?>">
                                <i class="fas fa-shopping-cart me-2"></i>Agregar al Carrito
                            </button>
                            <button class="btn btn-outline-primary btn-lg flex-md-fill" id="wishlistBtn">
                                <i class="fas fa-heart me-2"></i>Favoritos
                            </button>
                        </div>
                    </div>
                    
                    <!-- Additional Info -->
                    <div class="product-additional-info mt-4">
                        <div class="accordion" id="productAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="ingredientsHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ingredients">
                                        <i class="fas fa-list me-2"></i>Ingredientes
                                    </button>
                                </h2>
                                <div id="ingredients" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        <p>Ingredientes frescos seleccionados cuidadosamente para garantizar la mejor calidad y sabor auténtico.</p>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success me-2"></i>Carne de res premium</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Cebolla y ajo frescos</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Especias tradicionales</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Tortillas de maíz</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="nutritionHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nutrition">
                                        <i class="fas fa-chart-pie me-2"></i>Información Nutricional
                                    </button>
                                </h2>
                                <div id="nutrition" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Calorías:</strong> 450 kcal
                                            </div>
                                            <div class="col-6">
                                                <strong>Proteínas:</strong> 25g
                                            </div>
                                            <div class="col-6">
                                                <strong>Carbohidratos:</strong> 35g
                                            </div>
                                            <div class="col-6">
                                                <strong>Grasas:</strong> 20g
                                            </div>
                                        </div>
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

<!-- Related Products Section -->
<?php if (!empty($related_products)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Productos Relacionados</h2>
            <p>Otros platillos de la misma categoría que podrían interesarte</p>
        </div>
        
        <div class="row" id="relatedProducts">
            <?php foreach ($related_products as $related): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo $related['image'] ?: 'assets/images/placeholder.jpg'; ?>')"></div>
                    <div class="product-info">
                        <h5 class="product-title"><?php echo htmlspecialchars($related['name_en']); ?></h5>
                        <p class="product-description"><?php echo htmlspecialchars(substr($related['description_en'], 0, 100)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$<?php echo number_format($related['price'], 2); ?></span>
                            <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver
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

<!-- JavaScript específico para la página de producto -->
<script>
$(document).ready(function() {
    // Configurar selector de cantidad
    setupQuantitySelector();
    
    // Configurar botón de agregar al carrito
    setupAddToCart();
    
    // Configurar botón de favoritos
    setupWishlist();
    
    // Configurar galería de imágenes
    setupImageGallery();
});

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

function setupAddToCart() {
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const productPrice = parseFloat($(this).data('product-price'));
        const productImage = $(this).data('product-image');
        const quantity = parseInt($('#productQuantity').val());
        
        // Agregar producto al carrito con la cantidad especificada
        for (let i = 0; i < quantity; i++) {
            addToCart(productId, productName, productPrice, productImage);
        }
        
        // Mostrar feedback visual
        showCartFeedback($(this));
        
        // Mostrar notificación
        showNotification(`${quantity} ${productName} agregado(s) al carrito`, 'success');
    });
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

function setupImageGallery() {
    // Si hay múltiples imágenes, configurar galería
    // Por ahora, solo mostrar la imagen principal
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
    button.html('<i class="fas fa-check me-2"></i>Agregado').addClass('btn-success');
    
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
