<?php
/**
 * Horchata Mexican Food - Reviews AJAX Handler
 * Maneja las operaciones de reseñas
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

try {
    // Obtener acción
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'submit_review':
            submitReview();
            break;
            
        case 'get_public_reviews':
            getPublicReviews();
            break;
            
        case 'get_reviews':
            getReviews();
            break;
            
        case 'get_review_stats':
            getReviewStats();
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
 * Enviar reseña
 */
function submitReview() {
    // Obtener datos del formulario
    $token = $_POST['token'] ?? '';
    $rating = (int)($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review_text'] ?? '');
    
    if (empty($token)) {
        throw new Exception('Token requerido');
    }
    
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Calificación inválida');
    }
    
    if (empty($review_text)) {
        throw new Exception('Texto de reseña requerido');
    }
    
    // Verificar que la orden existe y el token es válido
    $order = fetchOne("
        SELECT id, review_token, status 
        FROM orders 
        WHERE review_token = ?
    ", [$token]);
    
    if (!$order) {
        throw new Exception('Token inválido o expirado');
    }
    
    // Verificar si ya existe una reseña para esta orden
    $existing_review = fetchOne("
        SELECT id FROM reviews 
        WHERE order_id = ?
    ", [$order['id']]);
    
    if ($existing_review) {
        throw new Exception('Ya existe una reseña para esta orden');
    }
    
    // Insertar reseña
    $sql = "INSERT INTO reviews (
        order_id, 
        rating, 
        food_quality, 
        preparation_time, 
        presentation, 
        service, 
        comments, 
        recommend, 
        is_approved, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $params = [
        $order['id'],
        $rating,
        0, // food_quality
        0, // preparation_time
        0, // presentation
        0, // service
        $review_text,
        1 // recommend
    ];
    
    $review_id = insertAndGetId($sql, $params);
    
    if (!$review_id) {
        throw new Exception('Error al guardar la reseña');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Reseña enviada exitosamente',
        'review_id' => $review_id
    ]);
}

/**
 * Obtener reseñas
 */
function getReviews() {
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
    $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    // Ejecutar consulta
    $stmt = executeQuery($sql, $params);
    $reviews = $stmt ? $stmt->fetchAll() : [];
    
    // Formatear datos
    $formatted_reviews = [];
    foreach ($reviews as $review) {
        $formatted_reviews[] = [
            'id' => $review['id'],
            'order_number' => $review['order_number'],
            'rating' => (int)$review['rating'],
            'food_quality' => (int)$review['food_quality'],
            'preparation_time' => (int)$review['preparation_time'],
            'presentation' => (int)$review['presentation'],
            'service' => (int)$review['service'],
            'comments' => $review['comments'],
            'recommend' => (int)$review['recommend'],
            'created_at' => $review['created_at'],
            'is_approved' => (bool)$review['is_approved']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reviews' => $formatted_reviews,
        'count' => count($formatted_reviews)
    ]);
}

/**
 * Obtener estadísticas de reseñas
 */
function getReviewStats() {
    // Estadísticas generales
    $total_reviews = fetchOne("SELECT COUNT(*) FROM reviews WHERE is_approved = 1")['COUNT(*)'];
    $avg_rating = fetchOne("SELECT AVG(rating) FROM reviews WHERE is_approved = 1")['AVG(rating)'];
    
    // Distribución de calificaciones
    $rating_distribution = [];
    for ($i = 1; $i <= 5; $i++) {
        $count = fetchOne("SELECT COUNT(*) FROM reviews WHERE rating = ? AND is_approved = 1", [$i])['COUNT(*)'];
        $rating_distribution[$i] = (int)$count;
    }
    
    // Estadísticas por aspecto
    $aspect_stats = [];
    $aspects = ['food_quality', 'preparation_time', 'presentation', 'service'];
    
    foreach ($aspects as $aspect) {
        $avg = fetchOne("SELECT AVG($aspect) FROM reviews WHERE $aspect > 0 AND is_approved = 1")["AVG($aspect)"];
        $aspect_stats[$aspect] = $avg ? round($avg, 2) : 0;
    }
    
    // Recomendaciones
    $recommendations = fetchOne("
        SELECT 
            SUM(CASE WHEN recommend = 1 THEN 1 ELSE 0 END) as positive,
            SUM(CASE WHEN recommend = 0 THEN 1 ELSE 0 END) as neutral,
            SUM(CASE WHEN recommend = -1 THEN 1 ELSE 0 END) as negative
        FROM reviews 
        WHERE is_approved = 1
    ");
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_reviews' => (int)$total_reviews,
            'avg_rating' => $avg_rating ? round($avg_rating, 2) : 0,
            'rating_distribution' => $rating_distribution,
            'aspect_stats' => $aspect_stats,
            'recommendations' => [
                'positive' => (int)$recommendations['positive'],
                'neutral' => (int)$recommendations['neutral'],
                'negative' => (int)$recommendations['negative']
            ]
        ]
    ]);
}

/**
 * Obtener reseñas públicas para mostrar en el sitio
 */
function getPublicReviews() {
    global $pdo;
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    try {
        // Obtener reseñas aprobadas
        $sql = "SELECT r.*, o.customer_name, o.customer_email, o.order_number
                FROM reviews r
                LEFT JOIN orders o ON r.order_id = o.id
                WHERE r.is_approved = 1
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
        $reviews = $stmt->fetchAll();
        
        // Obtener estadísticas
        $stats_sql = "SELECT 
                        COUNT(*) as total_reviews,
                        AVG(rating) as avg_rating,
                        COUNT(CASE WHEN recommend = 1 THEN 1 END) as recommendations
                      FROM reviews 
                      WHERE is_approved = 1";
        
        $stats_stmt = $pdo->query($stats_sql);
        $stats = $stats_stmt->fetch();
        
        // Formatear reseñas
        $formatted_reviews = [];
        foreach ($reviews as $review) {
            $formatted_reviews[] = [
                'id' => $review['id'],
                'rating' => (int)$review['rating'],
                'food_quality' => (int)$review['food_quality'],
                'preparation_time' => (int)$review['preparation_time'],
                'presentation' => (int)$review['presentation'],
                'service' => (int)$review['service'],
                'comments' => $review['comments'],
                'recommend' => (bool)$review['recommend'],
                'customer_name' => $review['customer_name'] ?: 'Cliente Anónimo',
                'order_number' => $review['order_number'],
                'created_at' => $review['created_at']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $formatted_reviews,
            'stats' => [
                'total_reviews' => (int)$stats['total_reviews'],
                'avg_rating' => $stats['avg_rating'] ? round($stats['avg_rating'], 2) : 0,
                'recommendations' => (int)$stats['recommendations']
            ],
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => count($formatted_reviews) === $limit
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Error en getPublicReviews: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al cargar las reseñas públicas',
            'error' => $e->getMessage()
        ]);
    }
}
?>