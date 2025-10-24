<?php
/**
 * Test Product Page - Horchata Mexican Food
 * Página de prueba para verificar que el sistema funciona
 */

// Incluir configuración
require_once 'includes/db_connect.php';

// Crear un producto de prueba si no existe
$test_product = fetchOne("SELECT * FROM products WHERE name_en LIKE '%Tacos%' LIMIT 1");

if (!$test_product) {
    // Insertar producto de prueba
    $sql = "INSERT INTO products (category_id, name_en, name_es, description_en, description_es, price, image, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        1, // category_id
        'Tacos al Pastor',
        'Tacos al Pastor',
        'Delicious tacos with marinated pork, pineapple, and traditional Mexican spices.',
        'Deliciosos tacos con carne de cerdo marinada, piña y especias tradicionales mexicanas.',
        12.99,
        'assets/images/placeholder.jpg',
        1, // is_available
        1  // is_featured
    ];
    
    if (executeQuery($sql, $params)) {
        $test_product = fetchOne("SELECT * FROM products WHERE name_en = 'Tacos al Pastor'");
    }
}

if ($test_product) {
    // Redirigir a la página de producto
    header("Location: product.php?id=" . $test_product['id']);
    exit;
} else {
    echo "Error: No se pudo crear o encontrar un producto de prueba.";
}
?>
