<?php
/**
 * AJAX Endpoint para Categorías
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
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $with_products = isset($_GET['with_products']) ? (int)$_GET['with_products'] : 0;
    
    // Construir consulta
    $sql = "SELECT c.*, 
                   COUNT(p.id) as product_count
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id AND p.is_available = 1
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.sort_order ASC, c.name_en ASC
            LIMIT ?";
    
    $params = [$limit];
    
    // Ejecutar consulta
    $stmt = executeQuery($sql, $params);
    $categories = $stmt ? $stmt->fetchAll() : [];
    
    // Formatear datos
    $formatted_categories = [];
    foreach ($categories as $category) {
        $formatted_categories[] = [
            'id' => $category['id'],
            'name' => $category['name_en'], // Usar inglés por defecto
            'name_es' => $category['name_es'],
            'description' => $category['description_en'],
            'description_es' => $category['description_es'],
            'image' => $category['image'] ?: 'assets/images/category-placeholder.jpg',
            'product_count' => (int)$category['product_count'],
            'sort_order' => $category['sort_order']
        ];
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $formatted_categories,
        'total' => count($formatted_categories)
    ]);
    
} catch (Exception $e) {
    // Error
    error_log("Error en categories.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar categorías',
        'error' => $e->getMessage()
    ]);
}
?>
