<?php
/**
 * Horchata Mexican Food - Gestión de Productos
 * Panel Administrativo
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

// Obtener parámetros
$action = $_GET['action'] ?? 'list';
$product_id = $_GET['id'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'create':
            createProduct();
            break;
        case 'update':
            updateProduct();
            break;
        case 'delete':
            deleteProduct();
            break;
        case 'toggle_status':
            toggleProductStatus();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'create':
    case 'edit':
        $product = $action === 'edit' ? getProduct($product_id) : null;
        $categories = getAllCategories();
        break;
    case 'list':
    default:
        $products = getAllProducts();
        $categories = getAllCategories();
        break;
}

// Configurar página
$page_title = $action === 'create' ? 'Nuevo Producto' : ($action === 'edit' ? 'Editar Producto' : 'Gestión de Productos');
$page_scripts = []; // JavaScript está inline en este archivo

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-box me-2"></i>Gestión de Productos
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshProducts()">
                    <i class="fas fa-sync-alt me-1"></i>Actualizar
                </button>
            </div>
            <div class="btn-group">
                <a href="products.php?action=create" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Nuevo Producto
                </a>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Products List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name_en']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar productos...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Destacado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $product['image'] ?: '../assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name_en']); ?>" 
                                     class="rounded" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($product['name_en']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars($product['name_es']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>
                                <strong class="text-primary">$<?php echo number_format($product['price'], 2); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $product['is_available'] ? 'success' : 'danger'; ?>">
                                    <?php echo $product['is_available'] ? 'Disponible' : 'No disponible'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($product['is_featured']): ?>
                                <i class="fas fa-star text-warning"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-<?php echo $product['is_available'] ? 'warning' : 'success'; ?>" 
                                            onclick="toggleProductStatus(<?php echo $product['id']; ?>, <?php echo $product['is_available'] ? 'false' : 'true'; ?>)" 
                                            title="<?php echo $product['is_available'] ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="fas fa-<?php echo $product['is_available'] ? 'eye-slash' : 'eye'; ?>"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_en']); ?>')" 
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Product Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php echo $action === 'create' ? 'Nuevo Producto' : 'Editar Producto'; ?>
            </h6>
        </div>
        <div class="card-body">
            <form id="productForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_product">
                <?php if ($action === 'edit'): ?>
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name_en" class="form-label">Nombre (Inglés) *</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" 
                                       value="<?php echo htmlspecialchars($product['name_en'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name_es" class="form-label">Nombre (Español) *</label>
                                <input type="text" class="form-control" id="name_es" name="name_es" 
                                       value="<?php echo htmlspecialchars($product['name_es'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Categoría *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name_en']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" min="0" 
                                           value="<?php echo $product['price'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description_en" class="form-label">Descripción (Inglés)</label>
                            <textarea class="form-control" id="description_en" name="description_en" rows="3"><?php echo htmlspecialchars($product['description_en'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description_es" class="form-label">Descripción (Español)</label>
                            <textarea class="form-control" id="description_es" name="description_es" rows="3"><?php echo htmlspecialchars($product['description_es'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" 
                                           <?php echo (isset($product['is_available']) && $product['is_available']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_available">
                                        Disponible
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           <?php echo (isset($product['is_featured']) && $product['is_featured']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Destacado
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Imagen del Producto</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img id="imagePreview" 
                                         src="<?php echo $product['image'] ?? '../assets/images/placeholder.jpg'; ?>" 
                                         alt="Vista previa" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*" onchange="previewImage(this)">
                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 5MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo $action === 'create' ? 'Crear Producto' : 'Actualizar Producto'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para productos -->
<script>
$(document).ready(function() {
    // Configurar formulario
    setupProductForm();
    
    // Configurar filtros
    setupFilters();
    
    // Configurar DataTable
    setupProductsTable();
});

function setupProductForm() {
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        
        if (validateProductForm()) {
            submitProductForm();
        }
    });
}

function validateProductForm() {
    let isValid = true;
    const requiredFields = ['name_en', 'name_es', 'category_id', 'price'];
    
    requiredFields.forEach(field => {
        const input = $(`#${field}`);
        if (!input.val().trim()) {
            input.addClass('is-invalid');
            isValid = false;
        } else {
            input.removeClass('is-invalid');
        }
    });
    
    // Validar precio
    const price = parseFloat($('#price').val());
    if (isNaN(price) || price < 0) {
        $('#price').addClass('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        showNotification('Por favor, completa todos los campos requeridos', 'error');
    }
    
    return isValid;
}

function submitProductForm() {
    console.log('📤 Submitting product form...');
    const formData = new FormData($('#productForm')[0]);
    
    // Log form data for debugging
    for (let [key, value] of formData.entries()) {
        console.log('📋 Form data:', key, ':', value);
    }
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('✅ Response:', response);
            if (response.success) {
                showNotification(response.message, 'success');
                setTimeout(() => {
                    window.location.href = 'products.php';
                }, 1500);
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX Error:', status, error);
            console.error('❌ Response Text:', xhr.responseText);
            
            let errorMessage = 'Error de conexión';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                errorMessage = 'Error del servidor: ' + xhr.status;
            }
            
            showNotification('Error: ' + errorMessage, 'error');
        }
    });
}

function setupFilters() {
    $('#categoryFilter, #statusFilter, #searchInput').on('change keyup', function() {
        applyFilters();
    });
}

function applyFilters() {
    const category = $('#categoryFilter').val();
    const status = $('#statusFilter').val();
    const search = $('#searchInput').val();
    
    // Aplicar filtros a DataTable (verificar que existe)
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#productsTable')) {
        const table = $('#productsTable').DataTable();
        table.column(2).search(category);
        table.column(4).search(status);
        table.search(search).draw();
    }
}

function setupProductsTable() {
    // Verificar si DataTable ya está inicializado
    if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#productsTable')) {
        $('#productsTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleProductStatus(productId, newStatus) {
    // Convertir boolean a número (true -> 1, false -> 0)
    const statusValue = newStatus === true || newStatus === 'true' ? 1 : 0;
    
    console.log('🔄 Toggle Product Status:', {
        productId: productId,
        newStatus: newStatus,
        statusValue: statusValue
    });
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: {
            action: 'toggle_product_status',
            product_id: productId,
            is_available: statusValue
        },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Response:', response);
            if (response.success) {
                showNotification(response.message, 'success');
                location.reload();
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error:', xhr, status, error);
            console.error('❌ Response Text:', xhr.responseText);
            showNotification('Error de conexión', 'error');
        }
    });
}

function deleteProduct(productId, productName) {
    if (confirm(`¿Estás seguro de que quieres eliminar "${productName}"? Esta acción no se puede deshacer.`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_product',
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error de conexión', 'error');
            }
        });
    }
}

function refreshProducts() {
    location.reload();
}
</script>

<?php
// Funciones auxiliares
function getProduct($id) {
    global $pdo;
    return fetchOne("
        SELECT p.*, c.name_en as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ", [$id]);
}

function getAllProducts() {
    return fetchAll("
        SELECT p.*, c.name_en as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
}

function getAllCategories() {
    return fetchAll("
        SELECT * FROM categories 
        WHERE is_active = 1 
        ORDER BY name_en ASC
    ");
}

function createProduct() {
    // Implementar creación de producto
    $name_en = $_POST['name_en'] ?? '';
    $name_es = $_POST['name_es'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $description_en = $_POST['description_en'] ?? '';
    $description_es = $_POST['description_es'] ?? '';
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $sql = "INSERT INTO products (category_id, name_en, name_es, description_en, description_es, price, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$category_id, $name_en, $name_es, $description_en, $description_es, $price, $is_available, $is_featured];
    
    if (executeQuery($sql, $params)) {
        $_SESSION['success_message'] = 'Producto creado exitosamente';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error al crear el producto';
    }
}

function updateProduct() {
    // Implementar actualización de producto
    $product_id = $_POST['product_id'] ?? 0;
    $name_en = $_POST['name_en'] ?? '';
    $name_es = $_POST['name_es'] ?? '';
    $category_id = $_POST['category_id'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $description_en = $_POST['description_en'] ?? '';
    $description_es = $_POST['description_es'] ?? '';
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $sql = "UPDATE products SET category_id = ?, name_en = ?, name_es = ?, description_en = ?, description_es = ?, price = ?, is_available = ?, is_featured = ? WHERE id = ?";
    $params = [$category_id, $name_en, $name_es, $description_en, $description_es, $price, $is_available, $is_featured, $product_id];
    
    if (executeQuery($sql, $params)) {
        $_SESSION['success_message'] = 'Producto actualizado exitosamente';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error al actualizar el producto';
    }
}

function deleteProduct() {
    $product_id = $_POST['product_id'] ?? 0;
    
    if (executeQuery("DELETE FROM products WHERE id = ?", [$product_id])) {
        $_SESSION['success_message'] = 'Producto eliminado exitosamente';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error al eliminar el producto';
    }
}

function toggleProductStatus() {
    $product_id = $_POST['product_id'] ?? 0;
    $is_available = $_POST['is_available'] ?? 0;
    
    if (executeQuery("UPDATE products SET is_available = ? WHERE id = ?", [$is_available, $product_id])) {
        $_SESSION['success_message'] = 'Estado del producto actualizado';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error al actualizar el estado';
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
