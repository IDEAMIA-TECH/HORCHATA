    </main>
    
    <!-- Footer -->
    <footer class="footer-triciclo py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- Restaurant Info -->
                <div class="col-lg-4 mb-4">
                    <div class="footer-logo mb-3">
                        <img src="assets/images/LOGO.JPG" alt="Logo del Restaurante" class="footer-logo-img" style="height: 60px; width: auto;">
                    </div>
                    <p class="footer-text">
                        Auténtica comida mexicana preparada con ingredientes frescos y recetas tradicionales. 
                        Disfruta de nuestros sabores únicos con servicio pickup.
                    </p>
                    <div class="d-flex">
                        <a href="#" class="footer-social me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="footer-social"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="footer-subtitle mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="footer-link text-decoration-none">Inicio</a></li>
                        <li class="mb-2"><a href="menu.php" class="footer-link text-decoration-none">Menú</a></li>
                        <li class="mb-2"><a href="reviews.php" class="footer-link text-decoration-none">Reseñas</a></li>
                        <li class="mb-2"><a href="#contact" class="footer-link text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-subtitle mb-3">Información de Contacto</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt footer-icon me-2"></i>
                        <span class="footer-text">10814 Jefferson Blvd, Culver City, CA</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone footer-icon me-2"></i>
                        <span class="footer-text">+1 (310) 204-2659</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope footer-icon me-2"></i>
                        <span class="footer-text">contact@horchatamexicanfood.com</span>
                    </div>
                </div>
                
                <!-- Business Hours -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="footer-subtitle mb-3">Horarios de Atención</h6>
                    <div class="footer-text">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Lunes - Sábado:</span>
                            <span>8:30 AM - 9:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Domingo:</span>
                            <span>9:00 AM - 8:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="footer-divider my-4">
            
            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="footer-copyright mb-0">
                        &copy; <?php echo date('Y'); ?> Horchata Mexican Food. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="terms.php" class="footer-link text-decoration-none me-3">Términos y Condiciones</a>
                    <a href="accessibility.php" class="footer-link text-decoration-none">Accesibilidad</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="fas fa-shopping-cart me-2"></i>Tu Carrito
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cartItems">
                <!-- Cart items will be loaded here via AJAX -->
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p>Tu carrito está vacío</p>
                </div>
            </div>
            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>Total:</strong>
                    <span class="h5 text-primary mb-0" id="cartTotal">$0.00</span>
                </div>
                <button class="btn btn-primary w-100" id="checkoutBtn" disabled>
                    <i class="fas fa-credit-card me-2"></i>Proceder al Pago
                </button>
            </div>
        </div>
    </div>
    
    <!-- jQuery (must be loaded first) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <!-- Page specific scripts -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
