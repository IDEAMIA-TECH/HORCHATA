<?php
// =============================================
// Horchata Mexican Food - Administración de Extras
// Panel para gestionar extras de productos
// =============================================

require_once '../includes/init.php';
require_once '../includes/db_connect.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$page_title = __('admin_panel') . ' - ' . __('extras_management');
$action = $_GET['action'] ?? 'list';

// Procesar acciones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'create_extra':
                $name_en = trim($_POST['name_en']);
                $name_es = trim($_POST['name_es']);
                $price = floatval($_POST['price']);
                $category_id = intval($_POST['category_id']);
                
                if (empty($name_en) || empty($name_es) || $price <= 0) {
                    throw new Exception('Datos inválidos');
                }
                
                $sql = "INSERT INTO product_extras (name_en, name_es, price, category_id, sort_order) VALUES (?, ?, ?, ?, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name_en, $name_es, $price, $category_id]);
                
                echo json_encode(['success' => true, 'message' => 'Extra creado exitosamente']);
                break;
                
            case 'update_extra':
                $id = intval($_POST['id']);
                $name_en = trim($_POST['name_en']);
                $name_es = trim($_POST['name_es']);
                $price = floatval($_POST['price']);
                $category_id = intval($_POST['category_id']);
                
                if (empty($name_en) || empty($name_es) || $price <= 0) {
                    throw new Exception('Datos inválidos');
                }
                
                $sql = "UPDATE product_extras SET name_en = ?, name_es = ?, price = ?, category_id = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name_en, $name_es, $price, $category_id, $id]);
                
                echo json_encode(['success' => true, 'message' => 'Extra actualizado exitosamente']);
                break;
                
            case 'delete_extra':
                $id = intval($_POST['id']);
                
                $sql = "DELETE FROM product_extras WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Extra eliminado exitosamente']);
                break;
                
            case 'toggle_extra_status':
                $id = intval($_POST['id']);
                $is_active = intval($_POST['is_active']);
                
                $sql = "UPDATE product_extras SET is_active = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$is_active, $id]);
                
                echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
                break;
                
            case 'assign_extra_to_product':
                $product_id = intval($_POST['product_id']);
                $extra_id = intval($_POST['extra_id']);
                
                $sql = "INSERT INTO product_extra_relations (product_id, extra_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_active = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$product_id, $extra_id]);
                
                echo json_encode(['success' => true, 'message' => 'Extra asignado al producto']);
                break;
                
            case 'remove_extra_from_product':
                $product_id = intval($_POST['product_id']);
                $extra_id = intval($_POST['extra_id']);
                
                $sql = "UPDATE product_extra_relations SET is_active = 0 WHERE product_id = ? AND extra_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$product_id, $extra_id]);
                
                echo json_encode(['success' => true, 'message' => 'Extra removido del producto']);
                break;
                
            case 'get_product_extras':
                $product_id = intval($_GET['product_id']);
                
                // Obtener todos los extras disponibles
                $sql = "SELECT id, name_en, name_es, price FROM product_extras WHERE is_active = 1 ORDER BY name_en";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $available_extras = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Obtener extras asignados al producto
                $sql = "SELECT extra_id FROM product_extra_relations WHERE product_id = ? AND is_active = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$product_id]);
                $assigned_extras = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'product_id' => $product_id,
                        'available' => $available_extras,
                        'assigned' => $assigned_extras
                    ]
                ]);
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Obtener datos para la página
$extras = [];
$categories = [];
$products = [];

try {
    // Obtener extras con sus categorías
    $sql = "SELECT e.*, c.name_en as category_name_en, c.name_es as category_name_es 
            FROM product_extras e 
            LEFT JOIN extra_categories c ON e.category_id = c.id 
            ORDER BY c.sort_order, e.sort_order, e.name_en";
    $stmt = $pdo->query($sql);
    $extras = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener categorías de extras
    $sql = "SELECT * FROM extra_categories WHERE is_active = 1 ORDER BY sort_order";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener productos para asignación
    $sql = "SELECT p.id, p.name_en, p.name_es, c.name_en as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.is_available = 1 
            ORDER BY c.sort_order, p.name_en";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error al cargar datos: " . $e->getMessage();
}

include '../includes/admin-header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo __('extras_management'); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#extraModal">
                        <i class="fas fa-plus"></i> <?php echo __('add_extra'); ?>
                    </button>
                </div>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Lista de Extras -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo __('extras_list'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="extrasTable">
                            <thead>
                                <tr>
                                    <th><?php echo __('name'); ?></th>
                                    <th><?php echo __('category'); ?></th>
                                    <th><?php echo __('price'); ?></th>
                                    <th><?php echo __('status'); ?></th>
                                    <th><?php echo __('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($extras as $extra): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($extra['name_en']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($extra['name_es']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($extra['category_name_en']): ?>
                                            <?php echo htmlspecialchars($extra['category_name_en']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin categoría</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($extra['price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $extra['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $extra['is_active'] ? __('active') : __('inactive'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editExtra(<?php echo htmlspecialchars(json_encode($extra)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-<?php echo $extra['is_active'] ? 'warning' : 'success'; ?>" 
                                                    onclick="toggleExtraStatus(<?php echo $extra['id']; ?>, <?php echo $extra['is_active'] ? 'false' : 'true'; ?>)">
                                                <i class="fas fa-<?php echo $extra['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteExtra(<?php echo $extra['id']; ?>)">
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

            <!-- Asignación de Extras a Productos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo __('assign_extras_to_products'); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?php echo __('select_product'); ?></h6>
                            <select class="form-select" id="productSelect">
                                <option value=""><?php echo __('select_product'); ?></option>
                                <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>">
                                    <?php echo htmlspecialchars($product['name_en']); ?> (<?php echo htmlspecialchars($product['category_name']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h6><?php echo __('available_extras'); ?></h6>
                            <div id="availableExtras">
                                <p class="text-muted"><?php echo __('select_product_first'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal para crear/editar extra -->
<div class="modal fade" id="extraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extraModalTitle"><?php echo __('add_extra'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extraForm">
                <div class="modal-body">
                    <input type="hidden" id="extraId" name="id">
                    <input type="hidden" name="action" id="extraAction" value="create_extra">
                    
                    <div class="mb-3">
                        <label for="extraNameEn" class="form-label"><?php echo __('name_en'); ?></label>
                        <input type="text" class="form-control" id="extraNameEn" name="name_en" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="extraNameEs" class="form-label"><?php echo __('name_es'); ?></label>
                        <input type="text" class="form-control" id="extraNameEs" name="name_es" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="extraPrice" class="form-label"><?php echo __('price'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="extraPrice" name="price" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="extraCategory" class="form-label"><?php echo __('category'); ?></label>
                        <select class="form-select" id="extraCategory" name="category_id">
                            <option value=""><?php echo __('no_category'); ?></option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name_en']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#extrasTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pageLength": 25,
        "order": [[0, "asc"]]
    });
    
    // Manejar formulario de extra
    $('#extraForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = $('#extraAction').val();
        
        $.ajax({
            url: 'extras.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error del servidor');
            }
        });
    });
    
    // Manejar selección de producto
    $('#productSelect').on('change', function() {
        const productId = $(this).val();
        if (productId) {
            loadProductExtras(productId);
        } else {
            $('#availableExtras').html('<p class="text-muted"><?php echo __('select_product_first'); ?></p>');
        }
    });
});

function editExtra(extra) {
    $('#extraModalTitle').text('<?php echo __('edit_extra'); ?>');
    $('#extraId').val(extra.id);
    $('#extraNameEn').val(extra.name_en);
    $('#extraNameEs').val(extra.name_es);
    $('#extraPrice').val(extra.price);
    $('#extraCategory').val(extra.category_id);
    $('#extraAction').val('update_extra');
    $('#extraModal').modal('show');
}

function deleteExtra(id) {
    if (confirm('<?php echo __('confirm_delete_extra'); ?>')) {
        $.ajax({
            url: 'extras.php',
            type: 'POST',
            data: {
                action: 'delete_extra',
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
}

function toggleExtraStatus(id, newStatus) {
    $.ajax({
        url: 'extras.php',
        type: 'POST',
        data: {
            action: 'toggle_extra_status',
            id: id,
            is_active: newStatus ? 1 : 0
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function loadProductExtras(productId) {
    $.ajax({
        url: 'extras.php',
        type: 'GET',
        data: {
            action: 'get_product_extras',
            product_id: productId
        },
        success: function(response) {
            if (response.success) {
                displayProductExtras(response.data);
            } else {
                $('#availableExtras').html('<p class="text-danger">Error: ' + response.message + '</p>');
            }
        }
    });
}

function displayProductExtras(data) {
    let html = '<div class="row">';
    
    data.available.forEach(extra => {
        html += `
            <div class="col-md-6 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="extra_${extra.id}" 
                           ${data.assigned.includes(extra.id) ? 'checked' : ''} 
                           onchange="toggleProductExtra(${data.product_id}, ${extra.id}, this.checked)">
                    <label class="form-check-label" for="extra_${extra.id}">
                        ${extra.name_en} (+$${extra.price})
                    </label>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#availableExtras').html(html);
}

function toggleProductExtra(productId, extraId, assign) {
    const action = assign ? 'assign_extra_to_product' : 'remove_extra_from_product';
    
    $.ajax({
        url: 'extras.php',
        type: 'POST',
        data: {
            action: action,
            product_id: productId,
            extra_id: extraId
        },
        success: function(response) {
            if (!response.success) {
                alert('Error: ' + response.message);
                // Revertir checkbox
                $('#extra_' + extraId).prop('checked', !assign);
            }
        }
    });
}

// Limpiar modal al cerrar
$('#extraModal').on('hidden.bs.modal', function() {
    $('#extraForm')[0].reset();
    $('#extraId').val('');
    $('#extraAction').val('create_extra');
    $('#extraModalTitle').text('<?php echo __('add_extra'); ?>');
});
</script>

<?php include '../includes/admin-footer.php'; ?>
