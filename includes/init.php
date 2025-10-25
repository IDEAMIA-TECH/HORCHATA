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
    // No redirigir automáticamente para evitar bucles de refresh
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
        'accessibility' => 'Accesibilidad',
        // Hero Section
        'authentic_cooking' => 'Auténtica Cocina Mexicana',
        'flavors_conquer' => 'Sabores que Conquistan el Corazón',
        'discover_magic' => 'Descubre la magia de la cocina mexicana tradicional con ingredientes frescos, recetas auténticas y el sabor que solo Horchata Mexican Food puede ofrecerte.',
        'years' => 'Años',
        'clients' => 'Clientes',
        'dishes' => 'Platillos',
        'explore_menu' => 'Explorar Menú',
        'specialties' => 'Especialidades',
        'spicy' => 'Picante',
        'fresh' => 'Fresco',
        'made_with_love' => 'Hecho con Amor',
        // About Section
        'our_story' => 'Nuestra Historia',
        'since_2015' => 'Desde 2015, Horchata Mexican Food ha sido el hogar de la auténtica cocina mexicana en nuestra comunidad.',
        'fresh_ingredients' => 'Utilizamos ingredientes frescos y recetas tradicionales que han sido transmitidas de generación en generación. Cada platillo es preparado con amor y dedicación para brindarte una experiencia culinaria única.',
        'years_experience' => 'Años de experiencia',
        'satisfied_clients' => 'Clientes satisfechos',
        // Categories Section
        'categories' => 'Categorías',
        'explore_variety' => 'Explora la variedad de sabores auténticos que tenemos para ofrecerte',
        // Reviews Section
        'testimonials' => 'Testimonios',
        'what_customers_say' => 'Lo que Dicen Nuestros Clientes',
        'verified_reviews' => 'Reseñas verificadas de clientes que han disfrutado de nuestros platillos auténticos',
        'see_all_reviews' => 'Ver Todas las Reseñas',
        // Contact Section
        'contact_us_title' => 'Contáctanos',
        'we_are_here' => 'Estamos aquí para servirte. ¡Visítanos o haz tu pedido!',
        'location' => 'Ubicación',
        'hours' => 'Horarios',
        'phone_number' => '+1 (310) 204-2659',
        'address_full' => '10814 Jefferson Blvd<br>Culver City, CA',
        'hours_full' => 'Lun-Sáb: 8:30 AM - 9:00 PM<br>Domingo: 9:00 AM - 8:00 PM',
        // Loading texts
        'loading_specialties' => 'Preparando los mejores sabores para ti',
        'loading_categories' => 'Organizando nuestros sabores para ti',
        'loading_testimonials' => 'Recopilando las mejores experiencias',
        'explore_full_menu' => 'Explorar Menú Completo'
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
        'accessibility' => 'Accessibility',
        // Hero Section
        'authentic_cooking' => 'Authentic Mexican Cooking',
        'flavors_conquer' => 'Flavors that <span class="text-primary-custom">Conquer</span> the Heart',
        'discover_magic' => 'Discover the magic of traditional Mexican cuisine with fresh ingredients, authentic recipes and the flavor that only Horchata Mexican Food can offer you.',
        'years' => 'Years',
        'clients' => 'Clients',
        'dishes' => 'Dishes',
        'explore_menu' => 'Explore Menu',
        'specialties' => 'Specialties',
        'spicy' => 'Spicy',
        'fresh' => 'Fresh',
        'made_with_love' => 'Made with Love',
        // About Section
        'our_story' => 'Our Story',
        'since_2015' => 'Since 2015, Horchata Mexican Food has been the home of authentic Mexican cuisine in our community.',
        'fresh_ingredients' => 'We use fresh ingredients and traditional recipes that have been passed down from generation to generation. Each dish is prepared with love and dedication to give you a unique culinary experience.',
        'years_experience' => 'Years of experience',
        'satisfied_clients' => 'Satisfied clients',
        // Categories Section
        'categories' => 'Categories',
        'explore_variety' => 'Explore the variety of authentic flavors we have to offer you',
        // Reviews Section
        'testimonials' => 'Testimonials',
        'what_customers_say' => 'What Our Customers Say',
        'verified_reviews' => 'Verified reviews from customers who have enjoyed our authentic dishes',
        'see_all_reviews' => 'See All Reviews',
        // Contact Section
        'contact_us_title' => 'Contact Us',
        'we_are_here' => 'We are here to serve you. Visit us or place your order!',
        'location' => 'Location',
        'hours' => 'Hours',
        'phone_number' => '+1 (310) 204-2659',
        'address_full' => '10814 Jefferson Blvd<br>Culver City, CA',
        'hours_full' => 'Mon-Sat: 8:30 AM - 9:00 PM<br>Sunday: 9:00 AM - 8:00 PM',
        // Loading texts
        'loading_specialties' => 'Preparing the best flavors for you',
        'loading_categories' => 'Organizing our flavors for you',
        'loading_testimonials' => 'Collecting the best experiences',
        'explore_full_menu' => 'Explore Full Menu'
    ];
    
    $texts = $lang === 'en' ? $texts_en : $texts_es;
    
    return $texts[$key] ?? $default ?? $key;
}
?>
