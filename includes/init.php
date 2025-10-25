<?php
/**
 * Inicialización del Sistema
 * Horchata Mexican Food - Sistema de Pedidos
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de idiomas
define('DEFAULT_LANGUAGE', 'es');
define('SUPPORTED_LANGUAGES', ['en', 'es']);

// Procesar cambio de idioma
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
    $_SESSION['language'] = $_GET['lang'];
    
    // Redirigir para limpiar la URL
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    $params = $_GET;
    unset($params['lang']);
    
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    header('Location: ' . $url);
    exit;
}

// Establecer idioma por defecto si no está definido
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = DEFAULT_LANGUAGE;
}

// Función para obtener el idioma actual
function getCurrentLanguage() {
    return $_SESSION['language'] ?? DEFAULT_LANGUAGE;
}

// Función para obtener texto traducido
function __($key, $default = '') {
    $lang = getCurrentLanguage();
    
    // Textos en español
    $texts_es = [
        'home' => 'Inicio',
        'menu' => 'Menú',
        'reviews' => 'Reseñas',
        'contact' => 'Contacto',
        'cart' => 'Carrito',
        'language' => 'Idioma',
        'spanish' => 'Español',
        'english' => 'English',
        'welcome' => 'Bienvenido a Horchata Mexican Food',
        'authentic_mexican' => 'Auténtica Comida Mexicana',
        'our_specialties' => 'Nuestras Especialidades',
        'our_categories' => 'Nuestras Categorías',
        'customer_reviews' => 'Reseñas de Clientes',
        'contact_info' => 'Información de Contacto',
        'business_hours' => 'Horarios de Atención',
        'address' => 'Dirección',
        'phone' => 'Teléfono',
        'email' => 'Correo',
        'monday_saturday' => 'Lunes - Sábado',
        'sunday' => 'Domingo',
        'add_to_cart' => 'Agregar al Carrito',
        'view_menu' => 'Ver Menú',
        'read_reviews' => 'Leer Reseñas',
        'contact_us' => 'Contáctanos',
        'order_now' => 'Ordenar Ahora',
        'learn_more' => 'Conocer Más',
        'get_directions' => 'Obtener Direcciones',
        'call_now' => 'Llamar Ahora',
        'send_message' => 'Enviar Mensaje',
        'follow_us' => 'Síguenos',
        'all_rights_reserved' => 'Todos los derechos reservados',
        'terms_conditions' => 'Términos y Condiciones',
        'privacy_policy' => 'Política de Privacidad',
        'accessibility' => 'Accesibilidad'
    ];
    
    // Textos en inglés
    $texts_en = [
        'home' => 'Home',
        'menu' => 'Menu',
        'reviews' => 'Reviews',
        'contact' => 'Contact',
        'cart' => 'Cart',
        'language' => 'Language',
        'spanish' => 'Español',
        'english' => 'English',
        'welcome' => 'Welcome to Horchata Mexican Food',
        'authentic_mexican' => 'Authentic Mexican Food',
        'our_specialties' => 'Our Specialties',
        'our_categories' => 'Our Categories',
        'customer_reviews' => 'Customer Reviews',
        'contact_info' => 'Contact Information',
        'business_hours' => 'Business Hours',
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'monday_saturday' => 'Monday - Saturday',
        'sunday' => 'Sunday',
        'add_to_cart' => 'Add to Cart',
        'view_menu' => 'View Menu',
        'read_reviews' => 'Read Reviews',
        'contact_us' => 'Contact Us',
        'order_now' => 'Order Now',
        'learn_more' => 'Learn More',
        'get_directions' => 'Get Directions',
        'call_now' => 'Call Now',
        'send_message' => 'Send Message',
        'follow_us' => 'Follow Us',
        'all_rights_reserved' => 'All rights reserved',
        'terms_conditions' => 'Terms and Conditions',
        'privacy_policy' => 'Privacy Policy',
        'accessibility' => 'Accessibility'
    ];
    
    $texts = $lang === 'en' ? $texts_en : $texts_es;
    
    return $texts[$key] ?? $default ?? $key;
}
?>
