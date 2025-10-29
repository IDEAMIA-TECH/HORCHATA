<?php
/**
 * Horchata Mexican Food - Contact Messages Management
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
$message_id = $_GET['id'] ?? 0;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'update_status':
            updateMessageStatus();
            break;
        case 'delete':
            deleteMessage();
            break;
    }
}

// Obtener datos según la acción
switch ($action) {
    case 'view':
        $message = getContactMessage($message_id);
        break;
    case 'list':
    default:
        $filter_status = $_GET['status'] ?? null;
        $filter_search = $_GET['search'] ?? null;
        $messages = getAllContactMessages($filter_status, $filter_search);
        $status_counts = getContactMessageStatusCounts();
        break;
}

// Configurar página
$page_title = $action === 'view' ? __('contact_message_details') : __('contact_messages_management');
$page_scripts = []; // JavaScript está inline en esta página

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-envelope me-2"></i><?php echo __('contact_messages_management'); ?>
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i><?php echo __('refresh'); ?>
                </button>
            </div>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
    <!-- Messages List -->
    <div class="row mb-4">
        <!-- Status Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <?php echo __('new_messages'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['new'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
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
                                <?php echo __('read'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['read'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope-open fa-2x text-gray-300"></i>
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
                                <?php echo __('replied'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['replied'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-reply fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                <?php echo __('total'); ?>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $status_counts['total'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-inbox fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo __('messages_list'); ?></h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value=""><?php echo __('all_statuses'); ?></option>
                        <option value="new"><?php echo __('new_messages'); ?></option>
                        <option value="read"><?php echo __('read'); ?></option>
                        <option value="replied"><?php echo __('replied'); ?></option>
                        <option value="archived"><?php echo __('archived'); ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="<?php echo __('search_messages'); ?>...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-primary w-100" onclick="filterMessages()">
                        <i class="fas fa-filter me-1"></i><?php echo __('filter'); ?>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered data-table" id="messagesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo __('name'); ?></th>
                            <th><?php echo __('email'); ?></th>
                            <th><?php echo __('phone'); ?></th>
                            <th><?php echo __('subject'); ?></th>
                            <th><?php echo __('status'); ?></th>
                            <th><?php echo __('date'); ?></th>
                            <th><?php echo __('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                                <?php if ($msg['newsletter']): ?>
                                <span class="badge bg-info ms-1" title="<?php echo __('subscribed_newsletter'); ?>">
                                    <i class="fas fa-bell"></i>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>">
                                    <?php echo htmlspecialchars($msg['email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($msg['phone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($msg['phone']); ?>">
                                        <?php echo htmlspecialchars($msg['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                                <br>
                                <small class="text-muted" style="max-width: 200px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?>
                                </small>
                            </td>
                            <td>
                                <?php 
                                $display_status = empty($msg['status']) ? 'new' : $msg['status'];
                                ?>
                                <span class="badge bg-<?php echo getMessageStatusColor($display_status); ?>">
                                    <?php echo ucfirst(__($display_status === 'new' ? 'new_messages' : $display_status)); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($msg['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="contact-messages.php?action=view&id=<?php echo $msg['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="<?php echo __('view'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown" title="<?php echo __('change_status'); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <ul class="dropdown-menu" style="z-index: 9999;">
                                            <li><a class="dropdown-item" href="#" onclick="updateMessageStatus(<?php echo $msg['id']; ?>, 'read')"><?php echo __('mark_as_read'); ?></a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateMessageStatus(<?php echo $msg['id']; ?>, 'replied')"><?php echo __('mark_as_replied'); ?></a></li>
                                            <li><a class="dropdown-item" href="#" onclick="updateMessageStatus(<?php echo $msg['id']; ?>, 'archived')"><?php echo __('archive'); ?></a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMessage(<?php echo $msg['id']; ?>)"><?php echo __('delete'); ?></a></li>
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
    <!-- Message Details -->
    <div class="row">
        <!-- Message Info -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo __('contact_message_details'); ?></h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted"><?php echo __('sender_information'); ?></h6>
                            <p><strong><?php echo __('name'); ?>:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
                            <p><strong><?php echo __('email'); ?>:</strong> 
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a>
                            </p>
                            <?php if ($message['phone']): ?>
                            <p><strong><?php echo __('phone'); ?>:</strong> 
                                <a href="tel:<?php echo htmlspecialchars($message['phone']); ?>">
                                    <?php echo htmlspecialchars($message['phone']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                            <?php if ($message['newsletter']): ?>
                            <p>
                                <span class="badge bg-info">
                                    <i class="fas fa-bell me-1"></i><?php echo __('subscribed_newsletter'); ?>
                                </span>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted"><?php echo __('message_information'); ?></h6>
                            <p><strong><?php echo __('subject'); ?>:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                            <p><strong><?php echo __('status'); ?>:</strong> 
                                <?php 
                                $display_status = empty($message['status']) ? 'new' : $message['status'];
                                ?>
                                <span class="badge bg-<?php echo getMessageStatusColor($display_status); ?>">
                                    <?php echo ucfirst(__($display_status === 'new' ? 'new_messages' : $display_status)); ?>
                                </span>
                            </p>
                            <p><strong><?php echo __('date'); ?>:</strong> <?php echo date('M d, Y g:i A', strtotime($message['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6 class="text-muted"><?php echo __('message'); ?>:</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($message['message']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="contact-messages.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i><?php echo __('back_to_messages'); ?>
                        </a>
                        <?php 
                        $message_status = empty($message['status']) ? 'new' : $message['status'];
                        if ($message_status !== 'read'): ?>
                        <button class="btn btn-primary" onclick="updateMessageStatus(<?php echo $message['id']; ?>, 'read')">
                            <i class="fas fa-check me-2"></i><?php echo __('mark_as_read'); ?>
                        </button>
                        <?php endif; ?>
                        <?php if ($message_status !== 'replied'): ?>
                        <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" 
                           class="btn btn-success">
                            <i class="fas fa-reply me-2"></i><?php echo __('reply'); ?>
                        </a>
                        <?php endif; ?>
                        <button class="btn btn-danger" onclick="deleteMessage(<?php echo $message['id']; ?>)">
                            <i class="fas fa-trash me-2"></i><?php echo __('delete'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Esperar a que jQuery esté disponible
(function() {
    function initContactMessages() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initContactMessages, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            // Inicializar DataTable
            if ($('#messagesTable').length) {
                $('#messagesTable').DataTable({
                    order: [[5, 'desc']], // Ordenar por fecha descendente
                    pageLength: 25,
                    language: {
                        search: '<?php echo __('search'); ?>:',
                        lengthMenu: '<?php echo __('showing'); ?> _MENU_ <?php echo __('messages'); ?>',
                        info: '<?php echo __('showing'); ?> _START_ <?php echo __('to'); ?> _END_ <?php echo __('of'); ?> _TOTAL_ <?php echo __('messages'); ?>',
                        infoEmpty: '<?php echo __('no_records_found'); ?>',
                        infoFiltered: '(<?php echo __('filtered_from'); ?> _MAX_ <?php echo __('total_entries'); ?>)',
                        paginate: {
                            first: '<?php echo __('first'); ?>',
                            last: '<?php echo __('last'); ?>',
                            next: '<?php echo __('next'); ?>',
                            previous: '<?php echo __('previous'); ?>'
                        }
                    }
                });
            }
        });
    }
    
    initContactMessages();
})();

function updateMessageStatus(messageId, status) {
    if (!confirm('<?php echo __('change_message_status_confirm'); ?>')) {
        return;
    }
    
    fetch('ajax/admin.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_contact_message_status',
            message_id: messageId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('error'); ?>: ' + (data.message || '<?php echo __('connection_error'); ?>'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?php echo __('error'); ?>: <?php echo __('connection_error'); ?>');
    });
}

function deleteMessage(messageId) {
    if (!confirm('<?php echo __('delete_message_confirm'); ?>\n<?php echo __('this_action_cannot_be_undone'); ?>')) {
        return;
    }
    
    fetch('ajax/admin.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete_contact_message',
            message_id: messageId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('error'); ?>: ' + (data.message || '<?php echo __('connection_error'); ?>'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('<?php echo __('error'); ?>: <?php echo __('connection_error'); ?>');
    });
}

function filterMessages() {
    const status = $('#statusFilter').val();
    const search = $('#searchInput').val();
    
    // Recargar con parámetros
    let url = 'contact-messages.php';
    const params = [];
    if (status) params.push('status=' + status);
    if (search) params.push('search=' + encodeURIComponent(search));
    if (params.length) url += '?' + params.join('&');
    
    window.location.href = url;
}
</script>

<?php
// Funciones PHP
function getAllContactMessages($status = null, $search = null) {
    global $pdo;
    
    $sql = "SELECT * FROM contact_messages WHERE 1=1";
    $params = [];
    
    if ($status) {
        if ($status === 'new') {
            // Incluir mensajes con status 'new' o NULL
            $sql .= " AND (status = 'new' OR status IS NULL OR status = '')";
        } else {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
    }
    
    if ($search) {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    return fetchAll($sql, $params) ?: [];
}

function getContactMessage($message_id) {
    return fetchOne("SELECT * FROM contact_messages WHERE id = ?", [$message_id]);
}

function getContactMessageStatusCounts() {
    // Contar nuevos (incluye NULL y vacío)
    $new_count = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new' OR status IS NULL OR status = ''", []);
    $counts = [
        'new' => $new_count['count'] ?? 0,
        'read' => fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'read'", [])['count'] ?? 0,
        'replied' => fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'replied'", [])['count'] ?? 0,
        'archived' => fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'archived'", [])['count'] ?? 0,
        'total' => fetchOne("SELECT COUNT(*) as count FROM contact_messages", [])['count'] ?? 0
    ];
    
    return $counts;
}

function getMessageStatusColor($status) {
    // Tratar NULL o vacío como 'new'
    if (empty($status) || $status === null) {
        $status = 'new';
    }
    
    $colors = [
        'new' => 'primary',
        'read' => 'info',
        'replied' => 'success',
        'archived' => 'secondary'
    ];
    
    return $colors[$status] ?? 'secondary';
}

function updateMessageStatus() {
    global $pdo;
    
    $message_id = $_POST['message_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!$message_id || !$status) {
        echo json_encode(['success' => false, 'message' => __('invalid_data')]);
        exit;
    }
    
    $valid_statuses = ['new', 'read', 'replied', 'archived'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => __('invalid_status')]);
        exit;
    }
    
    $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
    $stmt = executeQuery($sql, [$status, $message_id]);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => __('error_updating_status')]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => __('status_updated_successfully')]);
    exit;
}

function deleteMessage() {
    global $pdo;
    
    $message_id = $_POST['message_id'] ?? 0;
    
    if (!$message_id) {
        echo json_encode(['success' => false, 'message' => __('invalid_data')]);
        exit;
    }
    
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = executeQuery($sql, [$message_id]);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => __('error_deleting_message')]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => __('message_deleted_successfully')]);
    exit;
}
?>

