<?php
// Incluir inicialización del sistema
require_once 'includes/init.php';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Horchata Mexican Food</title>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0H90QZWHZQ"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);} 
      gtag('js', new Date());

      gtag('config', 'G-0H90QZWHZQ');
    </script>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/categories.css" rel="stylesheet">
    
    <!-- Page specific CSS -->
    <?php if (isset($page_styles)): ?>
        <?php foreach ($page_styles as $style): ?>
            <link href="<?php echo $style; ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Meta tags -->
    <meta name="description" content="Auténtica comida mexicana en Horchata Mexican Food. Pedidos pickup, menú tradicional, reseñas verificadas.">
    <meta name="keywords" content="comida mexicana, horchata, tacos, burritos, pedidos online, pickup">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Global Translations for JavaScript -->
    <script>
        window.translations = {
            addedToCart: <?php echo json_encode(__('added_to_cart')); ?>,
            addedToCartPlural: <?php echo json_encode(__('added_to_cart_plural')); ?>,
            productAddedToCart: <?php echo json_encode(__('product_added_to_cart')); ?>
        };
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/LOGO.JPG" alt="Logo del Restaurante" class="logo-img" style="height: 50px; width: auto;">
            </a>
            
            <!-- Mobile toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <?php echo strtoupper(__('home')); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>" href="menu.php">
                            <?php echo strtoupper(__('menu')); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews-public.php' ? 'active' : ''; ?>" href="reviews-public.php">
                            <?php echo strtoupper(__('reviews')); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                            <?php echo strtoupper(__('contact')); ?>
                        </a>
                    </li>
                </ul>
                
                <!-- Right side actions -->
                <div class="d-flex align-items-center">
                    <!-- Language Switcher -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <?php echo getCurrentLanguage() == 'en' ? 'EN' : 'ES'; ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?lang=es">🇲🇽 <?php echo strtoupper(__('spanish')); ?></a></li>
                            <li><a class="dropdown-item" href="?lang=en">🇺🇸 <?php echo strtoupper(__('english')); ?></a></li>
                        </ul>
                    </div>
                    
                    <!-- Cart Button -->
                    <button class="btn btn-primary position-relative" id="cartBtn" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                        <?php echo strtoupper(__('cart')); ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">
                            0
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">
        <!-- Add some top padding to account for fixed navbar -->
        <div class="pt-5"></div>
