<?php
/**
 * Horchata Mexican Food - Accesibilidad WCAG
 */

// Configurar página
$page_title = 'Accesibilidad';
$page_styles = ['assets/css/accessibility.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Accessibility Section -->
<section class="accessibility-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accessibility-content">
                    <h1 class="accessibility-title mb-4">
                        <i class="fas fa-universal-access me-3"></i>Accesibilidad WCAG
                    </h1>
                    
                    <div class="accessibility-intro mb-5">
                        <p class="lead">
                            Horchata Mexican Food se compromete a hacer su sitio web accesible para todas las personas, 
                            incluyendo aquellas con discapacidades.
                        </p>
                    </div>
                    
                    <div class="accessibility-sections">
                        <!-- WCAG Commitment -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-heart me-2"></i>Nuestro Compromiso
                            </h2>
                            <div class="section-content">
                                <p>
                                    Creemos que la accesibilidad web es fundamental para crear una experiencia 
                                    inclusiva para todos nuestros visitantes. Nos esforzamos por cumplir con 
                                    las pautas de accesibilidad web (WCAG) para garantizar que nuestro sitio 
                                    sea utilizable por personas con diversas capacidades.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Measures Implemented -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-tools me-2"></i>Medidas Implementadas
                            </h2>
                            <div class="section-content">
                                <div class="measures-grid">
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-palette"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4>Contraste de Colores Adecuado</h4>
                                            <p>Utilizamos combinaciones de colores que cumplen con los estándares de contraste WCAG AA para garantizar la legibilidad.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4>Texto Alternativo para Imágenes</h4>
                                            <p>Todas las imágenes incluyen descripciones alternativas (alt text) para usuarios de lectores de pantalla.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-keyboard"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4>Navegación Compatible con Lectores de Pantalla</h4>
                                            <p>Nuestro sitio está estructurado para ser completamente navegable usando tecnologías de asistencia.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="measure-item">
                                        <div class="measure-icon">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="measure-content">
                                            <h4>Diseño Responsivo</h4>
                                            <p>El sitio se adapta a diferentes tamaños de pantalla y dispositivos para una experiencia óptima.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Features -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-star me-2"></i>Características Adicionales
                            </h2>
                            <div class="section-content">
                                <ul class="features-list">
                                    <li><i class="fas fa-check-circle me-2"></i>Estructura semántica HTML5 para mejor navegación</li>
                                    <li><i class="fas fa-check-circle me-2"></i>Enlaces descriptivos y claros</li>
                                    <li><i class="fas fa-check-circle me-2"></i>Formularios accesibles con etiquetas apropiadas</li>
                                    <li><i class="fas fa-check-circle me-2"></i>Navegación por teclado completa</li>
                                    <li><i class="fas fa-check-circle me-2"></i>Texto escalable sin pérdida de funcionalidad</li>
                                    <li><i class="fas fa-check-circle me-2"></i>Contenido organizado jerárquicamente</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Contact for Accessibility Issues -->
                        <div class="accessibility-section-item mb-5">
                            <h2 class="section-title">
                                <i class="fas fa-envelope me-2"></i>Reportar Problemas de Accesibilidad
                            </h2>
                            <div class="section-content">
                                <p>
                                    Si experimentas dificultades para acceder a cualquier parte de nuestro sitio web, 
                                    o si tienes sugerencias para mejorar la accesibilidad, por favor contáctanos:
                                </p>
                                <div class="contact-methods">
                                    <div class="contact-method">
                                        <i class="fas fa-phone me-2"></i>
                                        <span>+1 (310) 204-2659</span>
                                    </div>
                                    <div class="contact-method">
                                        <i class="fas fa-envelope me-2"></i>
                                        <span>contact@horchatamexicanfood.com</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
