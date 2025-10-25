<?php
/**
 * Horchata Mexican Food - Categories AJAX Handler
 * Maneja las operaciones de categorías
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
        case 'get_categories':
            getCategories();
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Error en categories.ajax.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage()
    ]);
}

/**
 * Obtener categorías con sus imágenes
 */
function getCategories() {
    global $pdo;
    
    try {
        // Definir las categorías con sus imágenes
        $categories = [
            [
                'id' => 'burritos',
                'name' => 'Burritos',
                'name_es' => 'Burritos',
                'description' => 'Delicious burritos filled with authentic Mexican ingredients',
                'description_es' => 'Deliciosos burritos rellenos con ingredientes auténticos mexicanos',
                'image' => 'assets/images/categories/burritos.jpg',
                'icon' => 'fas fa-bread-slice',
                'color' => '#d4af37'
            ],
            [
                'id' => 'tacos',
                'name' => 'Tacos',
                'name_es' => 'Tacos',
                'description' => 'Traditional Mexican tacos with fresh ingredients',
                'description_es' => 'Tacos mexicanos tradicionales con ingredientes frescos',
                'image' => 'assets/images/categories/tacos.jpg',
                'icon' => 'fas fa-utensils',
                'color' => '#e74c3c'
            ],
            [
                'id' => 'nachos',
                'name' => 'Nachos',
                'name_es' => 'Nachos',
                'description' => 'Crispy nachos topped with cheese and jalapeños',
                'description_es' => 'Nachos crujientes cubiertos con queso y jalapeños',
                'image' => 'assets/images/categories/nachos.jpg',
                'icon' => 'fas fa-cheese',
                'color' => '#f39c12'
            ],
            [
                'id' => 'seafood',
                'name' => 'Seafood',
                'name_es' => 'Mariscos',
                'description' => 'Fresh seafood dishes with Mexican flavors',
                'description_es' => 'Platos de mariscos frescos con sabores mexicanos',
                'image' => 'assets/images/categories/seafood.jpg',
                'icon' => 'fas fa-fish',
                'color' => '#3498db'
            ],
            [
                'id' => 'hamburger',
                'name' => 'Hamburgers',
                'name_es' => 'Hamburguesas',
                'description' => 'Juicy burgers with Mexican-inspired toppings',
                'description_es' => 'Hamburguesas jugosas con ingredientes inspirados en México',
                'image' => 'assets/images/categories/hamburger.jpg',
                'icon' => 'fas fa-hamburger',
                'color' => '#8e44ad'
            ],
            [
                'id' => 'combination_plates',
                'name' => 'Combination Plates',
                'name_es' => 'Platos Combinados',
                'description' => 'Complete meals with multiple Mexican dishes',
                'description_es' => 'Comidas completas con múltiples platillos mexicanos',
                'image' => 'assets/images/categories/conbinaionplates.jpg',
                'icon' => 'fas fa-plate-wheat',
                'color' => '#27ae60'
            ],
            [
                'id' => 'daily_special',
                'name' => 'Daily Specials',
                'name_es' => 'Especiales del Día',
                'description' => 'Chef\'s special dishes that change daily',
                'description_es' => 'Platillos especiales del chef que cambian diariamente',
                'image' => 'assets/images/categories/dailyspecial.jpg',
                'icon' => 'fas fa-star',
                'color' => '#e67e22'
            ],
            [
                'id' => 'desayunos',
                'name' => 'Breakfast',
                'name_es' => 'Desayunos',
                'description' => 'Traditional Mexican breakfast dishes',
                'description_es' => 'Platillos tradicionales de desayuno mexicano',
                'image' => 'assets/images/categories/desayunos.png',
                'icon' => 'fas fa-sun',
                'color' => '#f1c40f'
            ]
        ];
        
        // Formatear categorías para la respuesta
        $formatted_categories = [];
        foreach ($categories as $category) {
            $formatted_categories[] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'name_es' => $category['name_es'],
                'description' => $category['description'],
                'description_es' => $category['description_es'],
                'image' => $category['image'],
                'icon' => $category['icon'],
                'color' => $category['color'],
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
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al cargar las categorías',
            'error' => $e->getMessage()
        ]);
    }
}
?>