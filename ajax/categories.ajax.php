<?php
/**
 * Horchata Mexican Food - Categories AJAX Handler
 * Maneja las operaciones de categorías
 */

// Incluir configuración de base de datos
require_once '../includes/db_connect.php';
require_once '../includes/init.php';

// Configurar headers para AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Obtener acción
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_categories':
            getCategories();
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => __('invalid_action')
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Error en categories.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => __('internal_server_error'),
        'error' => $e->getMessage()
    ]);
}

/**
 * Obtener categorías con sus imágenes desde la base de datos
 */
function getCategories() {
    global $pdo;
    
    try {
        // Verificar que la conexión a la base de datos esté disponible
        if (!isset($pdo) || $pdo === null) {
            throw new Exception('Database connection not available');
        }
        
        // Obtener límite opcional
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
        
        // Consultar categorías desde la base de datos
        // Usar COALESCE para manejar campos opcionales que pueden no existir
        $sql = "SELECT 
                    id,
                    name_en as name,
                    COALESCE(name_es, name_en) as name_es,
                    COALESCE(description_en, '') as description,
                    COALESCE(description_es, '') as description_es,
                    COALESCE(image, '') as image,
                    COALESCE(icon, 'fas fa-utensils') as icon,
                    COALESCE(color, '#d4af37') as color,
                    is_active,
                    COALESCE(display_order, 0) as display_order
                FROM categories 
                WHERE is_active = 1 
                ORDER BY display_order ASC, name_en ASC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare SQL statement: ' . implode(', ', $pdo->errorInfo()));
        }
        
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception('Failed to execute SQL statement: ' . implode(', ', $stmt->errorInfo()));
        }
        
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si no hay categorías, retornar array vacío en lugar de error
        if (empty($categories)) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'total' => 0,
                'message' => 'No categories found'
            ]);
            return;
        }
        
        // Formatear categorías para la respuesta
        $formatted_categories = [];
        foreach ($categories as $category) {
            // Asegurar que la imagen tenga la ruta correcta
            $image = $category['image'] ?? '';
            if (!empty($image)) {
                // Si la imagen comienza con '../', removerlo
                $image = str_replace('../', '', $image);
                // Si no comienza con 'http' o 'assets/', ajustar la ruta
                if (strpos($image, 'http') === 0) {
                    // URL externa, mantenerla tal cual
                } elseif (strpos($image, 'assets/') !== 0) {
                    // Si no comienza con 'assets/', usar la ruta relativa o generar una
                    if (strpos($image, '/') !== false) {
                        // Ya tiene una ruta, mantenerla
                    } else {
                        // Solo el nombre del archivo, agregar la ruta de categorías
                        $image = 'assets/images/categories/' . $image;
                    }
                }
            } else {
                // Imagen por defecto si no hay imagen
                $image = 'assets/images/categories/default.jpg';
            }
            
            $formatted_categories[] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'name_es' => $category['name_es'] ?? $category['name'],
                'description' => $category['description'] ?? '',
                'description_es' => $category['description_es'] ?? '',
                'image' => $image,
                'icon' => $category['icon'] ?? 'fas fa-utensils',
                'color' => $category['color'] ?? '#d4af37',
                'url' => 'menu.php?category=' . $category['id']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $formatted_categories,
            'total' => count($formatted_categories)
        ]);
        
    } catch (Exception $e) {
        error_log("Error en getCategories: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Usar mensaje de error directo si __() no está disponible
        $errorMessage = 'Error loading categories';
        if (function_exists('__')) {
            try {
                $errorMessage = __('error_loading_categories');
            } catch (Exception $translationError) {
                // Si falla la traducción, usar el mensaje por defecto
                error_log("Translation error: " . $translationError->getMessage());
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => $errorMessage,
            'error' => $e->getMessage(),
            'data' => []
        ]);
    }
}
?>