<?php
/**
 * AJAX Endpoint para Reseñas
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
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $approved = isset($_GET['approved']) ? (int)$_GET['approved'] : 1;
    $rating = isset($_GET['rating']) ? (int)$_GET['rating'] : null;
    
    // Construir consulta
    $sql = "SELECT r.*, o.order_number
            FROM reviews r
            LEFT JOIN orders o ON r.order_id = o.id
            WHERE 1=1";
    
    $params = [];
    
    // Filtros
    if ($approved) {
        $sql .= " AND r.is_approved = 1";
    }
    
    if ($rating) {
        $sql .= " AND r.rating = ?";
        $params[] = $rating;
    }
    
    // Ordenamiento y límites
    $sql .= " ORDER BY r.created_at DESC";
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    // Ejecutar consulta
    $stmt = executeQuery($sql, $params);
    $reviews = $stmt ? $stmt->fetchAll() : [];
    
    // Formatear datos
    $formatted_reviews = [];
    foreach ($reviews as $review) {
        $formatted_reviews[] = [
            'id' => $review['id'],
            'customer_name' => $review['customer_name'],
            'rating' => (int)$review['rating'],
            'comment' => $review['comment'],
            'image' => $review['image'],
            'is_verified' => (bool)$review['is_verified'],
            'order_number' => $review['order_number'],
            'created_at' => $review['created_at'],
            'formatted_date' => date('M d, Y', strtotime($review['created_at']))
        ];
    }
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $formatted_reviews,
        'total' => count($formatted_reviews),
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (Exception $e) {
    // Error
    error_log("Error en reviews.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar reseñas',
        'error' => $e->getMessage()
    ]);
}
?>
