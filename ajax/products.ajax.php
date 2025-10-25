<?php
/**
 * AJAX Endpoint para Productos
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

try {
    // Obtener parámetros
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    
    // Construir consulta
    $sql = "SELECT p.*, c.name_en as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_available = 1";
    
    $params = [];
    
    // Filtros
    if ($category_id) {
        // Si es un número, usar ID numérico
        if (is_numeric($category_id)) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$category_id;
        } else {
            // Mapeo de IDs estáticos a nombres reales de categorías
            $category_mapping = [
                'burritos' => ['Breakfast Burritos', 'Special Burritos'],
                'tacos' => ['Tacos & Quesadillas'],
                'nachos' => ['Nachos & Sides'],
                'seafood' => ['Seafood'],
                'hamburger' => ['Salads & Burgers'],
                'combination_plates' => ['Combinations'],
                'daily_special' => ['Daily Specials'],
                'desayunos' => ['Breakfast Plates']
            ];
            
            if (isset($category_mapping[$category_id])) {
                $category_names = $category_mapping[$category_id];
                $placeholders = str_repeat('?,', count($category_names) - 1) . '?';
                $sql .= " AND (c.name_en IN ($placeholders) OR c.name_es IN ($placeholders))";
                $params = array_merge($params, $category_names, $category_names);
            } else {
                // Si no está en el mapeo, buscar por nombre exacto
                $sql .= " AND (c.name_en = ? OR c.name_es = ?)";
                $params[] = $category_id;
                $params[] = $category_id;
            }
        }
    }
    
    if ($featured) {
        $sql .= " AND p.is_featured = 1";
    }
    
    if ($search) {
        $sql .= " AND (p.name_en LIKE ? OR p.name_es LIKE ? OR p.description_en LIKE ? OR p.description_es LIKE ?)";
        $searchTerm = "%{$search}%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    // Ordenamiento y límites
    $sql .= " ORDER BY p.sort_order ASC, p.name_en ASC";
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    // Ejecutar consulta
    $stmt = executeQuery($sql, $params);
    $products = $stmt ? $stmt->fetchAll() : [];
    
    
    // Formatear datos
    $formatted_products = [];
    foreach ($products as $product) {
        $formatted_products[] = [
            'id' => $product['id'],
            'name' => $product['name_en'], // Usar inglés por defecto
            'name_es' => $product['name_es'],
            'description' => $product['description_en'],
            'description_es' => $product['description_es'],
            'price' => number_format($product['price'], 2),
            'image' => $product['image'] ?: 'assets/images/placeholder.jpg',
            'category_id' => $product['category_id'],
            'category_name' => $product['category_name'],
            'is_featured' => (bool)$product['is_featured']
        ];
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $formatted_products,
        'total' => count($formatted_products),
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    // Error
    error_log("Error en products.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar productos',
        'error' => $e->getMessage()
    ]);
}
?>
