<?php
/**
 * Generador de Imágenes Placeholder
 * Horchata Mexican Food
 */

// Configuración
$image_dir = 'assets/images/';
$width = 1920;
$height = 1080;

// Crear directorio si no existe
if (!is_dir($image_dir)) {
    mkdir($image_dir, 0755, true);
}

// Función para crear imagen placeholder
function createPlaceholderImage($width, $height, $text, $filename) {
    // Crear imagen
    $image = imagecreate($width, $height);
    
    // Colores
    $bg_color = imagecolorallocate($image, 212, 175, 55); // Dorado
    $text_color = imagecolorallocate($image, 255, 255, 255); // Blanco
    $border_color = imagecolorallocate($image, 139, 69, 19); // Marrón
    
    // Rellenar fondo
    imagefill($image, 0, 0, $bg_color);
    
    // Dibujar borde
    imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);
    
    // Texto
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Guardar imagen
    imagejpeg($image, $filename, 80);
    imagedestroy($image);
}

// Crear imágenes placeholder
echo "<h1>🖼️ Generador de Imágenes Placeholder</h1>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>";

try {
    // Hero dish image
    createPlaceholderImage($width, $height, "HERO DISH IMAGE", $image_dir . 'hero-dish.jpg');
    echo "<p>✅ Creada: hero-dish.jpg</p>";
    
    // Restaurant interior
    createPlaceholderImage($width, $height, "RESTAURANT INTERIOR", $image_dir . 'restaurant-interior.jpg');
    echo "<p>✅ Creada: restaurant-interior.jpg</p>";
    
    // Placeholder para productos
    createPlaceholderImage(400, 300, "PRODUCT IMAGE", $image_dir . 'placeholder.jpg');
    echo "<p>✅ Creada: placeholder.jpg</p>";
    
    // Crear favicon simple
    $favicon = imagecreate(32, 32);
    $bg = imagecolorallocate($favicon, 212, 175, 55);
    $text = imagecolorallocate($favicon, 255, 255, 255);
    imagefill($favicon, 0, 0, $bg);
    imagestring($favicon, 3, 8, 8, "🍽️", $text);
    imagejpeg($favicon, $image_dir . 'favicon.ico', 80);
    imagedestroy($favicon);
    echo "<p>✅ Creada: favicon.ico</p>";
    
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745; margin-top: 20px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>🎉 ¡Imágenes Placeholder Creadas!</h3>";
    echo "<p>Se han creado todas las imágenes placeholder necesarias.</p>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Reemplazar con imágenes reales de alta calidad</li>";
    echo "<li>✅ Optimizar para web (comprimir, redimensionar)</li>";
    echo "<li>✅ Probar que no hay errores 404</li>";
    echo "<li>✅ Verificar que las imágenes se cargan correctamente</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error al Crear Imágenes</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "</div>";
?>

<style>
body {
    background-color: #f8f9fa;
    margin: 0;
    padding: 20px;
}

h1 {
    color: #d4af37;
    text-align: center;
    margin-bottom: 30px;
}

p {
    margin: 10px 0;
    padding: 5px 0;
}
</style>
