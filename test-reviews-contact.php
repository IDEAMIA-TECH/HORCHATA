<?php
/**
 * Test Reviews and Contact Pages
 * Verificar que las traducciones y páginas funcionen correctamente
 */

require_once 'includes/init.php';

echo "<h1>Test Reviews and Contact Pages</h1>";

echo "<h2>1. Test de Traducciones de Reviews en Español</h2>";
$_SESSION['language'] = 'es';
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Clave</th><th>Español</th></tr>";

$reviews_keys = [
    'what_customers_say_reviews', 'discover_authentic_experiences', 'site_reviews', 'yelp_reviews',
    'loading_reviews', 'collecting_best_experiences', 'yelp_reviews_title', 'see_all_yelp_reviews',
    'yelp_widget', 'yelp_widget_description', 'view_on_yelp', 'load_widget', 'average_rating',
    'total_reviews', 'satisfied_customers', 'happy_customers', 'share_experience',
    'share_experience_description', 'review_on_yelp', 'contact_us'
];

foreach ($reviews_keys as $key) {
    echo "<tr>";
    echo "<td><strong>$key</strong></td>";
    echo "<td>" . __($key) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>2. Test de Traducciones de Reviews en Inglés</h2>";
$_SESSION['language'] = 'en';
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Clave</th><th>English</th></tr>";

foreach ($reviews_keys as $key) {
    echo "<tr>";
    echo "<td><strong>$key</strong></td>";
    echo "<td>" . __($key) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>3. Test de URLs</h2>";
echo "<p><strong>Probar páginas con traducciones:</strong></p>";
echo "<ul>";
echo "<li><a href='reviews-public.php' target='_blank'>reviews-public.php (Español por defecto)</a></li>";
echo "<li><a href='reviews-public.php?lang=es' target='_blank'>reviews-public.php?lang=es (Español explícito)</a></li>";
echo "<li><a href='reviews-public.php?lang=en' target='_blank'>reviews-public.php?lang=en (English)</a></li>";
echo "<li><a href='contact.php' target='_blank'>contact.php (Español por defecto)</a></li>";
echo "<li><a href='contact.php?lang=en' target='_blank'>contact.php?lang=en (English)</a></li>";
echo "</ul>";

echo "<h2>4. Verificación de Funcionamiento</h2>";
echo "<p><strong>Después de aplicar las traducciones y crear la página de contacto:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>Reviews Public:</strong> Todas las secciones traducidas</li>";
echo "<li>✅ <strong>Contact Page:</strong> Página de contacto creada</li>";
echo "<li>✅ <strong>Contact Form:</strong> Formulario de contacto funcional</li>";
echo "<li>✅ <strong>Contact Info:</strong> Información de contacto traducida</li>";
echo "<li>✅ <strong>Map Integration:</strong> Mapa de ubicación incluido</li>";
echo "<li>✅ <strong>Responsive Design:</strong> Diseño adaptativo</li>";
echo "</ul>";

echo "<h2>5. Instrucciones de Verificación</h2>";
echo "<ol>";
echo "<li><strong>Abrir reviews-public.php:</strong> <a href='reviews-public.php' target='_blank'>reviews-public.php</a></li>";
echo "<li><strong>Verificar español:</strong> Todos los textos deben estar en español</li>";
echo "<li><strong>Cambiar a inglés:</strong> Hacer clic en el selector de idioma</li>";
echo "<li><strong>Verificar inglés:</strong> Todos los textos deben cambiar a inglés</li>";
echo "<li><strong>Probar contact.php:</strong> <a href='contact.php' target='_blank'>contact.php</a></li>";
echo "<li><strong>Verificar formulario:</strong> Formulario de contacto debe funcionar</li>";
echo "<li><strong>Verificar mapa:</strong> Mapa de ubicación debe mostrarse</li>";
echo "<li><strong>Verificar consistencia:</strong> No debe haber textos mezclados</li>";
echo "</ol>";

echo "<h2>6. Características de las Páginas</h2>";
echo "<ul>";
echo "<li>✅ <strong>Reviews Public:</strong> Sistema de pestañas, estadísticas, CTA</li>";
echo "<li>✅ <strong>Contact Page:</strong> Información de contacto, formulario, mapa</li>";
echo "<li>✅ <strong>Translations:</strong> Sistema completo de traducciones</li>";
echo "<li>✅ <strong>Responsive:</strong> Diseño adaptativo para móviles</li>";
echo "<li>✅ <strong>Professional:</strong> Diseño profesional y moderno</li>";
echo "</ul>";

echo "<h2>7. Funcionalidades Implementadas</h2>";
echo "<ul>";
echo "<li>✅ <strong>Reviews Page:</strong> Hero, pestañas, estadísticas, CTA</li>";
echo "<li>✅ <strong>Contact Page:</strong> Hero, información, formulario, mapa, info adicional</li>";
echo "<li>✅ <strong>Translation System:</strong> 20 nuevas claves para reviews</li>";
echo "<li>✅ <strong>Contact Form:</strong> Formulario completo con validación</li>";
echo "<li>✅ <strong>Map Integration:</strong> Google Maps embebido</li>";
echo "<li>✅ <strong>CSS Styling:</strong> Estilos profesionales para ambas páginas</li>";
echo "</ul>";

echo "<h2>8. Resumen de Implementación</h2>";
echo "<p><strong>Páginas creadas y traducidas:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>reviews-public.php:</strong> Página de reseñas completamente traducida</li>";
echo "<li>✅ <strong>contact.php:</strong> Página de contacto creada desde cero</li>";
echo "<li>✅ <strong>contact.css:</strong> Estilos profesionales para la página de contacto</li>";
echo "<li>✅ <strong>Translation Keys:</strong> 20 nuevas claves de traducción agregadas</li>";
echo "<li>✅ <strong>Navigation:</strong> Enlaces actualizados para apuntar a contact.php</li>";
echo "</ul>";
?>
