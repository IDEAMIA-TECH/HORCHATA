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

// Verificar autenticación
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
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
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    error_log("Error en admin.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor',
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
    
    echo json_encode([
        'success' => true,
        'data' => [
            'pending_orders' => $pending_orders['count'],
            'pending_reviews' => $pending_reviews['count']
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
            throw new Exception('Datos requeridos faltantes');
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
                'message' => 'Producto creado exitosamente'
            ]);
        } else {
            throw new Exception('Error al crear el producto');
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
            throw new Exception('ID de producto inválido');
        }
        
        // Validaciones
        if (empty($name_en) || empty($name_es) || $category_id <= 0 || $price <= 0) {
            $errors = [];
            if (empty($name_en)) $errors[] = "name_en vacío";
            if (empty($name_es)) $errors[] = "name_es vacío";
            if ($category_id <= 0) $errors[] = "category_id inválido ($category_id)";
            if ($price <= 0) $errors[] = "price inválido ($price)";
            throw new Exception('Datos requeridos faltantes: ' . implode(', ', $errors));
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
                'message' => 'Producto actualizado exitosamente'
            ]);
        } else {
            error_log("❌ Error executing query");
            throw new Exception('Error al actualizar el producto');
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
        throw new Exception('ID de producto inválido');
    }
    
    // Verificar si el producto tiene órdenes asociadas
    $orders_count = fetchOne("
        SELECT COUNT(*) as count
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE oi.product_id = ? AND o.status != 'cancelled'
    ", [$product_id]) ?: ['count' => 0];
    
    if ($orders_count['count'] > 0) {
        throw new Exception('No se puede eliminar un producto que tiene órdenes asociadas');
    }
    
    if (executeQuery("DELETE FROM products WHERE id = ?", [$product_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar el producto');
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
        throw new Exception('ID de producto inválido');
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
        $status = $is_available ? 'activado' : 'desactivado';
        error_log("✅ Product status updated successfully - Status: $status");
        echo json_encode([
            'success' => true,
            'message' => "Producto {$status} exitosamente"
        ]);
    } else {
        error_log("❌ Error updating product status");
        throw new Exception('Error al actualizar el estado del producto');
    }
}

/**
 * Subir imagen
 */
function uploadImage() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception('No se subió ninguna imagen');
    }
    
    $image_url = processImageUpload();
    
    echo json_encode([
        'success' => true,
        'image_url' => $image_url,
        'message' => 'Imagen subida exitosamente'
    ]);
}

/**
 * Procesar subida de imagen
 */
function processImageUpload() {
    $upload_dir = '../assets/images/products/';
    
    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Tipo de archivo no permitido');
    }
    
    // Validar tamaño
    if ($file['size'] > $max_size) {
        throw new Exception('El archivo es demasiado grande (máximo 5MB)');
    }
    
    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'assets/images/products/' . $filename;
    } else {
        throw new Exception('Error al subir el archivo');
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
        'message' => 'Datos guardados automáticamente'
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
        throw new Exception('ID de elemento inválido');
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
            throw new Exception('Tipo de elemento no válido');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Estado actualizado exitosamente'
    ]);
}

/**
 * Eliminar elemento
 */
function deleteElement() {
    $element_id = (int)($_POST['element_id'] ?? 0);
    $item_type = $_POST['item_type'] ?? 'elemento';
    
    if ($element_id <= 0) {
        throw new Exception('ID de elemento inválido');
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
            throw new Exception('Tipo de elemento no válido');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Elemento eliminado exitosamente'
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
            throw new Exception('Formato de exportación no válido');
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
        throw new Exception('ID de pedido inválido');
    }
    
    $valid_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception('Estado de pedido no válido');
    }
    
    // Verificar que el pedido existe
    $order = fetchOne("SELECT id, status, payment_status FROM orders WHERE id = ?", [$order_id]);
    if (!$order) {
        throw new Exception('Pedido no encontrado');
    }
    
    // Determinar si también actualizar el estado de pago
    $payment_status = $order['payment_status'];
    if ($status === 'confirmed' && $payment_status === 'pending') {
        $payment_status = 'paid';
    } elseif ($status === 'cancelled' && $payment_status === 'paid') {
        $payment_status = 'refunded';
    }
    
    // Actualizar estado del pedido y estado de pago si es necesario
    $sql = "UPDATE orders SET status = ?, updated_at = NOW()";
    $params = [$status];
    
    if ($payment_status !== $order['payment_status']) {
        $sql .= ", payment_status = ?";
        $params[] = $payment_status;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $order_id;
    
    if (executeQuery($sql, $params)) {
        echo json_encode([
            'success' => true,
            'message' => 'Estado del pedido actualizado exitosamente',
            'new_status' => $status,
            'payment_status' => $payment_status
        ]);
    } else {
        throw new Exception('Error al actualizar el estado del pedido');
    }
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
        throw new Exception('Invalid order ID');
    }
    
    $valid_payment_methods = ['cash', 'card', 'online'];
    if (!in_array($payment_method, $valid_payment_methods)) {
        throw new Exception('Invalid payment method');
    }
    
    if ($payment_amount <= 0) {
        throw new Exception('Invalid payment amount');
    }
    
    // Verificar que el pedido existe
    $order = fetchOne("SELECT id, payment_status, total FROM orders WHERE id = ?", [$order_id]);
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    if ($order['payment_status'] === 'paid') {
        throw new Exception('Order is already marked as paid');
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
            'message' => 'Order marked as paid successfully',
            'payment_method' => $payment_method,
            'payment_amount' => $payment_amount
        ]);
    } else {
        throw new Exception('Error updating payment status');
    }
}

function getOrderDetails() {
    global $pdo;
    $order_id = (int)($_POST['order_id'] ?? 0);
    
    if ($order_id <= 0) {
        throw new Exception('Invalid order ID');
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
            throw new Exception('Order not found');
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
        throw new Exception('Please provide an order number or ID');
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
            throw new Exception('Order not found. Please check the order number or ID.');
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
            throw new Exception('All required fields must be filled');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
        
        // Verificar si el usuario o email ya existe
        $existing = fetchOne("SELECT COUNT(*) as count FROM users WHERE username = ? OR email = ?", [$username, $email]);
        if ($existing['count'] > 0) {
            throw new Exception('Username or email already exists');
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (first_name, last_name, username, email, password, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        if (executeQuery($sql, [$first_name, $last_name, $username, $email, $hashed_password, $role, $is_active])) {
            error_log("✅ User created successfully");
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } else {
            throw new Exception('Error creating user');
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
            throw new Exception('Invalid user ID');
        }
        
        // Validaciones
        if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
            throw new Exception('All required fields must be filled');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
        
        // Verificar si el usuario o email ya existe (excluyendo el usuario actual)
        $existing = fetchOne("SELECT COUNT(*) as count FROM users WHERE (username = ? OR email = ?) AND id != ?", [$username, $email, $user_id]);
        if ($existing['count'] > 0) {
            throw new Exception('Username or email already exists');
        }
        
        // Si se proporciona una nueva contraseña, actualizarla
        $sql = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ?, is_active = ?, updated_at = NOW()";
        $params = [$first_name, $last_name, $username, $email, $role, $is_active];
        
        if (!empty($password)) {
            if (strlen($password) < 6) {
                throw new Exception('Password must be at least 6 characters');
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
                'message' => 'User updated successfully'
            ]);
        } else {
            throw new Exception('Error updating user');
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
            throw new Exception('Invalid user ID');
        }
        
        // Prevenir eliminar la propia cuenta
        if (isset($_SESSION['admin_id']) && $user_id == $_SESSION['admin_id']) {
            throw new Exception('Cannot delete your own account');
        }
        
        if (executeQuery("DELETE FROM users WHERE id = ?", [$user_id])) {
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            throw new Exception('Error deleting user');
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
            throw new Exception('Invalid user ID');
        }
        
        // Prevenir desactivar la propia cuenta
        if (isset($_SESSION['admin_id']) && $user_id == $_SESSION['admin_id'] && $status == 0) {
            throw new Exception('Cannot deactivate your own account');
        }
        
        if (executeQuery("UPDATE users SET is_active = ?, updated_at = NOW() WHERE id = ?", [$status, $user_id])) {
            echo json_encode([
                'success' => true,
                'message' => 'User status updated successfully'
            ]);
        } else {
            throw new Exception('Error updating user status');
        }
    } catch (Exception $e) {
        error_log("❌ Error en toggleUserStatus: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>
