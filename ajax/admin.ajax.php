<?php
/**
 * AJAX Endpoint para Panel Administrativo
 * Horchata Mexican Food
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexión a BD
require_once '../includes/db_connect.php';
require_once '../includes/init.php';
function normalizeEmailImageUrl(?string $path): string {
    if (!$path) return '';
    $siteUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    if (str_starts_with($path, '../')) { $path = substr($path, 3); }
    if (str_starts_with($path, './')) { $path = substr($path, 2); }
    if (preg_match('~^https?://~i', $path)) { return $path; }
    return $siteUrl . '/' . ltrim($path, '/');
}

function imageToDataUri(?string $path): string {
    if (!$path) return '';
    if (preg_match('~^https?://~i', $path)) { return $path; }
    if (str_starts_with($path, '../')) { $path = substr($path, 3); }
    if (str_starts_with($path, './')) { $path = substr($path, 2); }
    $fs = realpath(__DIR__ . '/../' . $path);
    if (!$fs || !file_exists($fs)) {
        $fsAlt = realpath(__DIR__ . '/../../' . $path);
        if ($fsAlt && file_exists($fsAlt)) { $fs = $fsAlt; }
    }
    if ($fs && file_exists($fs)) {
        $mime = function_exists('mime_content_type') ? mime_content_type($fs) : 'image/jpeg';
        $data = @file_get_contents($fs);
        if ($data !== false) {
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        }
    }
    return normalizeEmailImageUrl($path);
}

function resolveLogoDataUriAdmin(): string {
    $candidates = [
        'assets/images/LOGO.JPG',
        'assets/images/LOGO.jpg',
        'assets/images/logo.jpg',
        'assets/images/logo.png',
        'assets/images/LOGO.png',
        'assets/images/LOGO.webp',
    ];
    foreach ($candidates as $rel) {
        $fs = realpath(__DIR__ . '/../' . $rel);
        if (!$fs || !file_exists($fs)) {
            $fs = realpath(__DIR__ . '/../../' . $rel);
        }
        if ($fs && file_exists($fs)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($fs) : 'image/jpeg';
            $data = @file_get_contents($fs);
            if ($data !== false) {
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }
    }
    return normalizeEmailImageUrl('assets/images/LOGO.JPG');
}

// Verificar autenticación
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => __('unauthorized')
    ]);
    exit;
}

try {
    error_log("📥 AJAX Request - Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("📥 AJAX Request - POST keys: " . implode(', ', array_keys($_POST)));
    error_log("📥 AJAX Request - FILES keys: " . implode(', ', array_keys($_FILES)));
    if (!empty($_FILES)) {
        error_log("📥 AJAX Request - FILES data: " . print_r($_FILES, true));
    }
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    error_log("📥 AJAX Action: $action");
    
    switch ($action) {
        case 'get_notifications':
            getNotifications();
            break;
        case 'create_product':
            createProduct();
            break;
        case 'update_product':
            updateProduct();
            break;
        case 'save_product':
            // Determinar si es crear o actualizar
            $product_id = (int)($_POST['product_id'] ?? 0);
            if ($product_id > 0) {
                updateProduct();
            } else {
                createProduct();
            }
            break;
        case 'delete_product':
            deleteProduct();
            break;
        case 'toggle_product_status':
            toggleProductStatus();
            break;
        case 'upload_image':
            uploadImage();
            break;
        case 'auto_save':
            autoSave();
            break;
        case 'update_status':
            updateStatus();
            break;
        case 'update_order_status':
            updateOrderStatus();
            break;
        case 'mark_order_paid':
            markOrderAsPaid();
            break;
        case 'get_order_details':
            getOrderDetails();
            break;
        case 'search_order':
            searchOrder();
            break;
        case 'delete_element':
            deleteElement();
            break;
        case 'export':
            exportData();
            break;
        case 'create_user':
            createUser();
            break;
        case 'update_user':
            updateUser();
            break;
        case 'delete_user':
            deleteUser();
            break;
        case 'toggle_user_status':
            toggleUserStatus();
            break;
        case 'get_all_extras':
            getAllExtras();
            break;
        case 'get_product_extras':
            getProductExtras();
            break;
        case 'create_extra':
            createExtra();
            break;
        case 'assign_extra_to_product':
            assignExtraToProduct();
            break;
        case 'remove_extra_from_product':
            removeExtraFromProduct();
            break;
        case 'update_contact_message_status':
            updateContactMessageStatus();
            break;
        case 'delete_contact_message':
            deleteContactMessage();
            break;
        default:
            throw new Exception(__('invalid_action'));
    }
    
} catch (Exception $e) {
    error_log("Error en admin.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => __('server_error'),
        'error' => $e->getMessage()
    ]);
}

/**
 * Obtener notificaciones pendientes
 */
function getNotifications() {
    $pending_orders = fetchOne("
        SELECT COUNT(*) as count
        FROM orders 
        WHERE status IN ('pending', 'confirmed')
    ") ?: ['count' => 0];
    
    $pending_reviews = fetchOne("
        SELECT COUNT(*) as count
        FROM reviews 
        WHERE is_approved = 0
    ") ?: ['count' => 0];
    
    $new_messages = fetchOne("
        SELECT COUNT(*) as count
        FROM contact_messages 
        WHERE status = 'new' OR status IS NULL OR status = ''
    ") ?: ['count' => 0];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'pending_orders' => $pending_orders['count'],
            'pending_reviews' => $pending_reviews['count'],
            'new_messages' => $new_messages['count']
        ]
    ]);
}

/**
 * Crear producto
 */
function createProduct() {
    try {
        global $pdo;
        
        $name_en = trim($_POST['name_en'] ?? '');
        $name_es = trim($_POST['name_es'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $description_en = trim($_POST['description_en'] ?? '');
        $description_es = trim($_POST['description_es'] ?? '');
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        // Validaciones
        if (empty($name_en) || empty($name_es) || $category_id <= 0 || $price <= 0) {
            throw new Exception(__('all_fields_required'));
        }
        
        // Procesar imagen si se subió una
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = processImageUpload();
        }
        
        $sql = "INSERT INTO products (category_id, name_en, name_es, description_en, description_es, price, image, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$category_id, $name_en, $name_es, $description_en, $description_es, $price, $image, $is_available, $is_featured];
        
        if (executeQuery($sql, $params)) {
            echo json_encode([
                'success' => true,
                'message' => __('product_created_successfully')
            ]);
        } else {
            throw new Exception(__('error_creating_product'));
        }
    } catch (Exception $e) {
        error_log("Error en createProduct: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Actualizar producto
 */
function updateProduct() {
    try {
        global $pdo;
        
        error_log("🔄 updateProduct called");
        error_log("📦 POST data: " . print_r($_POST, true));
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        $name_en = trim($_POST['name_en'] ?? '');
        $name_es = trim($_POST['name_es'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $description_en = trim($_POST['description_en'] ?? '');
        $description_es = trim($_POST['description_es'] ?? '');
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        error_log("🔄 Product ID: $product_id, Name EN: $name_en, Name ES: $name_es, Category: $category_id, Price: $price");
        
        if ($product_id <= 0) {
            throw new Exception(__('invalid_product_id'));
        }
        
        // Validaciones
        if (empty($name_en) || empty($name_es) || $category_id <= 0 || $price <= 0) {
            $errors = [];
            if (empty($name_en)) $errors[] = "name_en vacío";
            if (empty($name_es)) $errors[] = "name_es vacío";
            if ($category_id <= 0) $errors[] = "category_id inválido ($category_id)";
            if ($price <= 0) $errors[] = "price inválido ($price)";
            throw new Exception(__('all_fields_required') . ': ' . implode(', ', $errors));
        }
        
        // Procesar imagen si se subió una nueva
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = processImageUpload();
            error_log("🖼️ Image uploaded: $image");
        }
        
        $sql = "UPDATE products SET category_id = ?, name_en = ?, name_es = ?, description_en = ?, description_es = ?, price = ?, is_available = ?, is_featured = ?";
        $params = [$category_id, $name_en, $name_es, $description_en, $description_es, $price, $is_available, $is_featured];
        
        if ($image) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $product_id;
        
        error_log("📝 SQL: $sql");
        error_log("📦 Params: " . print_r($params, true));
        
        if (executeQuery($sql, $params)) {
            error_log("✅ Product updated successfully");
            echo json_encode([
                'success' => true,
                'message' => __('product_updated_successfully')
            ]);
        } else {
            error_log("❌ Error executing query");
            throw new Exception(__('error_updating_product'));
        }
    } catch (Exception $e) {
        error_log("❌ Error en updateProduct: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Eliminar producto
 */
function deleteProduct() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0) {
        throw new Exception(__('invalid_product_id'));
    }
    
    // Verificar si el producto tiene órdenes asociadas
    $orders_count = fetchOne("
        SELECT COUNT(*) as count
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.product_id = ? AND o.status != 'cancelled'
    ", [$product_id]) ?: ['count' => 0];
    
    if ($orders_count['count'] > 0) {
        throw new Exception(__('cannot_delete_product_with_orders'));
    }
    
    if (executeQuery("DELETE FROM products WHERE id = ?", [$product_id])) {
        echo json_encode([
            'success' => true,
            'message' => __('product_deleted_successfully')
        ]);
    } else {
        throw new Exception(__('error_deleting_product'));
    }
}

/**
 * Cambiar estado de producto
 */
function toggleProductStatus() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $is_available = (int)($_POST['is_available'] ?? 0);
    
    error_log("🔄 Toggle Product Status - Product ID: $product_id, Is Available: $is_available");
    
    if ($product_id <= 0) {
        throw new Exception(__('invalid_product_id'));
    }
    
    // Convertir el valor de is_available (puede venir como string 'true'/'false')
    if ($is_available === 'true' || $is_available === true) {
        $is_available = 1;
    } elseif ($is_available === 'false' || $is_available === false) {
        $is_available = 0;
    }
    
    $is_available = (int)$is_available;
    
    error_log("🔄 Final value - Is Available: $is_available");
    
    if (executeQuery("UPDATE products SET is_available = ? WHERE id = ?", [$is_available, $product_id])) {
        $status = $is_available ? __('product_activated') : __('product_deactivated');
        error_log("✅ Product status updated successfully - Status: $status");
        echo json_encode([
            'success' => true,
            'message' => $status . ' ' . __('status_updated_successfully')
        ]);
    } else {
        error_log("❌ Error updating product status");
        throw new Exception(__('error_updating_product_status'));
    }
}

/**
 * Subir imagen
 */
function uploadImage() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception(__('no_image_uploaded'));
    }
    
    $image_url = processImageUpload();
    
    echo json_encode([
        'success' => true,
        'image_url' => $image_url,
        'message' => __('image_uploaded_successfully')
    ]);
}

/**
 * Procesar subida de imagen
 */
function processImageUpload() {
    error_log("🖼️ [processImageUpload] Starting image upload...");
    
    // Verificar si $_FILES está vacío
    if (!isset($_FILES['image'])) {
        error_log("❌ [processImageUpload] No file uploaded (image not set)");
        throw new Exception(__('no_image_uploaded'));
    }
    
    $file = $_FILES['image'];
    error_log("📁 [processImageUpload] File info: " . print_r($file, true));
    
    // Verificar errores de subida
    if ($file['error'] !== 0) {
        error_log("❌ [processImageUpload] Upload error code: " . $file['error']);
        throw new Exception(str_replace('{code}', $file['error'], __('error_file_upload')));
    }
    
    $upload_dir = '../assets/images/products/';
    
    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        error_log("📁 [processImageUpload] Creating directory: $upload_dir");
        if (!mkdir($upload_dir, 0755, true)) {
            error_log("❌ [processImageUpload] Failed to create directory");
            throw new Exception('No se pudo crear el directorio de subida');
        }
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    error_log("🔍 [processImageUpload] File type: " . $file['type']);
    error_log("🔍 [processImageUpload] File size: " . $file['size'] . " bytes");
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowed_types)) {
        error_log("❌ [processImageUpload] File type not allowed: " . $file['type']);
        throw new Exception('Tipo de archivo no permitido. Solo se permiten: JPEG, PNG, GIF, WebP');
    }
    
    // Validar tamaño
    if ($file['size'] > $max_size) {
        error_log("❌ [processImageUpload] File too large: " . $file['size'] . " bytes");
        throw new Exception('El archivo es demasiado grande (máximo 5MB)');
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    error_log("💾 [processImageUpload] Saving to: $filepath");
    
    // Verificar que el directorio es escribible
    if (!is_writable($upload_dir)) {
        error_log("❌ [processImageUpload] Directory is not writable: $upload_dir");
        throw new Exception('El directorio de subida no es escribible');
    }
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        error_log("✅ [processImageUpload] File uploaded successfully to: $filepath");
        // Ruta relativa desde la raíz del sitio (sin ../assets, solo assets)
        $relative_path = '../assets/images/products/' . $filename;
        error_log("✅ [processImageUpload] Returning path: $relative_path");
        return $relative_path;
    } else {
        error_log("❌ [processImageUpload] Failed to move uploaded file");
        error_log("❌ [processImageUpload] temp_name: " . $file['tmp_name']);
        error_log("❌ [processImageUpload] destination: " . $filepath);
        throw new Exception(__('error_saving_file'));
    }
}

/**
 * Auto-guardar formulario
 */
function autoSave() {
    $form_id = $_POST['form_id'] ?? '';
    $form_data = $_POST['form_data'] ?? '';
    
    // Guardar en sesión o base de datos temporal
    $_SESSION['auto_save_' . $form_id] = $form_data;
    
    echo json_encode([
        'success' => true,
        'message' => __('data_saved_automatically')
    ]);
}

/**
 * Actualizar estado de elemento
 */
function updateStatus() {
    $element_id = (int)($_POST['element_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $item_type = $_POST['item_type'] ?? 'elemento';
    
    if ($element_id <= 0) {
        throw new Exception(__('invalid_element_id'));
    }
    
    // Implementar según el tipo de elemento
    switch ($item_type) {
        case 'product':
            executeQuery("UPDATE products SET is_available = ? WHERE id = ?", [$new_status, $element_id]);
            break;
        case 'order':
            executeQuery("UPDATE orders SET status = ? WHERE id = ?", [$new_status, $element_id]);
            break;
        case 'review':
            executeQuery("UPDATE reviews SET is_approved = ? WHERE id = ?", [$new_status, $element_id]);
            break;
        default:
            throw new Exception(__('invalid_element_type'));
    }
    
    echo json_encode([
        'success' => true,
        'message' => __('status_updated_successfully')
    ]);
}

/**
 * Eliminar elemento
 */
function deleteElement() {
    $element_id = (int)($_POST['element_id'] ?? 0);
    $item_type = $_POST['item_type'] ?? 'elemento';
    
    if ($element_id <= 0) {
        throw new Exception(__('invalid_element_id'));
    }
    
    // Implementar según el tipo de elemento
    switch ($item_type) {
        case 'product':
            executeQuery("DELETE FROM products WHERE id = ?", [$element_id]);
            break;
        case 'order':
            executeQuery("DELETE FROM orders WHERE id = ?", [$element_id]);
            break;
        case 'review':
            executeQuery("DELETE FROM reviews WHERE id = ?", [$element_id]);
            break;
        default:
            throw new Exception(__('invalid_element_type'));
    }
    
    echo json_encode([
        'success' => true,
        'message' => __('item_deleted_successfully')
    ]);
}

/**
 * Exportar datos
 */
function exportData() {
    $format = $_GET['format'] ?? 'csv';
    $table = $_GET['table'] ?? 'all';
    
    // Implementar exportación según formato y tabla
    switch ($format) {
        case 'csv':
            exportToCSV($table);
            break;
        case 'excel':
            exportToExcel($table);
            break;
        default:
            throw new Exception(__('invalid_export_format'));
    }
}

/**
 * Exportar a CSV
 */
function exportToCSV($table) {
    // Implementar exportación CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export_' . $table . '_' . date('Y-m-d') . '.csv"');
    
    // Aquí implementarías la lógica de exportación
    echo "ID,Nombre,Precio,Fecha\n";
    echo "1,Producto 1,10.00,2024-01-01\n";
}

/**
 * Exportar a Excel
 */
function exportToExcel($table) {
    // Implementar exportación Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="export_' . $table . '_' . date('Y-m-d') . '.xls"');
    
    // Aquí implementarías la lógica de exportación
    echo "ID\tNombre\tPrecio\tFecha\n";
    echo "1\tProducto 1\t10.00\t2024-01-01\n";
}

/**
 * Actualizar estado del pedido
 */
function updateOrderStatus() {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    
    // Validar datos
    if ($order_id <= 0) {
        throw new Exception(__('invalid_order_id'));
    }
    
    $valid_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception(__('invalid_status'));
    }
    
    // Verificar que el pedido existe
    $order = fetchOne("SELECT id, status, payment_status FROM orders WHERE id = ?", [$order_id]);
    if (!$order) {
        throw new Exception(__('order_not_found'));
    }
    
    // NO modificar estado de pago aquí; solo el admin lo marca como pagado con el botón
    $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    $params = [$status, $order_id];
    
    if (executeQuery($sql, $params)) {
        // Enviar correo al cliente
        try {
            error_log("[updateOrderStatus] sending email. order_id={$order_id}, status={$status}, payment_status={$order['payment_status']}");
            sendAdminOrderStatusEmail($order_id, $status, $order['payment_status']);
        } catch (Exception $e) {
            error_log('sendAdminOrderStatusEmail error: ' . $e->getMessage());
        }
        echo json_encode([
            'success' => true,
            'message' => __('order_status_updated_successfully'),
            'new_status' => $status,
            'payment_status' => $order['payment_status']
        ]);
    } else {
        throw new Exception(__('error_updating_order_status'));
    }
}

function sendAdminOrderStatusEmail(int $orderId, string $newStatus, string $paymentStatus) {
    $siteUrl = defined('SITE_URL') ? SITE_URL : '';
    $fromEmail = getSetting('email_from', 'orders@horchatamexicanfood.com');
    $fromName = getSetting('email_from_name', 'Horchata Mexican Food');
    $logoUrl = resolveLogoDataUriAdmin();
    $order = fetchOne("SELECT * FROM orders WHERE id = ?", [$orderId]);
    if (!$order) { throw new Exception(__('order_not_found')); }
    $items = fetchAll("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?", [$orderId]) ?: [];
    $itemsHtml = '';
    foreach ($items as $it) {
        $img = imageToDataUri($it['image'] ?? '');
        $itemsHtml .= '<tr>' .
            '<td style="padding:10px; border-bottom:1px solid #eee;"><img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($it['product_name']) . '" style="width:70px;height:70px;object-fit:cover;border-radius:8px;vertical-align:middle;margin-right:10px;">' .
            '<strong>' . htmlspecialchars($it['product_name']) . '</strong><br><small>Cant: ' . (int)$it['quantity'] . '</small></td>' .
            '<td style="padding:10px; border-bottom:1px solid #eee; text-align:right;">$' . number_format($it['subtotal'], 2) . '</td>' .
        '</tr>';
    }
    $subject = 'Order #' . $order['order_number'] . ' update: ' . ucfirst($newStatus);
    $html = '<div style="font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;padding:20px;">' .
        '<table style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);" cellpadding="0" cellspacing="0" width="100%">' .
            '<tr><td style="background:linear-gradient(135deg,#111,#333);padding:24px;text-align:center;">' .
                '<img src="' . htmlspecialchars($logoUrl) . '" alt="Horchata Mexican Food" style="max-width:180px;border-radius:10px;background:#fff;padding:8px;box-shadow:0 4px 20px rgba(212,175,55,0.35);border:2px solid rgba(212,175,55,0.25);">' .
                '<h2 style="color:#fff;margin:16px 0 0;font-weight:700;">Order Update</h2>' .
                '<p style="color:#ddd;margin:8px 0 0;">Status: <strong style="color:#d4af37;">' . htmlspecialchars(ucfirst($newStatus)) . '</strong> &middot; Payment: <strong>' . htmlspecialchars(ucfirst($paymentStatus)) . '</strong></p>' .
            '</td></tr>' .
            '<tr><td style="padding:24px;">' .
                '<p style="margin:0 0 12px;color:#333;">Order Number: <strong>#' . htmlspecialchars($order['order_number']) . '</strong></p>' .
                '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">' .
                    $itemsHtml .
                '</table>' .
            '</td></tr>' .
            '<tr><td style="background:#fafafa;padding:16px;text-align:center;color:#777;font-size:12px;">Horchata Mexican Food &middot; This is an automated message.</td></tr>' .
        '</table>' .
    '</div>';
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    @mail($order['customer_email'], $subject, $html, implode("\r\n", $headers));
}

/**
 * Mark order as paid
 */
function markOrderAsPaid() {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);
    $payment_notes = trim($_POST['payment_notes'] ?? '');
    
    // Validar datos
    if ($order_id <= 0) {
        throw new Exception(__('invalid_order_id'));
    }
    
    $valid_payment_methods = ['cash', 'card', 'online'];
    if (!in_array($payment_method, $valid_payment_methods)) {
        throw new Exception(__('invalid_payment_method'));
    }
    
    if ($payment_amount <= 0) {
        throw new Exception(__('invalid_payment_amount'));
    }
    
    // Verificar que el pedido existe
    $order = fetchOne("SELECT id, payment_status, total FROM orders WHERE id = ?", [$order_id]);
    if (!$order) {
        throw new Exception(__('order_not_found'));
    }
    
    if ($order['payment_status'] === 'paid') {
        throw new Exception(__('order_already_paid'));
    }
    
    // Actualizar estado de pago y método de pago
    $sql = "UPDATE orders SET payment_status = 'paid', payment_method = ?, updated_at = NOW()";
    $params = [$payment_method];
    
    // Si hay notas de pago, las guardamos en el campo notes (o podrías crear una tabla separada)
    if (!empty($payment_notes)) {
        $sql .= ", notes = CONCAT(COALESCE(notes, ''), '\nPayment Notes: ', ?)";
        $params[] = $payment_notes;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $order_id;
    
    if (executeQuery($sql, $params)) {
        echo json_encode([
            'success' => true,
            'message' => __('order_marked_paid_successfully'),
            'payment_method' => $payment_method,
            'payment_amount' => $payment_amount
        ]);
    } else {
        throw new Exception(__('error_updating_payment_status'));
    }
}

function getOrderDetails() {
    global $pdo;
    $order_id = (int)($_POST['order_id'] ?? 0);
    
    if ($order_id <= 0) {
        throw new Exception(__('invalid_order_id'));
    }
    
    try {
        // Get order with item count
        $order = fetchOne("
            SELECT o.*, COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ?
            GROUP BY o.id
        ", [$order_id]);
        
        if (!$order) {
            throw new Exception(__('order_not_found'));
        }
        
        echo json_encode([
            'success' => true,
            'order' => $order
        ]);
    } catch (Exception $e) {
        error_log("Error in getOrderDetails: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

function searchOrder() {
    global $pdo;
    $search_value = trim($_POST['search_value'] ?? '');
    
    if (empty($search_value)) {
        throw new Exception(__('please_provide_order_number'));
    }
    
    try {
        // Try to find by order_number first, then by ID
        $order = fetchOne("
            SELECT o.*, COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.order_number = ? OR o.id = ?
            GROUP BY o.id
        ", [$search_value, (int)$search_value]);
        
        if (!$order) {
            throw new Exception(__('order_not_found_check_number'));
        }
        
        echo json_encode([
            'success' => true,
            'order' => $order
        ]);
    } catch (Exception $e) {
        error_log("Error in searchOrder: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Crear usuario
 */
function createUser() {
    try {
        global $pdo;
        
        error_log("🔄 createUser called");
        
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'staff');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        error_log("📦 User data: Name: $first_name $last_name, Username: $username, Email: $email, Role: $role");
        
        // Validaciones
        if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
            throw new Exception(__('all_fields_required'));
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__('invalid_email_address'));
        }
        
        if (strlen($password) < 6) {
            throw new Exception(__('password_min_length'));
        }
        
        // Verificar si el usuario o email ya existe
        $existing = fetchOne("SELECT COUNT(*) as count FROM users WHERE username = ? OR email = ?", [$username, $email]);
        if ($existing['count'] > 0) {
            throw new Exception(__('username_email_exists'));
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (first_name, last_name, username, email, password, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        if (executeQuery($sql, [$first_name, $last_name, $username, $email, $hashed_password, $role, $is_active])) {
            error_log("✅ User created successfully");
            echo json_encode([
                'success' => true,
                'message' => __('user_created_successfully')
            ]);
        } else {
            throw new Exception(__('error_creating_user'));
        }
    } catch (Exception $e) {
        error_log("❌ Error en createUser: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Actualizar usuario
 */
function updateUser() {
    try {
        global $pdo;
        
        error_log("🔄 updateUser called");
        
        $user_id = (int)($_POST['user_id'] ?? 0);
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'staff');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        error_log("📦 User data: ID: $user_id, Name: $first_name $last_name, Username: $username, Email: $email, Role: $role");
        
        if ($user_id <= 0) {
            throw new Exception(__('invalid_user_id'));
        }
        
        // Validaciones
        if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
            throw new Exception(__('all_fields_required'));
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(__('invalid_email_address'));
        }
        
        // Verificar si el usuario o email ya existe (excluyendo el usuario actual)
        $existing = fetchOne("SELECT COUNT(*) as count FROM users WHERE (username = ? OR email = ?) AND id != ?", [$username, $email, $user_id]);
        if ($existing['count'] > 0) {
            throw new Exception(__('username_email_exists'));
        }
        
        // Si se proporciona una nueva contraseña, actualizarla
        $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ?, is_active = ?, updated_at = NOW()";
        $params = [$first_name, $last_name, $username, $email, $role, $is_active];
        
        if (!empty($password)) {
            if (strlen($password) < 6) {
                throw new Exception(__('password_min_length'));
            }
            $sql .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        
        if (executeQuery($sql, $params)) {
            error_log("✅ User updated successfully");
            echo json_encode([
                'success' => true,
                'message' => __('user_updated_successfully')
            ]);
        } else {
            throw new Exception(__('error_updating_user'));
        }
    } catch (Exception $e) {
        error_log("❌ Error en updateUser: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Eliminar usuario
 */
function deleteUser() {
    try {
        global $pdo;
        
        $user_id = (int)($_POST['user_id'] ?? 0);
        
        if ($user_id <= 0) {
            throw new Exception(__('invalid_user_id'));
        }
        
        // Prevenir eliminar la propia cuenta
        if (isset($_SESSION['admin_id']) && $user_id == $_SESSION['admin_id']) {
            throw new Exception(__('cannot_delete_own_account'));
        }
        
        if (executeQuery("DELETE FROM users WHERE id = ?", [$user_id])) {
            echo json_encode([
                'success' => true,
                'message' => __('user_deleted_successfully')
            ]);
        } else {
            throw new Exception(__('error_deleting_user'));
        }
    } catch (Exception $e) {
        error_log("❌ Error en deleteUser: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Cambiar estado de usuario
 */
function toggleUserStatus() {
    try {
        global $pdo;
        
        $user_id = (int)($_POST['user_id'] ?? 0);
        $status = $_POST['status'] === 'true' ? 1 : 0;
        
        if ($user_id <= 0) {
            throw new Exception(__('invalid_user_id'));
        }
        
        // Prevenir desactivar la propia cuenta
        if (isset($_SESSION['admin_id']) && $user_id == $_SESSION['admin_id'] && $status == 0) {
            throw new Exception(__('cannot_deactivate_own_account'));
        }
        
        if (executeQuery("UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?", [$status, $user_id])) {
            echo json_encode([
                'success' => true,
                'message' => __('user_status_updated_successfully')
            ]);
        } else {
            throw new Exception(__('error_updating_user_status'));
        }
    } catch (Exception $e) {
        error_log("❌ Error en toggleUserStatus: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Obtener todos los extras disponibles
 */
function getAllExtras() {
    try {
        global $pdo;
        
        $sql = "SELECT e.id, e.name_en, e.name_es, e.price, c.name_en as category_name 
                FROM product_extras e 
                LEFT JOIN extra_categories c ON e.category_id = c.id 
                WHERE e.is_active = 1 
                ORDER BY c.sort_order, e.name_en";
        
        $stmt = $pdo->query($sql);
        $extras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $extras
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en getAllExtras: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Obtener extras de un producto específico
 */
function getProductExtras() {
    try {
        global $pdo;
        
        $product_id = (int)($_GET['product_id'] ?? 0);
        
        if ($product_id <= 0) {
            throw new Exception(__('invalid_product_id'));
        }
        
        $sql = "SELECT e.id, e.name_en, e.name_es, e.price 
                FROM product_extras e 
                INNER JOIN product_extra_relations r ON e.id = r.extra_id 
                WHERE r.product_id = ? AND e.is_active = 1 AND r.is_active = 1 
                ORDER BY e.name_en";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        $extras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $extras
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en getProductExtras: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Crear un nuevo extra
 */
function createExtra() {
    try {
        global $pdo;
        
        $name_en = trim($_POST['name_en'] ?? '');
        $name_es = trim($_POST['name_es'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);
        
        if (empty($name_en) || empty($name_es) || $price < 0) {
            throw new Exception('Datos inválidos');
        }
        
        $sql = "INSERT INTO product_extras (name_en, name_es, price, category_id, sort_order) VALUES (?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name_en, $name_es, $price, $category_id]);
        
        echo json_encode([
            'success' => true,
            'message' => __('extra_created_successfully')
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en createExtra: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Asignar extra a producto
 */
function assignExtraToProduct() {
    try {
        global $pdo;
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        $extra_id = (int)($_POST['extra_id'] ?? 0);
        
        if ($product_id <= 0 || $extra_id <= 0) {
            throw new Exception(__('invalid_ids'));
        }
        
        $sql = "INSERT INTO product_extra_relations (product_id, extra_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_active = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $extra_id]);
        
        echo json_encode([
            'success' => true,
            'message' => __('extra_assigned_to_product')
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en assignExtraToProduct: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Remover extra de producto
 */
function removeExtraFromProduct() {
    try {
        global $pdo;
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        $extra_id = (int)($_POST['extra_id'] ?? 0);
        
        if ($product_id <= 0 || $extra_id <= 0) {
            throw new Exception(__('invalid_ids'));
        }
        
        $sql = "UPDATE product_extra_relations SET is_active = 0 WHERE product_id = ? AND extra_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id, $extra_id]);
        
        echo json_encode([
            'success' => true,
            'message' => __('extra_removed_from_product')
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en removeExtraFromProduct: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Actualizar estado de mensaje de contacto
 */
function updateContactMessageStatus() {
    try {
        global $pdo;
        
        $message_id = (int)($_POST['message_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        
        error_log("📝 updateContactMessageStatus - message_id: $message_id, status: $status");
        
        if ($message_id <= 0 || empty($status)) {
            throw new Exception(__('invalid_data'));
        }
        
        $valid_statuses = ['new', 'read', 'replied', 'archived'];
        if (!in_array($status, $valid_statuses)) {
            throw new Exception(__('invalid_status') . ': ' . $status);
        }
        
        $sql = "UPDATE contact_messages SET status = ? WHERE id = ?";
        $stmt = executeQuery($sql, [$status, $message_id]);
        
        if ($stmt === false) {
            throw new Exception(__('error_updating_message_status'));
        }
        
        error_log("✅ updateContactMessageStatus - Estado actualizado correctamente");
        
        echo json_encode([
            'success' => true,
            'message' => __('message_status_updated')
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en updateContactMessageStatus: " . $e->getMessage());
        error_log("❌ Stack trace: " . $e->getTraceAsString());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Eliminar mensaje de contacto
 */
function deleteContactMessage() {
    try {
        global $pdo;
        
        $message_id = (int)($_POST['message_id'] ?? 0);
        
        error_log("📝 deleteContactMessage - message_id: $message_id");
        
        if ($message_id <= 0) {
            throw new Exception(__('invalid_data'));
        }
        
        $sql = "DELETE FROM contact_messages WHERE id = ?";
        $stmt = executeQuery($sql, [$message_id]);
        
        if ($stmt === false) {
            throw new Exception(__('error_deleting_message'));
        }
        
        error_log("✅ deleteContactMessage - Mensaje eliminado correctamente");
        
        echo json_encode([
            'success' => true,
            'message' => __('message_deleted_successfully')
        ]);
    } catch (Exception $e) {
        error_log("❌ Error en deleteContactMessage: " . $e->getMessage());
        error_log("❌ Stack trace: " . $e->getTraceAsString());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>
