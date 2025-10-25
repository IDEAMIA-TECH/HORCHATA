<?php
/**
 * Debug Featured Products - Verificar productos destacados
 */

// Incluir configuración
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug Productos Destacados</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Debug Productos Destacados</h1>
        <hr>
";

try {
    $pdo = getDbConnection();
    
    // Verificar productos destacados
    echo "<h2>1. Productos Destacados en la Base de Datos</h2>";
    $stmt = $pdo->query("
        SELECT p.id, p.name_en, p.name_es, p.price, p.is_featured, p.is_available, c.name_en as category_name
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_featured = 1 AND p.is_available = 1
        ORDER BY p.sort_order ASC, p.name_en ASC
        LIMIT 10
    ");
    $featured_products = $stmt->fetchAll();
    
    if ($featured_products) {
        echo "<div class='alert alert-success'>✅ Se encontraron " . count($featured_products) . " productos destacados</div>";
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>ID</th><th>Nombre EN</th><th>Nombre ES</th><th>Precio</th><th>Categoría</th><th>Destacado</th><th>Disponible</th></tr></thead>";
        echo "<tbody>";
        foreach ($featured_products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . $product['name_en'] . "</td>";
            echo "<td>" . $product['name_es'] . "</td>";
            echo "<td>$" . number_format($product['price'], 2) . "</td>";
            echo "<td>" . $product['category_name'] . "</td>";
            echo "<td>" . ($product['is_featured'] ? '✅' : '❌') . "</td>";
            echo "<td>" . ($product['is_available'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning'>⚠️ No se encontraron productos destacados</div>";
    }
    
    // Verificar todos los productos
    echo "<h2>2. Todos los Productos (Primeros 10)</h2>";
    $stmt = $pdo->query("
        SELECT p.id, p.name_en, p.name_es, p.price, p.is_featured, p.is_available, c.name_en as category_name
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id ASC
        LIMIT 10
    ");
    $all_products = $stmt->fetchAll();
    
    if ($all_products) {
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>ID</th><th>Nombre EN</th><th>Nombre ES</th><th>Precio</th><th>Categoría</th><th>Destacado</th><th>Disponible</th></tr></thead>";
        echo "<tbody>";
        foreach ($all_products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . $product['name_en'] . "</td>";
            echo "<td>" . $product['name_es'] . "</td>";
            echo "<td>$" . number_format($product['price'], 2) . "</td>";
            echo "<td>" . $product['category_name'] . "</td>";
            echo "<td>" . ($product['is_featured'] ? '✅' : '❌') . "</td>";
            echo "<td>" . ($product['is_available'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    
    // Probar endpoint AJAX
    echo "<h2>3. Probar Endpoint AJAX</h2>";
    echo "<div class='card'>";
    echo "<div class='card-body'>";
    echo "<h5>URL del Endpoint:</h5>";
    echo "<code>ajax/products.ajax.php?featured=1&limit=6</code>";
    echo "<br><br>";
    echo "<button class='btn btn-primary' onclick='testAjaxEndpoint()'>Probar Endpoint</button>";
    echo "<div id='ajaxResult' class='mt-3'></div>";
    echo "</div>";
    echo "</div>";
    
    // Crear algunos productos destacados si no existen
    if (empty($featured_products)) {
        echo "<h2>4. Crear Productos Destacados</h2>";
        echo "<div class='alert alert-info'>No hay productos destacados. ¿Quieres marcar algunos como destacados?</div>";
        echo "<button class='btn btn-success' onclick='createFeaturedProducts()'>Marcar Productos como Destacados</button>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "
    </div>
    
    <script src='https://code.jquery.com/jquery-3.7.0.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
    
    <script>
    function testAjaxEndpoint() {
        $.ajax({
            url: 'ajax/products.ajax.php',
            method: 'GET',
            data: { featured: 1, limit: 6 },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta AJAX:', response);
                $('#ajaxResult').html('<div class=\"alert alert-success\">✅ Endpoint funcionando correctamente</div><pre>' + JSON.stringify(response, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                $('#ajaxResult').html('<div class=\"alert alert-danger\">❌ Error en endpoint: ' + error + '</div>');
            }
        });
    }
    
    function createFeaturedProducts() {
        $.ajax({
            url: 'ajax/admin.ajax.php',
            method: 'POST',
            data: { 
                action: 'update_featured_products',
                product_ids: [1, 2, 3, 4, 5, 6]
            },
            success: function(response) {
                alert('Productos marcados como destacados');
                location.reload();
            },
            error: function() {
                alert('Error al marcar productos como destacados');
            }
        });
    }
    </script>
</body>
</html>";
?>
