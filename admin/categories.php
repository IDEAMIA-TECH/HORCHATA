<?php
/**
 * Horchata Mexican Food - Categories Management
 * Admin Panel
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Verificar que el usuario es administrador
if (($_SESSION['admin_role'] ?? 'staff') !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

// Obtener parámetros
$action = $_GET['action'] ?? 'list';
$category_id = $_GET['id'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'create':
            createCategory();
            break;
        case 'update':
            updateCategory();
            break;
        case 'delete':
            deleteCategory();
            break;
        case 'toggle_status':
            toggleCategoryStatus();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'create':
    case 'edit':
        $category = $action === 'edit' ? getCategory($category_id) : null;
        break;
    case 'list':
    default:
        $categories = getAllCategories();
        break;
}

// Configurar página
$page_title = $action === 'edit' ? 'Edit Category' : ($action === 'create' ? 'Create Category' : 'Categories Management');
$page_scripts = []; // JavaScript está inline en la página

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tags me-2"></i>Categories Management
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshCategories()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="btn-group">
                <a href="categories.php?action=create" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Category
                </a>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Categories Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search categories...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="table-responsive">
                <table class="table table-bordered data-table" id="categoriesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name (EN)</th>
                            <th>Name (ES)</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name_en']); ?></td>
                            <td><?php echo htmlspecialchars($category['name_es']); ?></td>
                            <td>
                                <div class="category-description" style="max-width: 200px;">
                                    <?php echo htmlspecialchars(substr($category['description_en'], 0, 50)); ?>
                                    <?php if (strlen($category['description_en']) > 50): ?>...<?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo getCategoryProductCount($category['id']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $category['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $category['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="categories.php?action=edit&id=<?php echo $category['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-<?php echo $category['is_active'] ? 'warning' : 'success'; ?>" 
                                            onclick="toggleCategoryStatus(<?php echo $category['id']; ?>, <?php echo $category['is_active'] ? 'false' : 'true'; ?>)" 
                                            title="<?php echo $category['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fas fa-<?php echo $category['is_active'] ? 'pause' : 'play'; ?>"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteCategory(<?php echo $category['id']; ?>)" title="Delete">
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
    <!-- Category Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php echo $action === 'create' ? 'Create New Category' : 'Edit Category'; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form id="categoryForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Name (English) *</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en" 
                                           value="<?php echo $category['name_en'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_es" class="form-label">Name (Spanish) *</label>
                                    <input type="text" class="form-control" id="name_es" name="name_es" 
                                           value="<?php echo $category['name_es'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_en" class="form-label">Description (English)</label>
                                    <textarea class="form-control" id="description_en" name="description_en" rows="3"><?php echo $category['description_en'] ?? ''; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_es" class="form-label">Description (Spanish)</label>
                                    <textarea class="form-control" id="description_es" name="description_es" rows="3"><?php echo $category['description_es'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon Class</label>
                                    <input type="text" class="form-control" id="icon" name="icon" 
                                           value="<?php echo $category['icon'] ?? ''; ?>" 
                                           placeholder="fas fa-utensils">
                                    <div class="form-text">Font Awesome icon class (e.g., fas fa-utensils)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" class="form-control" id="color" name="color" 
                                           value="<?php echo $category['color'] ?? '#007bff'; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Category Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if ($action === 'edit' && !empty($category['image'])): ?>
                            <div class="mt-2">
                                <img src="../<?php echo $category['image']; ?>" alt="Current image" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?php echo ($category['is_active'] ?? true) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Category
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="categories.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Categories
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo $action === 'create' ? 'Create Category' : 'Update Category'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Category Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Categories help organize your products and make it easier for customers to find what they're looking for.
                    </p>
                    
                    <h6>Tips:</h6>
                    <ul class="small">
                        <li>Use descriptive names in both languages</li>
                        <li>Choose appropriate icons and colors</li>
                        <li>Keep descriptions concise but informative</li>
                        <li>Upload high-quality images for better presentation</li>
                    </ul>
                    
                    <?php if ($action === 'edit'): ?>
                    <hr>
                    <h6>Category Stats:</h6>
                    <p><strong>Products:</strong> <?php echo getCategoryProductCount($category['id']); ?></p>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($category['created_at'])); ?></p>
                    <p><strong>Updated:</strong> <?php echo $category['updated_at'] ? date('M d, Y', strtotime($category['updated_at'])) : 'Never'; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para categorías -->
<script>
$(document).ready(function() {
    // Prevenir múltiples inicializaciones con un flag
    if (!window.categoriesTableInitialized) {
        // Verificar si DataTable ya está inicializado
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#categoriesTable')) {
            $('#categoriesTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "Show _MENU_ categories per page",
                "zeroRecords": "No categories found",
                "info": "Showing _START_ to _END_ of _TOTAL_ categories",
                "infoEmpty": "No categories available",
                "infoFiltered": "(filtered from _MAX_ total categories)",
                "search": "Search:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
            });
            window.categoriesTableInitialized = true;
        }
    }
    
    // Configurar formulario
    setupCategoryForm();
});

function setupCategoryForm() {
    // Auto-save del formulario
    $('#categoryForm input, #categoryForm textarea, #categoryForm select').on('change', function() {
        autoSaveCategory();
    });
    
    // Manejar envío del formulario
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        saveCategory();
    });
}

function refreshCategories() {
    location.reload();
}

function autoSaveCategory() {
    const formData = new FormData($('#categoryForm')[0]);
    formData.append('action', 'auto_save_category');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSaveIndicator();
            }
        },
        error: function() {
            // No mostrar error en auto-save
        }
    });
}

function saveCategory() {
    const formData = new FormData($('#categoryForm')[0]);
    
    // Mostrar loading
    const submitBtn = $('#categoryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                setTimeout(function() {
                    window.location.href = 'categories.php';
                }, 1500);
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Connection error', 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

function toggleCategoryStatus(categoryId, newStatus) {
    const action = newStatus === 'true' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this category?`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'toggle_category_status',
                category_id: categoryId,
                status: newStatus
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
                showNotification('Connection error', 'error');
            }
        });
    }
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_category',
                category_id: categoryId
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
                showNotification('Connection error', 'error');
            }
        });
    }
}

function showSaveIndicator() {
    let indicator = $('#saveIndicator');
    if (indicator.length === 0) {
        indicator = $('<div id="saveIndicator" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>');
        $('body').append(indicator);
    }
    
    indicator.html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check me-1"></i>Auto-saved
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    setTimeout(function() {
        indicator.find('.alert').alert('close');
    }, 3000);
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                     type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}
</script>

<?php
// Funciones auxiliares
function getAllCategories() {
    global $pdo;
    
    $sql = "SELECT * FROM categories ORDER BY name_en ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategory($id) {
    global $pdo;
    
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCategoryProductCount($category_id) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);
    return $stmt->fetch()['count'] ?? 0;
}

function createCategory() {
    global $pdo;
    
    $name_en = trim($_POST['name_en'] ?? '');
    $name_es = trim($_POST['name_es'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $description_es = trim($_POST['description_es'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $color = trim($_POST['color'] ?? '#007bff');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name_en) || empty($name_es)) {
        throw new Exception('Category names are required');
    }
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/categories/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'category_' . time() . '.' . $file_extension;
        $image_path = 'assets/images/categories/' . $filename;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path)) {
            throw new Exception('Error uploading image');
        }
    }
    
    $sql = "INSERT INTO categories (name_en, name_es, description_en, description_es, icon, color, image, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$name_en, $name_es, $description_en, $description_es, $icon, $color, $image_path, $is_active])) {
        echo json_encode([
            'success' => true,
            'message' => 'Category created successfully'
        ]);
    } else {
        throw new Exception('Error creating category');
    }
}

function updateCategory() {
    global $pdo;
    
    $category_id = (int)($_POST['category_id'] ?? 0);
    $name_en = trim($_POST['name_en'] ?? '');
    $name_es = trim($_POST['name_es'] ?? '');
    $description_en = trim($_POST['description_en'] ?? '');
    $description_es = trim($_POST['description_es'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $color = trim($_POST['color'] ?? '#007bff');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($category_id <= 0) {
        throw new Exception('Invalid category ID');
    }
    
    if (empty($name_en) || empty($name_es)) {
        throw new Exception('Category names are required');
    }
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/categories/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'category_' . time() . '.' . $file_extension;
        $image_path = 'assets/images/categories/' . $filename;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path)) {
            throw new Exception('Error uploading image');
        }
    }
    
    $sql = "UPDATE categories SET name_en = ?, name_es = ?, description_en = ?, description_es = ?, 
            icon = ?, color = ?, is_active = ?, updated_at = NOW()";
    $params = [$name_en, $name_es, $description_en, $description_es, $icon, $color, $is_active];
    
    if (!empty($image_path)) {
        $sql .= ", image = ?";
        $params[] = $image_path;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $category_id;
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    } else {
        throw new Exception('Error updating category');
    }
}

function deleteCategory() {
    global $pdo;
    
    $category_id = (int)($_POST['category_id'] ?? 0);
    
    if ($category_id <= 0) {
        throw new Exception('Invalid category ID');
    }
    
    // Check if category has products
    $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);
    $product_count = $stmt->fetch()['count'];
    
    if ($product_count > 0) {
        throw new Exception('Cannot delete category with products. Please move or delete products first.');
    }
    
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$category_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    } else {
        throw new Exception('Error deleting category');
    }
}

function toggleCategoryStatus() {
    global $pdo;
    
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = $_POST['status'] === 'true' ? 1 : 0;
    
    if ($category_id <= 0) {
        throw new Exception('Invalid category ID');
    }
    
    $sql = "UPDATE categories SET is_active = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$status, $category_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Category status updated successfully'
        ]);
    } else {
        throw new Exception('Error updating category status');
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
