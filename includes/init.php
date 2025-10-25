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
        'explore_full_menu' => 'Explorar Menú Completo',
        // Menu Page
        'our_menu' => 'Nuestro Menú',
        'discover_authentic' => 'Descubre la auténtica cocina mexicana con nuestros platillos tradicionales, preparados con ingredientes frescos y recetas familiares.',
        'all_categories' => 'Todos',
        'search_dishes' => 'Buscar platillos...',
        'preparing_menu' => 'Preparando tu menú...',
        'loading_delicious' => 'Cargando nuestros deliciosos platillos',
        'oops_not_found' => '¡Ups! No encontramos ese platillo',
        'no_dishes_found' => 'No se encontraron platillos con esos términos. Intenta con otros ingredientes o explora nuestras categorías.',
        'clear_search' => 'Limpiar Búsqueda',
        'view_all_categories' => 'Ver Todas las Categorías',
        'spicy_dishes' => 'Platillos Picantes',
        'spicy_description' => 'Nuestros platillos más picantes para los amantes del chile',
        'fresh_ingredients_title' => 'Ingredientes Frescos',
        'fresh_ingredients_description' => 'Utilizamos solo los ingredientes más frescos y de calidad',
        'made_with_love_title' => 'Hecho con Amor',
        'made_with_love_description' => 'Cada platillo se prepara con el amor de nuestras recetas familiares',
        // Additional translations
        'popular_dishes_description' => 'Los platillos más populares de nuestro menú, preparados con amor y tradición mexicana',
        'footer_description' => 'Auténtica comida mexicana preparada con ingredientes frescos y recetas tradicionales. Disfruta de nuestros sabores únicos con servicio pickup.',
        'quick_links' => 'Enlaces Rápidos',
        'contact_info' => 'Información de Contacto',
        'business_hours' => 'Horarios de Atención',
        'monday_saturday' => 'Lunes - Sábado',
        'sunday' => 'Domingo',
        'all_rights_reserved' => 'Todos los derechos reservados',
        'terms_conditions' => 'Términos y Condiciones',
        'accessibility' => 'Accesibilidad',
        'your_cart' => 'Tu Carrito',
        'cart_empty' => 'Tu carrito está vacío',
        'total' => 'Total',
        'proceed_payment' => 'Proceder al Pago',
        // Reviews Public Page
        'what_customers_say_reviews' => 'Lo que Dicen Nuestros Clientes',
        'discover_authentic_experiences' => 'Descubre las experiencias auténticas de nuestros clientes y únete a la comunidad de amantes de la comida mexicana tradicional.',
        'site_reviews' => 'Reseñas del Sitio',
        'yelp_reviews' => 'Reseñas de Yelp',
        'loading_reviews' => 'Cargando reseñas...',
        'collecting_best_experiences' => 'Recopilando las mejores experiencias',
        'yelp_reviews_title' => 'Reseñas en Yelp',
        'see_all_yelp_reviews' => 'Ve todas nuestras reseñas y calificaciones en Yelp',
        'yelp_widget' => 'Widget de Yelp',
        'yelp_widget_description' => 'Aquí se mostrará el widget oficial de Yelp con las reseñas más recientes.',
        'view_on_yelp' => 'Ver en Yelp',
        'load_widget' => 'Cargar Widget',
        'average_rating' => 'Calificación Promedio',
        'total_reviews' => 'Reseñas Totales',
        'satisfied_customers' => 'Clientes Satisfechos',
        'happy_customers' => 'Clientes Felices',
        'share_experience' => '¿Tienes una Experiencia que Compartir?',
        'share_experience_description' => 'Si has visitado nuestro restaurante, nos encantaría conocer tu opinión. Tu feedback nos ayuda a mejorar y servir mejor a nuestra comunidad.',
        'review_on_yelp' => 'Reseñar en Yelp',
        'contact_us' => 'Contactarnos'
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
        'explore_full_menu' => 'Explore Full Menu',
        // Menu Page
        'our_menu' => 'Our Menu',
        'discover_authentic' => 'Discover authentic Mexican cuisine with our traditional dishes, prepared with fresh ingredients and family recipes.',
        'all_categories' => 'All',
        'search_dishes' => 'Search dishes...',
        'preparing_menu' => 'Preparing your menu...',
        'loading_delicious' => 'Loading our delicious dishes',
        'oops_not_found' => 'Oops! We didn\'t find that dish',
        'no_dishes_found' => 'No dishes were found with those terms. Try other ingredients or explore our categories.',
        'clear_search' => 'Clear Search',
        'view_all_categories' => 'View All Categories',
        'spicy_dishes' => 'Spicy Dishes',
        'spicy_description' => 'Our spiciest dishes for chili lovers',
        'fresh_ingredients_title' => 'Fresh Ingredients',
        'fresh_ingredients_description' => 'We use only the freshest and highest quality ingredients',
        'made_with_love_title' => 'Made with Love',
        'made_with_love_description' => 'Each dish is prepared with the love of our family recipes',
        // Additional translations
        'popular_dishes_description' => 'The most popular dishes from our menu, prepared with love and Mexican tradition',
        'footer_description' => 'Authentic Mexican food prepared with fresh ingredients and traditional recipes. Enjoy our unique flavors with pickup service.',
        'quick_links' => 'Quick Links',
        'contact_info' => 'Contact Information',
        'business_hours' => 'Business Hours',
        'monday_saturday' => 'Monday - Saturday',
        'sunday' => 'Sunday',
        'all_rights_reserved' => 'All rights reserved',
        'terms_conditions' => 'Terms and Conditions',
        'accessibility' => 'Accessibility',
        'your_cart' => 'Your Cart',
        'cart_empty' => 'Your cart is empty',
        'total' => 'Total',
        'proceed_payment' => 'Proceed to Payment',
        // Reviews Public Page
        'what_customers_say_reviews' => 'What Our Customers Say',
        'discover_authentic_experiences' => 'Discover the authentic experiences of our customers and join the community of lovers of traditional Mexican food.',
        'site_reviews' => 'Site Reviews',
        'yelp_reviews' => 'Yelp Reviews',
        'loading_reviews' => 'Loading reviews...',
        'collecting_best_experiences' => 'Collecting the best experiences',
        'yelp_reviews_title' => 'Yelp Reviews',
        'see_all_yelp_reviews' => 'See all our reviews and ratings on Yelp',
        'yelp_widget' => 'Yelp Widget',
        'yelp_widget_description' => 'The official Yelp widget with the most recent reviews will be displayed here.',
        'view_on_yelp' => 'View on Yelp',
        'load_widget' => 'Load Widget',
        'average_rating' => 'Average Rating',
        'total_reviews' => 'Total Reviews',
        'satisfied_customers' => 'Satisfied Customers',
        'happy_customers' => 'Happy Customers',
        'share_experience' => 'Have an Experience to Share?',
        'share_experience_description' => 'If you have visited our restaurant, we would love to know your opinion. Your feedback helps us improve and better serve our community.',
        'review_on_yelp' => 'Review on Yelp',
        'contact_us' => 'Contact Us'
    ];
    
    $texts = $lang === 'en' ? $texts_en : $texts_es;
    
    return $texts[$key] ?? $default ?? $key;
}
?>
