<?php
/**
 * Horchata Mexican Food - Reports Management
 * Admin Panel
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
$report_type = $_GET['type'] ?? 'dashboard';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Obtener datos del reporte
$report_data = getReportData($report_type, $date_from, $date_to);

// Configurar página
$page_title = 'Reports & Analytics';
$page_scripts = []; // JavaScript está inline en la página

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshReports()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="exportReport()">
                    <i class="fas fa-download me-1"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select class="form-select" id="report_type" name="type">
                                <option value="dashboard" <?php echo $report_type === 'dashboard' ? 'selected' : ''; ?>>Dashboard Overview</option>
                                <option value="sales" <?php echo $report_type === 'sales' ? 'selected' : ''; ?>>Sales Report</option>
                                <option value="orders" <?php echo $report_type === 'orders' ? 'selected' : ''; ?>>Orders Report</option>
                                <option value="products" <?php echo $report_type === 'products' ? 'selected' : ''; ?>>Products Report</option>
                                <option value="customers" <?php echo $report_type === 'customers' ? 'selected' : ''; ?>>Customers Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Overview -->
    <?php if ($report_type === 'dashboard'): ?>
    <div class="row mb-4">
        <!-- Key Metrics -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($report_data['total_revenue'], 2); ?>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $report_data['total_orders']; ?>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($report_data['avg_order_value'], 2); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $report_data['total_products']; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Order Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sales Report -->
    <?php if ($report_type === 'sales'): ?>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Report</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                    <th>Average Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['daily_sales'] as $day): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                    <td><?php echo $day['orders']; ?></td>
                                    <td>$<?php echo number_format($day['revenue'], 2); ?></td>
                                    <td>$<?php echo number_format($day['avg_order_value'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Orders Report -->
    <?php if ($report_type === 'orders'): ?>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Orders Report</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['orders'] as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($order['total'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Products Report -->
    <?php if ($report_type === 'products'): ?>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Products Report</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['products'] as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Customers Report -->
    <?php if ($report_type === 'customers'): ?>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customers Report</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['customers'] as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                                    <td><?php echo $customer['order_count']; ?></td>
                                    <td>$<?php echo number_format($customer['total_spent'], 2); ?></td>
                                    <td><?php echo $customer['last_order'] ? date('M d, Y', strtotime($customer['last_order'])) : 'Never'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para reportes -->
<script>
$(document).ready(function() {
    // Inicializar gráficos
    initCharts();
});

function initCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($report_data['chart_labels'] ?? []); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($report_data['chart_data'] ?? []); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($report_data['status_labels'] ?? []); ?>,
                datasets: [{
                    data: <?php echo json_encode($report_data['status_data'] ?? []); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    }
}

function refreshReports() {
    location.reload();
}

function exportReport() {
    const reportType = document.getElementById('report_type').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    const url = `reports.php?type=${reportType}&date_from=${dateFrom}&date_to=${dateTo}&export=1`;
    window.open(url, '_blank');
}
</script>

<?php
// Funciones auxiliares
function getReportData($type, $date_from, $date_to) {
    global $pdo;
    
    switch ($type) {
        case 'dashboard':
            return getDashboardData($date_from, $date_to);
        case 'sales':
            return getSalesData($date_from, $date_to);
        case 'orders':
            return getOrdersData($date_from, $date_to);
        case 'products':
            return getProductsData();
        case 'customers':
            return getCustomersData($date_from, $date_to);
        default:
            return getDashboardData($date_from, $date_to);
    }
}

function getDashboardData($date_from, $date_to) {
    global $pdo;
    
    // Total revenue
    $sql = "SELECT SUM(total) as total_revenue FROM orders 
            WHERE created_at BETWEEN ? AND ? AND status != 'cancelled'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    $revenue = $stmt->fetch()['total_revenue'] ?? 0;
    
    // Total orders
    $sql = "SELECT COUNT(*) as total_orders FROM orders 
            WHERE created_at BETWEEN ? AND ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    $orders = $stmt->fetch()['total_orders'] ?? 0;
    
    // Average order value
    $avg_order_value = $orders > 0 ? $revenue / $orders : 0;
    
    // Total products
    $sql = "SELECT COUNT(*) as total_products FROM products WHERE is_active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetch()['total_products'] ?? 0;
    
    // Chart data (last 7 days)
    $chart_labels = [];
    $chart_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = date('M d', strtotime($date));
        
        $sql = "SELECT SUM(total) as daily_revenue FROM orders 
                WHERE DATE(created_at) = ? AND status != 'cancelled'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date]);
        $daily_revenue = $stmt->fetch()['daily_revenue'] ?? 0;
        $chart_data[] = $daily_revenue;
    }
    
    // Order status distribution
    $sql = "SELECT status, COUNT(*) as count FROM orders 
            WHERE created_at BETWEEN ? AND ? 
            GROUP BY status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    $status_data = $stmt->fetchAll();
    
    $status_labels = [];
    $status_counts = [];
    foreach ($status_data as $status) {
        $status_labels[] = ucfirst($status['status']);
        $status_counts[] = $status['count'];
    }
    
    return [
        'total_revenue' => $revenue,
        'total_orders' => $orders,
        'avg_order_value' => $avg_order_value,
        'total_products' => $products,
        'chart_labels' => $chart_labels,
        'chart_data' => $chart_data,
        'status_labels' => $status_labels,
        'status_data' => $status_counts
    ];
}

function getSalesData($date_from, $date_to) {
    global $pdo;
    
    $sql = "SELECT DATE(created_at) as date, 
                   COUNT(*) as orders,
                   SUM(total) as revenue,
                   AVG(total) as avg_order_value
            FROM orders 
            WHERE created_at BETWEEN ? AND ? AND status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    return ['daily_sales' => $stmt->fetchAll()];
}

function getOrdersData($date_from, $date_to) {
    global $pdo;
    
    $sql = "SELECT * FROM orders 
            WHERE created_at BETWEEN ? AND ?
            ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    return ['orders' => $stmt->fetchAll()];
}

function getProductsData() {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return ['products' => $stmt->fetchAll()];
}

function getCustomersData($date_from, $date_to) {
    global $pdo;
    
    $sql = "SELECT customer_name, customer_email,
                   COUNT(*) as order_count,
                   SUM(total) as total_spent,
                   MAX(created_at) as last_order
            FROM orders 
            WHERE created_at BETWEEN ? AND ?
            GROUP BY customer_name, customer_email
            ORDER BY total_spent DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$date_from, $date_to]);
    return ['customers' => $stmt->fetchAll()];
}

function getStatusColor($status) {
    switch ($status) {
        case 'completed': return 'success';
        case 'confirmed': return 'info';
        case 'preparing': return 'warning';
        case 'ready': return 'primary';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
