<?php
/**
 * Horchata Mexican Food - Cart AJAX Handler
 * Maneja las operaciones del carrito de compras
 */

// Incluir configuración
require_once '../includes/db_connect.php';

// Configurar headers para AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Iniciar sesión
session_start();

try {
    // Obtener acción
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'prepare_checkout':
            prepareCheckout();
            break;
            
        case 'get_cart':
            getCart();
            break;
            
        case 'update_cart':
            updateCart();
            break;
            
        case 'clear_cart':
            clearCart();
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

/**
 * Preparar checkout - transferir datos del carrito a la sesión
 */
function prepareCheckout() {
    $cart_data = $_POST['cart_data'] ?? '';
    
    if (empty($cart_data)) {
        throw new Exception('No hay datos del carrito');
    }
    
    // Decodificar datos del carrito
    $cart_items = json_decode($cart_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar datos del carrito');
    }
    
    if (empty($cart_items)) {
        throw new Exception('El carrito está vacío');
    }
    
    // Guardar en sesión
    $_SESSION['cart'] = $cart_items;
    $_SESSION['cart_timestamp'] = time();
    
    // Calcular totales
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $_SESSION['cart_subtotal'] = $subtotal;
    $_SESSION['cart_tax'] = $subtotal * 0.0825; // 8.25% de impuestos
    $_SESSION['cart_total'] = $subtotal + $_SESSION['cart_tax'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Carrito preparado para checkout',
        'cart_count' => count($cart_items),
        'subtotal' => $subtotal,
        'total' => $_SESSION['cart_total']
    ]);
}

/**
 * Obtener carrito de la sesión
 */
function getCart() {
    $cart_items = $_SESSION['cart'] ?? [];
    
    echo json_encode([
        'success' => true,
        'cart' => $cart_items,
        'count' => count($cart_items)
    ]);
}

/**
 * Actualizar carrito
 */
function updateCart() {
    $cart_data = $_POST['cart_data'] ?? '';
    
    if (!empty($cart_data)) {
        $cart_items = json_decode($cart_data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $_SESSION['cart'] = $cart_items;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Carrito actualizado'
    ]);
}

/**
 * Limpiar carrito
 */
function clearCart() {
    unset($_SESSION['cart']);
    unset($_SESSION['cart_subtotal']);
    unset($_SESSION['cart_tax']);
    unset($_SESSION['cart_total']);
    unset($_SESSION['cart_timestamp']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Carrito limpiado'
    ]);
}
?>
