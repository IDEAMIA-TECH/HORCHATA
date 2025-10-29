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

// Función para detectar país por IP
function detectCountryByIP($ip = null) {
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Si está detrás de un proxy, intentar obtener la IP real
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
    }
    
    // Si la IP es localhost, devolver un país por defecto
    if ($ip === '127.0.0.1' || $ip === '::1' || empty($ip)) {
        return DEFAULT_LANGUAGE === 'es' ? 'MX' : 'US';
    }
    
    try {
        // Usar ipapi.co para detectar el país
        $response = @file_get_contents("https://ipapi.co/{$ip}/country_code/");
        if ($response !== false) {
            return trim($response);
        }
    } catch (Exception $e) {
        error_log("Error detecting country by IP: " . $e->getMessage());
    }
    
    // Valor por defecto
    return DEFAULT_LANGUAGE === 'es' ? 'MX' : 'US';
}

// Función para obtener idioma basado en país
function getLanguageByCountry($country) {
    // Países que usan español (prioridad)
    $spanish_countries = ['MX', 'ES', 'AR', 'CO', 'CL', 'PE', 'VE', 'EC', 'GT', 'CU', 'BO', 'DO', 'HN', 'PY', 'SV', 'NI', 'CR', 'PA', 'UY', 'PY', 'GQ'];
    
    // Países que usan inglés
    $english_countries = ['US', 'CA', 'GB', 'IE', 'AU', 'NZ', 'ZA', 'SG', 'MY', 'PH', 'IN'];
    
    if (in_array($country, $spanish_countries)) {
        return 'es';
    } elseif (in_array($country, $english_countries)) {
        return 'en';
    }
    
    // Por defecto, usar español
    return 'es';
}

// Procesar cambio de idioma
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
    $_SESSION['language'] = $_GET['lang'];
    // Guardar que el usuario cambió manualmente el idioma
    $_SESSION['language_manual'] = true;
    // No redirigir automáticamente para evitar bucles de refresh
}

// Establecer idioma por defecto si no está definido
if (!isset($_SESSION['language'])) {
    // Si no hay cambio manual, detectar país
    if (!isset($_SESSION['language_manual'])) {
        $country = detectCountryByIP();
        $detected_lang = getLanguageByCountry($country);
        $_SESSION['language'] = $detected_lang;
        error_log("Auto-detected language: $detected_lang for country: $country");
    } else {
        $_SESSION['language'] = DEFAULT_LANGUAGE;
    }
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
        'contact_us' => 'Contactarnos',
        // Terms and Conditions Page
        'terms_conditions_title' => 'Términos y Condiciones',
        'terms_intro' => 'El sitio web de Horchata Mexican Food es únicamente para fines informativos y no realiza ventas en línea. Todos los derechos de propiedad intelectual están protegidos, y no nos hacemos responsables por errores en la información publicada.',
        'website_use' => 'Uso del Sitio Web',
        'website_use_description' => 'Este sitio es únicamente para fines informativos. No aceptamos pedidos en línea ni realizamos ventas a través de esta plataforma.',
        'intellectual_property' => 'Propiedad Intelectual',
        'intellectual_property_description' => 'Todo el contenido de este sitio, incluyendo texto, imágenes y logos, es propiedad de Horchata Mexican Food.',
        'disclaimer' => 'Exención de Responsabilidad',
        'disclaimer_description' => 'Horchata Mexican Food no se hace responsable por errores en la información del sitio o por daños derivados de su uso.',
        'contact_information' => 'Información de Contacto',
        // Accessibility Page
        'accessibility_title' => 'Accesibilidad WCAG',
        'accessibility_intro' => 'Horchata Mexican Food se compromete a hacer su sitio web accesible para todas las personas, incluyendo aquellas con discapacidades.',
        'our_commitment' => 'Nuestro Compromiso',
        'our_commitment_description' => 'Creemos que la accesibilidad web es fundamental para crear una experiencia inclusiva para todos nuestros visitantes. Nos esforzamos por cumplir con las pautas de accesibilidad web (WCAG) para garantizar que nuestro sitio sea utilizable por personas con diversas capacidades.',
        'measures_implemented' => 'Medidas Implementadas',
        'adequate_color_contrast' => 'Contraste de Colores Adecuado',
        'adequate_color_contrast_description' => 'Utilizamos combinaciones de colores que cumplen con los estándares de contraste WCAG AA para garantizar la legibilidad.',
        'alt_text_images' => 'Texto Alternativo para Imágenes',
        'alt_text_images_description' => 'Todas las imágenes incluyen descripciones alternativas (alt text) para usuarios de lectores de pantalla.',
        'screen_reader_navigation' => 'Navegación Compatible con Lectores de Pantalla',
        'screen_reader_navigation_description' => 'Nuestro sitio está estructurado para ser completamente navegable usando tecnologías de asistencia.',
        'responsive_design' => 'Diseño Responsivo',
        'responsive_design_description' => 'El sitio se adapta a diferentes tamaños de pantalla y dispositivos para una experiencia óptima.',
        'additional_features' => 'Características Adicionales',
        'semantic_html5' => 'Estructura semántica HTML5 para mejor navegación',
        'descriptive_links' => 'Enlaces descriptivos y claros',
        'accessible_forms' => 'Formularios accesibles con etiquetas apropiadas',
        'keyboard_navigation' => 'Navegación por teclado completa',
        'scalable_text' => 'Texto escalable sin pérdida de funcionalidad',
        'hierarchical_content' => 'Contenido organizado jerárquicamente',
        'report_accessibility_issues' => 'Reportar Problemas de Accesibilidad',
        'report_accessibility_description' => 'Si experimentas dificultades para acceder a cualquier parte de nuestro sitio web, o si tienes sugerencias para mejorar la accesibilidad, por favor contáctanos:',
        // Product Detail Page
        'quantity' => 'Cantidad',
        'ingredients' => 'Ingredientes',
        'nutritional_information' => 'Información Nutricional',
        'preparation_time' => 'Tiempo de preparación',
        'spicy_level' => 'Nivel de picante',
        'preparation_time_default' => 'Tiempo de preparación: 15-20 min',
        'spicy_level_default' => 'Nivel de picante: Medio',
        'traditional_recipe' => 'Receta tradicional',
        'ingredients_description' => 'Ingredientes frescos seleccionados cuidadosamente para garantizar la mejor calidad y sabor auténtico.',
        'fresh_ingredients_list_1' => 'Carne de res premium',
        'fresh_ingredients_list_2' => 'Cebolla y ajo frescos',
        'fresh_ingredients_list_3' => 'Especias tradicionales',
        'fresh_ingredients_list_4' => 'Tortillas de maíz',
        'nutritional_info_default' => 'La información nutricional detallada no está disponible para este producto.',
        'favorites' => 'Favoritos',
        'related_products' => 'Productos Relacionados',
        'related_products_description' => 'Otros platillos de la misma categoría que podrían interesarte',
        'view' => 'Ver',
        // Customize Modal
        'customize_order' => 'Personalizar Pedido',
        'special_instructions' => 'Instrucciones Especiales',
        'special_instructions_placeholder' => 'Ej: Sin cebolla, sin tomate, sin cilantro...',
        'special_instructions_help' => 'Agrega cualquier instrucción especial para tu pedido',
        'extras' => 'Extras',
        'extra_cheese' => 'Queso Extra',
        'extra_guacamole' => 'Guacamole Extra',
        'extra_sour_cream' => 'Crema Agria Extra',
        'spice_level' => 'Nivel de Picante',
        'mild' => 'Suave',
        'hot' => 'Picante',
        'extra_hot' => 'Extra Picante',
        'cancel' => 'Cancelar',
        // Order Success Page
        'order_confirmed' => 'Pedido Confirmado',
        'order_confirmed_message' => 'Gracias por tu pedido. Te hemos enviado un correo de confirmación con todos los detalles.',
        'order_details' => 'Detalles del Pedido',
        'order_number' => 'Número de Pedido',
        'order_date' => 'Fecha de Pedido',
        'order_items' => 'Items del Pedido',
        'payment_information' => 'Información de Pago',
        'payment_method' => 'Método de Pago',
        'payment_status' => 'Estado del Pago',
        'pay_on_pickup' => 'Pagar al Recoger',
        'contact_information' => 'Información de Contacto',
        'phone' => 'Teléfono',
        'email' => 'Correo',
        'address' => 'Dirección',
        'back_to_home' => 'Volver al Inicio',
        'view_menu' => 'Ver Menú',
        'print_order' => 'Imprimir Pedido',
        'leave_review' => 'Deja una Reseña',
        'leave_review_message' => '¡Nos encantaría saber qué te pareció tu experiencia! Deja una reseña y ayuda a otros clientes.',
        'write_review' => 'Escribir Reseña',
        // Review Form
        'rating' => 'Calificación',
        'select_rating' => 'Selecciona una calificación',
        'stars' => 'estrellas',
        'star' => 'estrella',
        'your_review' => 'Tu Reseña',
        'write_your_review_here' => 'Escribe tu reseña aquí...',
        'submit_review' => 'Enviar Reseña',
        // Review error messages
        'error_loading_reviews' => 'Error al cargar reseñas',
        'error_loading_public_reviews' => 'Error al cargar las reseñas públicas',
        'try_again' => 'Intentar de nuevo',
        // Additional order success translations
        'qr_code_for_restaurant' => 'Código QR para el Restaurante',
        'scan_qr_to_view_order' => 'Escanea este código para ver el pedido en el sistema',
        'customer' => 'Cliente',
        'status' => 'Estado',
        'subtotal' => 'Subtotal',
        'tax' => 'Impuestos',
        'quantity' => 'Cantidad',
        // Product customization extras
        'guacamole' => 'Guacamole',
        'sour_cream' => 'Crema',
        'cheese' => 'Queso',
        // Extras Management
        'extras_management' => 'Gestión de Extras',
        'add_extra' => 'Agregar Extra',
        'edit_extra' => 'Editar Extra',
        'extras_list' => 'Lista de Extras',
        'assign_extras_to_products' => 'Asignar Extras a Productos',
        'select_product' => 'Seleccionar Producto',
        'available_extras' => 'Extras Disponibles',
        'select_product_first' => 'Selecciona un producto primero',
        'no_category' => 'Sin categoría',
        'confirm_delete_extra' => '¿Estás seguro de que quieres eliminar este extra?'
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
        'contact_us' => 'Contact Us',
        // Terms and Conditions Page
        'terms_conditions_title' => 'Terms and Conditions',
        'terms_intro' => 'The Horchata Mexican Food website is for informational purposes only and does not conduct online sales. All intellectual property rights are protected, and we are not responsible for any errors in the information published.',
        'website_use' => 'Use of the Website',
        'website_use_description' => 'This site is for informational purposes only. We do not accept online orders or conduct sales through this platform.',
        'intellectual_property' => 'Intellectual Property',
        'intellectual_property_description' => 'All content on this site, including text, images, and logos, is the property of Horchata Mexican Food.',
        'disclaimer' => 'Disclaimer of Liability',
        'disclaimer_description' => 'Horchata Mexican Food is not responsible for errors in the information on the site or for damages arising from its use.',
        'contact_information' => 'Contact Information',
        // Accessibility Page
        'accessibility_title' => 'WCAG Accessibility',
        'accessibility_intro' => 'Horchata Mexican Food is committed to making its website accessible to all people, including those with disabilities.',
        'our_commitment' => 'Our Commitment',
        'our_commitment_description' => 'We believe that web accessibility is fundamental to creating an inclusive experience for all our visitors. We strive to comply with web accessibility guidelines (WCAG) to ensure our site is usable by people with diverse abilities.',
        'measures_implemented' => 'Measures Implemented',
        'adequate_color_contrast' => 'Adequate Color Contrasts',
        'adequate_color_contrast_description' => 'We use color combinations that meet WCAG AA contrast standards to ensure readability.',
        'alt_text_images' => 'Text Alternatives for Images',
        'alt_text_images_description' => 'All images include alternative descriptions (alt text) for screen reader users.',
        'screen_reader_navigation' => 'Screen Reader-Friendly Navigation',
        'screen_reader_navigation_description' => 'Our site is structured to be completely navigable using assistive technologies.',
        'responsive_design' => 'Responsive Design',
        'responsive_design_description' => 'The site adapts to different screen sizes and devices for an optimal experience.',
        'additional_features' => 'Additional Features',
        'semantic_html5' => 'Semantic HTML5 structure for better navigation',
        'descriptive_links' => 'Descriptive and clear links',
        'accessible_forms' => 'Accessible forms with appropriate labels',
        'keyboard_navigation' => 'Complete keyboard navigation',
        'scalable_text' => 'Scalable text without loss of functionality',
        'hierarchical_content' => 'Hierarchically organized content',
        'report_accessibility_issues' => 'Report Accessibility Issues',
        'report_accessibility_description' => 'If you experience difficulties accessing any part of our website, or if you have suggestions to improve accessibility, please contact us:',
        // Product Detail Page
        'quantity' => 'Quantity',
        'ingredients' => 'Ingredients',
        'nutritional_information' => 'Nutritional Information',
        'preparation_time' => 'Preparation time',
        'spicy_level' => 'Spicy level',
        'preparation_time_default' => 'Preparation time: 15-20 min',
        'spicy_level_default' => 'Spicy level: Medium',
        'traditional_recipe' => 'Traditional recipe',
        'ingredients_description' => 'Fresh ingredients carefully selected to ensure the best quality and authentic flavor.',
        'fresh_ingredients_list_1' => 'Premium beef',
        'fresh_ingredients_list_2' => 'Fresh onion and garlic',
        'fresh_ingredients_list_3' => 'Traditional spices',
        'fresh_ingredients_list_4' => 'Corn tortillas',
        'nutritional_info_default' => 'Detailed nutritional information is not available for this product.',
        'favorites' => 'Favorites',
        'related_products' => 'Related Products',
        'related_products_description' => 'Other dishes from the same category that might interest you',
        'view' => 'View',
        // Customize Modal
        'customize_order' => 'Customize Order',
        'special_instructions' => 'Special Instructions',
        'special_instructions_placeholder' => 'E.g., No onion, no tomatoes, no cilantro...',
        'special_instructions_help' => 'Add any special instructions for your order',
        'extras' => 'Extras',
        'extra_cheese' => 'Extra Cheese',
        'extra_guacamole' => 'Extra Guacamole',
        'extra_sour_cream' => 'Extra Sour Cream',
        'spice_level' => 'Spice Level',
        'mild' => 'Mild',
        'hot' => 'Hot',
        'extra_hot' => 'Extra Hot',
        'cancel' => 'Cancel',
        // Order Success Page
        'order_confirmed' => 'Order Confirmed',
        'order_confirmed_message' => 'Thank you for your order. We have sent you a confirmation email with all the details.',
        'order_details' => 'Order Details',
        'order_number' => 'Order Number',
        'order_date' => 'Order Date',
        'order_items' => 'Order Items',
        'payment_information' => 'Payment Information',
        'payment_method' => 'Payment Method',
        'payment_status' => 'Payment Status',
        'pay_on_pickup' => 'Pay on Pickup',
        'contact_information' => 'Contact Information',
        'phone' => 'Phone',
        'email' => 'Email',
        'address' => 'Address',
        'back_to_home' => 'Back to Home',
        'view_menu' => 'View Menu',
        'print_order' => 'Print Order',
        'leave_review' => 'Leave a Review',
        'leave_review_message' => 'We would love to hear about your experience! Leave a review and help other customers.',
        'write_review' => 'Write Review',
        // Review Form
        'rating' => 'Rating',
        'select_rating' => 'Select a rating',
        'stars' => 'stars',
        'star' => 'star',
        'your_review' => 'Your Review',
        'write_your_review_here' => 'Write your review here...',
        'submit_review' => 'Submit Review',
        // Review error messages
        'error_loading_reviews' => 'Error loading reviews',
        'error_loading_public_reviews' => 'Error loading public reviews',
        'try_again' => 'Try again',
        // Additional order success translations
        'qr_code_for_restaurant' => 'QR Code for Restaurant',
        'scan_qr_to_view_order' => 'Scan this code to view the order in the system',
        'customer' => 'Customer',
        'status' => 'Status',
        'subtotal' => 'Subtotal',
        'tax' => 'Tax',
        'quantity' => 'Quantity',
        // Product customization extras
        'guacamole' => 'Guacamole',
        'sour_cream' => 'Sour Cream',
        'cheese' => 'Cheese',
        // Extras Management
        'extras_management' => 'Extras Management',
        'add_extra' => 'Add Extra',
        'edit_extra' => 'Edit Extra',
        'extras_list' => 'Extras List',
        'assign_extras_to_products' => 'Assign Extras to Products',
        'select_product' => 'Select Product',
        'available_extras' => 'Available Extras',
        'select_product_first' => 'Select a product first',
        'no_category' => 'No category',
        'confirm_delete_extra' => 'Are you sure you want to delete this extra?'
    ];
    
    $texts = $lang === 'en' ? $texts_en : $texts_es;
    
    return $texts[$key] ?? $default ?? $key;
}

/**
 * Obtener configuración del sistema
 */
function getSetting($key, $default = '') {
    static $settings = null;
    
    // Si las configuraciones no están cargadas, cargarlas
    if ($settings === null) {
        $settings = [];
        
        // Solo cargar si hay conexión a BD
        if (file_exists(__DIR__ . '/db_connect.php')) {
            try {
                require_once __DIR__ . '/db_connect.php';
                
                // Verificar si $pdo existe después de incluir
                if (isset($pdo)) {
                    $sql = "SELECT setting_key, setting_value FROM settings";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($results as $result) {
                        $settings[$result['setting_key']] = $result['setting_value'];
                    }
                }
            } catch (Exception $e) {
                error_log("Error loading settings: " . $e->getMessage());
            }
        }
    }
    
    return $settings[$key] ?? $default;
}
?>
