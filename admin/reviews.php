<?php
/**
 * Horchata Mexican Food - Reviews Management
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
require_once '../includes/init.php';

// Obtener parámetros
$action = $_GET['action'] ?? 'list';
$review_id = $_GET['id'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'approve':
            approveReview();
            break;
        case 'reject':
            rejectReview();
            break;
        case 'delete':
            deleteReview();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'view':
        $review = getReview($review_id);
        break;
    case 'list':
    default:
        $reviews = getAllReviews();
        $status_counts = getReviewStatusCounts();
        break;
}

// Configurar página
$page_title = $action === 'view' ? __('review_details') : __('reviews_management');
$page_scripts = []; // No necesita script adicional

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-star me-2"></i><?php echo __('reviews_management'); ?>
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshReviews()">
                    <i class="fas fa-sync-alt me-1"></i><?php echo __('refresh'); ?>
                </button>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Reviews List -->
    <div class="row mb-4">
        <!-- Status Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <?php echo __('pending'); ?>
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <?php echo __('approved'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['approved'] ?? 0; ?>
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <?php echo __('rejected'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['rejected'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                <?php echo __('total'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['total'] ?? 0; ?>
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

    <!-- Reviews Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo __('reviews_list'); ?></h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value=""><?php echo __('all_statuses'); ?></option>
                        <option value="pending"><?php echo __('pending'); ?></option>
                        <option value="approved"><?php echo __('approved'); ?></option>
                        <option value="rejected"><?php echo __('rejected'); ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="<?php echo __('search_reviews'); ?>">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i><?php echo __('filter'); ?>
                    </button>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="table-responsive">
                <table class="table table-bordered data-table" id="reviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo __('customer'); ?></th>
                            <th><?php echo __('rating'); ?></th>
                            <th><?php echo __('comment'); ?></th>
                            <th><?php echo __('status'); ?></th>
                            <th><?php echo __('date'); ?></th>
                            <th><?php echo __('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($review['customer_email']); ?></small>
                            </td>
                            <td>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <div class="review-text" style="max-width: 300px;">
                                    <?php echo htmlspecialchars(substr($review['comment'], 0, 100)); ?>
                                    <?php if (strlen($review['comment']) > 100): ?>...<?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getReviewStatusColor($review['is_approved']); ?>">
                                    <?php echo getReviewStatusText($review['is_approved']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="reviews.php?action=view&id=<?php echo $review['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="<?php echo __('view'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($review['is_approved'] === 0): ?>
                                    <button class="btn btn-sm btn-outline-success" 
                                            onclick="approveReview(<?php echo $review['id']; ?>)" title="<?php echo __('approve'); ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="rejectReview(<?php echo $review['id']; ?>)" title="<?php echo __('reject'); ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteReview(<?php echo $review['id']; ?>)" title="<?php echo __('delete'); ?>">
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

    <?php elseif ($action === 'view'): ?>
    <!-- Review Details -->
    <div class="row">
        <!-- Review Info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('review_details'); ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted"><?php echo __('customer_information'); ?></h6>
                            <p><strong><?php echo __('name'); ?>:</strong> <?php echo htmlspecialchars($review['customer_name']); ?></p>
                            <p><strong><?php echo __('email'); ?>:</strong> <?php echo htmlspecialchars($review['customer_email']); ?></p>
                            <p><strong><?php echo __('order'); ?>:</strong> #<?php echo htmlspecialchars($review['order_number']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted"><?php echo __('review_information'); ?></h6>
                            <p><strong><?php echo __('rating'); ?>:</strong> 
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </p>
                            <p><strong><?php echo __('status'); ?>:</strong> 
                                <span class="badge bg-<?php echo getReviewStatusColor($review['is_approved']); ?>">
                                    <?php echo getReviewStatusText($review['is_approved']); ?>
                                </span>
                            </p>
                            <p><strong><?php echo __('date'); ?>:</strong> <?php echo date('M d, Y g:i A', strtotime($review['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted"><?php echo __('review_comment'); ?></h6>
                            <div class="review-comment p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Review Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('actions'); ?></h6>
                </div>
                <div class="card-body">
                    <?php if ($review['is_approved'] === 0): ?>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="approveReview(<?php echo $review['id']; ?>)">
                            <i class="fas fa-check me-2"></i><?php echo __('approve_review'); ?>
                        </button>
                        <button class="btn btn-danger" onclick="rejectReview(<?php echo $review['id']; ?>)">
                            <i class="fas fa-times me-2"></i><?php echo __('reject_review'); ?>
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        <?php echo __('this_review_already'); ?> <?php echo $review['is_approved'] === 1 ? __('approved') : __('rejected'); ?>.
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger" onclick="deleteReview(<?php echo $review['id']; ?>)">
                            <i class="fas fa-trash me-2"></i><?php echo __('delete_review'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- JavaScript específico para reseñas -->
<script>
$(document).ready(function() {
    // Prevenir múltiples inicializaciones con un flag
    if (!window.reviewsTableInitialized) {
        // Verificar si DataTable ya está inicializado
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#reviewsTable')) {
            $('#reviewsTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "<?php echo __('showing'); ?> _MENU_ <?php echo __('reviews'); ?> <?php echo __('per_page'); ?>",
                "zeroRecords": "<?php echo __('no_records_found'); ?>",
                "info": "<?php echo __('showing'); ?> _START_ <?php echo __('to'); ?> _END_ <?php echo __('of'); ?> _TOTAL_ <?php echo __('reviews'); ?>",
                "infoEmpty": "<?php echo __('no_data_available'); ?>",
                "infoFiltered": "(<?php echo __('filtered_from'); ?> _MAX_ <?php echo __('total_entries'); ?>)",
                "search": "<?php echo __('search'); ?>:",
                "paginate": {
                    "first": "<?php echo __('first'); ?>",
                    "last": "<?php echo __('last'); ?>",
                    "next": "<?php echo __('next'); ?>",
                    "previous": "<?php echo __('previous'); ?>"
                }
            }
            });
            window.reviewsTableInitialized = true;
        }
    }
});

function refreshReviews() {
    location.reload();
}

function approveReview(reviewId) {
    if (confirm('<?php echo __('approve_review_confirm'); ?>')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'approve_review',
                review_id: reviewId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('<?php echo __('error'); ?>: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('<?php echo __('connection_error'); ?>', 'error');
            }
        });
    }
}

function rejectReview(reviewId) {
    if (confirm('<?php echo __('reject_review_confirm'); ?>')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'reject_review',
                review_id: reviewId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('<?php echo __('error'); ?>: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('<?php echo __('connection_error'); ?>', 'error');
            }
        });
    }
}

function deleteReview(reviewId) {
    if (confirm('<?php echo __('delete_review_confirm'); ?> <?php echo __('this_action_cannot_be_undone'); ?>.')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_review',
                review_id: reviewId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    location.reload();
                } else {
                    showNotification('<?php echo __('error'); ?>: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('<?php echo __('connection_error'); ?>', 'error');
            }
        });
    }
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
function getAllReviews() {
    global $pdo;
    
    $sql = "SELECT r.*, o.customer_name, o.customer_email, o.order_number
            FROM reviews r
            LEFT JOIN orders o ON r.order_id = o.id
            ORDER BY r.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getReview($id) {
    global $pdo;
    
    $sql = "SELECT r.*, o.customer_name, o.customer_email, o.order_number
            FROM reviews r
            LEFT JOIN orders o ON r.order_id = o.id
            WHERE r.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getReviewStatusCounts() {
    global $pdo;
    
    $sql = "SELECT 
                SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN is_approved = -1 THEN 1 ELSE 0 END) as rejected,
                COUNT(*) as total
            FROM reviews";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}

function getReviewStatusColor($status) {
    switch ($status) {
        case 1: return 'success';
        case -1: return 'danger';
        default: return 'warning';
    }
}

function getReviewStatusText($status) {
    switch ($status) {
        case 1: return 'Approved';
        case -1: return 'Rejected';
        default: return 'Pending';
    }
}

function approveReview() {
    global $pdo;
    
    $review_id = (int)($_POST['review_id'] ?? 0);
    
    if ($review_id <= 0) {
        throw new Exception('Invalid review ID');
    }
    
    $sql = "UPDATE reviews SET is_approved = 1, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$review_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Review approved successfully'
        ]);
    } else {
        throw new Exception('Error approving review');
    }
}

function rejectReview() {
    global $pdo;
    
    $review_id = (int)($_POST['review_id'] ?? 0);
    
    if ($review_id <= 0) {
        throw new Exception('Invalid review ID');
    }
    
    $sql = "UPDATE reviews SET is_approved = -1, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$review_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Review rejected successfully'
        ]);
    } else {
        throw new Exception('Error rejecting review');
    }
}

function deleteReview() {
    global $pdo;
    
    $review_id = (int)($_POST['review_id'] ?? 0);
    
    if ($review_id <= 0) {
        throw new Exception('Invalid review ID');
    }
    
    $sql = "DELETE FROM reviews WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$review_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    } else {
        throw new Exception('Error deleting review');
    }
}

// Incluir footer del admin
include 'includes/admin-footer.php';
?>
