<?php
/**
 * Horchata Mexican Food - Gestión de Pedidos
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
$order_id = $_GET['id'] ?? 0;
$view = $_GET['view'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'update_status':
            updateOrderStatus();
            break;
        case 'print_order':
            printOrder();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'view':
        $order = getOrderDetails($view);
        $order_items = getOrderItems($view);
        break;
    case 'list':
    default:
        $orders = getAllOrders();
        $status_counts = getOrderStatusCounts();
        break;
}

// Configurar página
$page_title = $action === 'view' ? 'Detalle del Pedido' : 'Gestión de Pedidos';
$page_scripts = ['assets/js/orders.js'];

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-shopping-cart me-2"></i>Gestión de Pedidos
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt me-1"></i>Actualizar
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="printOrders()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Orders List -->
    <div class="row mb-4">
        <!-- Status Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['pending'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Confirmados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['confirmed'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Preparando
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['preparing'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Listos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['ready'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Pedidos</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="preparing">Preparando</option>
                        <option value="ready">Listo</option>
                        <option value="completed">Completado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFilter" placeholder="Filtrar por fecha">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar pedidos...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-bordered data-table" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Pickup</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo $order['item_count']; ?> items</small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($order['pickup_time'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($order['pickup_time'])); ?></small>
                            </td>
                            <td>
                                <strong class="text-primary">$<?php echo number_format($order['total'], 2); ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                                <br>
                                <small class="text-muted"><?php echo ucfirst($order['payment_method']); ?></small>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="orders.php?action=view&id=<?php echo $order['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown" title="Cambiar Estado">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')">Confirmar</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">Preparando</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')">Listo</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">Completado</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">Cancelar</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($action === 'view'): ?>
    <!-- Order Details -->
    <div class="row">
        <!-- Order Info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles del Pedido</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Información del Cliente</h6>
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Información del Pedido</h6>
                            <p><strong>Número:</strong> #<?php echo htmlspecialchars($order['order_number']); ?></p>
                            <p><strong>Fecha:</strong> <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></p>
                            <p><strong>Pickup:</strong> <?php echo date('M d, Y g:i A', strtotime($order['pickup_time'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Instrucciones Especiales</h6>
                            <p><?php echo htmlspecialchars($order['notes']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Items del Pedido</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    </td>
                                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Subtotal:</th>
                                    <th>$<?php echo number_format($order['subtotal'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Impuestos:</th>
                                    <th>$<?php echo number_format($order['tax'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Total:</th>
                                    <th class="text-primary">$<?php echo number_format($order['total'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones</h6>
                </div>
                <div class="card-body">
                    <!-- Status Update -->
                    <div class="mb-4">
                        <h6 class="text-muted">Cambiar Estado</h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <button class="btn btn-outline-info" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')">
                                <i class="fas fa-check-circle me-2"></i>Confirmar
                            </button>
                            <button class="btn btn-outline-warning" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">
                                <i class="fas fa-utensils me-2"></i>Preparando
                            </button>
                            <button class="btn btn-outline-success" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')">
                                <i class="fas fa-check me-2"></i>Listo
                            </button>
                            <button class="btn btn-outline-primary" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                <i class="fas fa-flag-checkered me-2"></i>Completado
                            </button>
                            <button class="btn btn-outline-danger" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Print Order -->
                    <div class="mb-4">
                        <button class="btn btn-primary w-100" onclick="printOrder(<?php echo $order['id']; ?>)">
                            <i class="fas fa-print me-2"></i>Imprimir Pedido
                        </button>
                    </div>
                    
                    <!-- Order Info -->
                    <div class="mb-4">
                        <h6 class="text-muted">Información de Pago</h6>
                        <p><strong>Método:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para pedidos -->
<script>
$(document).ready(function() {
    // Configurar filtros
    setupFilters();
    
    // Configurar DataTable
    setupOrdersTable();
});

function setupFilters() {
    $('#statusFilter, #dateFilter, #searchInput').on('change keyup', function() {
        applyFilters();
    });
}

function applyFilters() {
    const status = $('#statusFilter').val();
    const date = $('#dateFilter').val();
    const search = $('#searchInput').val();
    
    // Aplicar filtros a DataTable
    const table = $('#ordersTable').DataTable();
    table.column(4).search(status);
    table.column(2).search(date);
    table.search(search).draw();
}

function setupOrdersTable() {
    // DataTable ya se inicializa automáticamente
}

function updateOrderStatus(orderId, newStatus) {
    if (confirm(`¿Cambiar el estado del pedido a ${newStatus}?`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'update_order_status',
                order_id: orderId,
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
                showNotification('Error de conexión', 'error');
            }
        });
    }
}

function printOrder(orderId) {
    window.open(`orders.php?action=print&id=${orderId}`, '_blank');
}

function printOrders() {
    window.print();
}

function refreshOrders() {
    location.reload();
}
</script>

<?php
// Funciones auxiliares
function getAllOrders() {
    return fetchAll("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
}

function getOrderStatusCounts() {
    $counts = fetchAll("
        SELECT status, COUNT(*) as count
        FROM orders
        GROUP BY status
    ");
    
    $result = [];
    foreach ($counts as $count) {
        $result[$count['status']] = $count['count'];
    }
    
    return $result;
}

function getOrderDetails($order_id) {
    return fetchOne("
        SELECT * FROM orders WHERE id = ?
    ", [$order_id]);
}

function getOrderItems($order_id) {
    return fetchAll("
        SELECT * FROM order_items WHERE order_id = ?
    ", [$order_id]);
}

function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'preparing' => 'primary',
        'ready' => 'success',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function updateOrderStatus() {
    $order_id = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (executeQuery("UPDATE orders SET status = ? WHERE id = ?", [$status, $order_id])) {
        $_SESSION['success_message'] = 'Estado del pedido actualizado';
        header('Location: orders.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error al actualizar el estado';
    }
}

function printOrder() {
    // Implementar impresión de pedido
    $order_id = $_POST['order_id'] ?? 0;
    // Lógica de impresión
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
