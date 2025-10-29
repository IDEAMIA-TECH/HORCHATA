<?php
/**
 * Horchata Mexican Food - Dashboard Administrativo
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';
require_once '../includes/init.php';

// Obtener estadísticas del dashboard
$stats = [];

// Órdenes del día
$today_orders = fetchOne("
    SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as revenue
    FROM orders 
    WHERE DATE(created_at) = CURDATE()
") ?: ['count' => 0, 'revenue' => 0];

// Órdenes pendientes
$pending_orders = fetchOne("
    SELECT COUNT(*) as count
    FROM orders 
    WHERE status IN ('pending', 'confirmed')
") ?: ['count' => 0];

// Total de productos
$total_products = fetchOne("
    SELECT COUNT(*) as count
    FROM products 
    WHERE is_available = 1
") ?: ['count' => 0];

// Reseñas pendientes
$pending_reviews = fetchOne("
    SELECT COUNT(*) as count
    FROM reviews 
    WHERE is_approved = 0
") ?: ['count' => 0];

// Órdenes recientes
$recent_orders = fetchAll("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
") ?: [];

// Productos más vendidos
$top_products = fetchAll("
    SELECT p.name_en, p.image, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status != 'cancelled'
    GROUP BY p.id, p.name_en, p.image
    ORDER BY total_sold DESC
    LIMIT 5
") ?: [];

// Configurar página
$page_title = __('dashboard');
$page_scripts = ['assets/js/dashboard.js'];

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Dashboard Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tachometer-alt me-2"></i><?php echo __('dashboard'); ?>
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt me-1"></i><?php echo __('refresh'); ?>
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i><?php echo __('print'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?php echo __('orders_today'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $today_orders['count']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                <?php echo __('revenue_today'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($today_orders['revenue'], 2); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                <?php echo __('pending_orders'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $pending_orders['count']; ?>
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
                                <?php echo __('pending_reviews'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $pending_reviews['count']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Orders Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('orders_by_day'); ?></h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('payment_methods'); ?></h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('recent_orders'); ?></h6>
                    <a href="orders.php" class="btn btn-sm btn-primary"><?php echo __('view_all'); ?></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recentOrdersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><?php echo __('order'); ?></th>
                                    <th><?php echo __('customer'); ?></th>
                                    <th><?php echo __('total'); ?></th>
                                    <th><?php echo __('status'); ?></th>
                                    <th><?php echo __('date'); ?></th>
                                    <th><?php echo __('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo $order['item_count']; ?> <?php echo __('items'); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($order['customer_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                    </td>
                                    <td>
                                        <strong>$<?php echo number_format($order['total'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                        <br>
                                        <small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('top_products'); ?></h6>
                    <a href="products.php" class="btn btn-sm btn-primary"><?php echo __('view_all_products'); ?></a>
                </div>
                <div class="card-body">
                    <?php foreach ($top_products as $product): ?>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo $product['image'] ? str_replace('../', '', $product['image']) : '../assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name_en']); ?>" 
                             class="rounded me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name_en']); ?></h6>
                            <small class="text-muted">
                                <?php echo $product['total_sold']; ?> <?php echo __('sold'); ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <strong>$<?php echo number_format($product['revenue'], 2); ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript específico para dashboard -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Inicializar gráficas
    initCharts();
    
    // Configurar auto-refresh
    setupAutoRefresh();
});

function initCharts() {
    // Gráfica de órdenes
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: '<?php echo __('orders'); ?>',
                data: [12, 19, 3, 5, 2, 3, 8],
                borderColor: 'rgb(212, 175, 55)',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfica de métodos de pago
    const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['PayPal', '<?php echo __('pay_on_pickup'); ?>'],
            datasets: [{
                data: [65, 35],
                backgroundColor: ['#d4af37', '#ff6b35'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function setupAutoRefresh() {
    // Auto-refresh cada 5 minutos
    setInterval(function() {
        refreshDashboard();
    }, 300000);
}

function refreshDashboard() {
    // Mostrar indicador de carga
    $('.btn-outline-secondary').html('<i class="fas fa-spinner fa-spin me-1"></i><?php echo __('updating'); ?>');
    
    // Recargar página después de un breve delay
    setTimeout(function() {
        location.reload();
    }, 1000);
}

// Función para obtener color del estado
function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'info',
        'preparing': 'primary',
        'ready': 'success',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}
</script>

<!-- Estilos adicionales para dashboard -->
<style>
.border-left-primary {
    border-left: 0.25rem solid #d4af37 !important;
}

.border-left-success {
    border-left: 0.25rem solid #28a745 !important;
}

.border-left-warning {
    border-left: 0.25rem solid #ffc107 !important;
}

.border-left-info {
    border-left: 0.25rem solid #17a2b8 !important;
}

.text-xs {
    font-size: 0.7rem;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.chart-area {
    position: relative;
    height: 10rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

@media (max-width: 768px) {
    .chart-area, .chart-pie {
        height: 8rem;
    }
}
</style>

<?php
// Función auxiliar para obtener color del estado
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

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
