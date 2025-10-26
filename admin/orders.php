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
        $order = getOrderDetails($order_id);
        $order_items = getOrderItems($order_id);
        break;
    case 'print':
        $order = getOrderDetails($order_id);
        $order_items = getOrderItems($order_id);
        // Override page title for print view
        $page_title = 'Print Order - ' . $order['order_number'];
        break;
    case 'list':
    default:
        $orders = getAllOrders();
        $status_counts = getOrderStatusCounts();
        break;
}

// Configurar página
$page_title = $action === 'view' ? 'Order Details' : 'Order Management';
$page_scripts = ['assets/js/orders.js'];

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-shopping-cart me-2"></i>Order Management
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshOrders()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="printOrders()">
                    <i class="fas fa-print me-1"></i>Print
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
                                Pending
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
                                Confirmed
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
                                Preparing
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
                                Ready
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
            <h6 class="m-0 font-weight-bold text-primary">Orders List</h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready">Ready</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search orders...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-bordered data-table" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Pickup</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Actions</th>
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
                                        <ul class="dropdown-menu" style="z-index: 9999;">
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')">Confirm</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">Preparing</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')">Ready</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">Completed</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">Cancel</a></li>
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
                    <h6 class="m-0 font-weight-bold text-primary">Order Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Information</h6>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Information</h6>
                            <p><strong>Number:</strong> #<?php echo htmlspecialchars($order['order_number']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></p>
                            <p><strong>Pickup:</strong> <?php echo date('M d, Y g:i A', strtotime($order['pickup_time'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['notes'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Special Instructions</h6>
                            <p><?php echo htmlspecialchars($order['notes']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Customizations</th>
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
                                    <td>
                                        <?php if (!empty($item['customizations'])): 
                                            $customizations = json_decode($item['customizations'], true);
                                        ?>
                                            <div class="customizations-info">
                                                <?php if (!empty($customizations['specialInstructions'])): ?>
                                                    <div class="mb-2">
                                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                                        <strong>Instructions:</strong> <?php echo htmlspecialchars($customizations['specialInstructions']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($customizations['extras']) && is_array($customizations['extras'])): ?>
                                                    <div class="mb-2">
                                                        <i class="fas fa-plus-circle text-success me-1"></i>
                                                        <strong>Extras:</strong>
                                                        <ul class="mb-0">
                                                            <?php foreach ($customizations['extras'] as $extra): ?>
                                                                <li><?php echo htmlspecialchars($extra['name']); ?> (+ $<?php echo number_format($extra['price'], 2); ?>)</li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($customizations['spiceLevel'])): ?>
                                                    <div class="mb-2">
                                                        <i class="fas fa-fire text-danger me-1"></i>
                                                        <strong>Spice Level:</strong> 
                                                        <span class="badge bg-warning"><?php echo htmlspecialchars(ucfirst($customizations['spiceLevel'])); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Subtotal:</th>
                                    <th>$<?php echo number_format($order['subtotal'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4">Tax:</th>
                                    <th>$<?php echo number_format($order['tax'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4">Total:</th>
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
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <!-- Status Update -->
                    <div class="mb-4">
                        <h6 class="text-muted">Change Status</h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <button class="btn btn-outline-info" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'confirmed')">
                                <i class="fas fa-check-circle me-2"></i>Confirm
                            </button>
                            <button class="btn btn-outline-warning" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'preparing')">
                                <i class="fas fa-utensils me-2"></i>Preparing
                            </button>
                            <button class="btn btn-outline-success" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'ready')">
                                <i class="fas fa-check me-2"></i>Ready
                            </button>
                            <button class="btn btn-outline-primary" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                <i class="fas fa-flag-checkered me-2"></i>Completed
                            </button>
                            <button class="btn btn-outline-danger" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                        </div>
                    </div>
                    
                    <!-- Print Order -->
                    <div class="mb-4">
                        <button class="btn btn-primary w-100" onclick="printOrder(<?php echo $order['id']; ?>)">
                            <i class="fas fa-print me-2"></i>Print Order
                        </button>
                    </div>
                    
                    <!-- Order Status -->
                    <div class="mb-4">
                        <h6 class="text-muted">Order Status</h6>
                        <p><strong>Current Status:</strong> 
                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="mb-4">
                        <h6 class="text-muted">Payment Information</h6>
                        <p><strong>Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </p>
                        
                        <?php if ($order['payment_status'] !== 'paid'): ?>
                        <!-- Mark as Paid Button -->
                        <div class="mt-3">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#markPaidModal">
                                <i class="fas fa-credit-card me-1"></i>Mark as Paid
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php elseif ($action === 'print'): ?>
    <!-- Print View -->
    <div class="print-container">
        <div class="print-header text-center mb-4">
            <img src="../assets/images/LOGO.JPG" alt="Restaurant Logo" class="logo-img mb-3" style="height: 80px;">
            <h2>Horchata Mexican Food</h2>
            <p class="text-muted">10814 Jefferson Blvd, Culver City, CA</p>
            <p class="text-muted">Phone: +1 (310) 204-2659</p>
            <hr>
        </div>
        
        <div class="order-info mb-4">
            <div class="row">
                <div class="col-6">
                    <h5>Order Information</h5>
                    <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y g:i A', strtotime($order['created_at'])); ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-<?php echo getStatusColor($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></p>
                </div>
                <div class="col-6">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="order-items mb-4">
            <h5>Order Items</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                            <?php if (!empty($item['customizations'])): 
                                $customizations = json_decode($item['customizations'], true);
                            ?>
                                <div class="customizations-info mt-2">
                                    <?php if (!empty($customizations['specialInstructions'])): ?>
                                        <div><small><strong>Instructions:</strong> <?php echo htmlspecialchars($customizations['specialInstructions']); ?></small></div>
                                    <?php endif; ?>
                                    <?php if (!empty($customizations['extras']) && is_array($customizations['extras'])): ?>
                                        <div><small><strong>Extras:</strong> 
                                            <?php 
                                                $extrasList = [];
                                                foreach ($customizations['extras'] as $extra) {
                                                    $extrasList[] = $extra['name'];
                                                }
                                                echo htmlspecialchars(implode(', ', $extrasList));
                                            ?>
                                        </small></div>
                                    <?php endif; ?>
                                    <?php if (!empty($customizations['spiceLevel'])): ?>
                                        <div><small><strong>Spice Level:</strong> <?php echo htmlspecialchars(ucfirst($customizations['spiceLevel'])); ?></small></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>$<?php echo number_format($order['total'], 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php if (!empty($order['notes'])): ?>
        <div class="order-notes mb-4">
            <h5>Special Instructions</h5>
            <p><?php echo htmlspecialchars($order['notes']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="print-footer text-center mt-5">
            <p class="text-muted"><small>Thank you for your order!</small></p>
        </div>
    </div>
    
    <style>
    @media print {
        .sidebar, .navbar, .btn, .no-print {
            display: none !important;
        }
        .print-container {
            margin: 0;
            padding: 5px;
            font-size: 10px !important;
        }
        body {
            background: white;
            font-size: 10px !important;
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 14px !important;
            margin: 0 !important;
        }
        h5 {
            font-size: 11px !important;
            margin: 5px 0 !important;
        }
        p {
            margin: 2px 0 !important;
            font-size: 9px !important;
        }
        .table {
            font-size: 9px !important;
        }
        .table th, .table td {
            padding: 2px 4px !important;
            font-size: 8px !important;
        }
        .badge {
            font-size: 8px !important;
            padding: 2px 4px !important;
        }
        hr {
            margin: 5px 0 !important;
        }
        .mb-4 {
            margin-bottom: 5px !important;
        }
        .mt-5 {
            margin-top: 10px !important;
        }
        .mb-3 {
            margin-bottom: 3px !important;
        }
        .text-muted {
            font-size: 8px !important;
        }
        .customizations-info {
            font-size: 7px !important;
        }
        .customizations-info strong {
            font-size: 7px !important;
        }
    }
    .print-container {
        max-width: 80mm;
        margin: 0 auto;
        padding: 5px;
        font-size: 10px;
    }
    .logo-img {
        max-width: 120px;
        height: auto;
    }
    .print-header {
        margin-bottom: 5px;
    }
    .order-info h5,
    .order-items h5,
    .order-notes h5 {
        font-size: 11px;
        margin-bottom: 3px;
    }
    </style>
    
    <script>
    window.onload = function() {
        window.print();
    };
    </script>
    
    <?php endif; ?>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Mark Order as Paid
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="markPaidForm">
                    <input type="hidden" id="orderId" value="<?php echo $order['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="">Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="paymentAmount" 
                                   value="<?php echo $order['total']; ?>" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="paymentNotes" rows="3" 
                                  placeholder="Add any payment notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="markOrderAsPaid()">
                    <i class="fas fa-check me-1"></i>Mark as Paid
                </button>
            </div>
        </div>
    </div>
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
            url: 'ajax/admin.ajax.php',
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

function markOrderAsPaid() {
    const orderId = document.getElementById('orderId').value;
    const paymentMethod = document.getElementById('paymentMethod').value;
    const paymentAmount = document.getElementById('paymentAmount').value;
    const paymentNotes = document.getElementById('paymentNotes').value;
    
    if (!paymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    if (!paymentAmount || paymentAmount <= 0) {
        alert('Please enter a valid payment amount');
        return;
    }
    
    if (confirm(`Mark order #${orderId} as paid with ${paymentMethod} for $${paymentAmount}?`)) {
        $.ajax({
            url: 'ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'mark_order_paid',
                order_id: orderId,
                payment_method: paymentMethod,
                payment_amount: paymentAmount,
                payment_notes: paymentNotes
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    // Close modal
                    $('#markPaidModal').modal('hide');
                    // Reload page to show updated status
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
