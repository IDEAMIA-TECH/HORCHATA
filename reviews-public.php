<?php
/**
 * Horchata Mexican Food - Reseñas Públicas
 * Muestra reseñas del sitio y widget de Yelp
 */

// Incluir configuración
require_once 'includes/db_connect.php';
require_once 'includes/init.php';

// Obtener token de review si existe
$review_token = $_GET['token'] ?? '';

// Configurar página
$page_title = __('reviews');
$page_scripts = ['assets/js/reviews-public.js'];
$page_styles = ['assets/css/reviews-public.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Reviews Hero Section -->
<section class="reviews-hero-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="reviews-hero-title mb-4">
                    <i class="fas fa-star me-3"></i><?php echo __('what_customers_say_reviews'); ?>
                </h1>
                <p class="reviews-hero-description lead">
                    <?php echo __('discover_authentic_experiences'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($review_token)): ?>
<!-- Leave Review Section -->
<section class="leave-review-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-star me-2"></i><?php echo __('leave_review'); ?>
                        </h3>
                        <p class="text-center text-muted mb-4">
                            <?php echo __('leave_review_message'); ?>
                        </p>
                        
                        <form id="reviewForm">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($review_token); ?>">
                            
                            <div class="mb-3">
                                <label for="rating" class="form-label"><?php echo __('rating'); ?></label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value=""><?php echo __('select_rating'); ?></option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 <?php echo __('stars'); ?>)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 <?php echo __('stars'); ?>)</option>
                                    <option value="3">⭐⭐⭐ (3 <?php echo __('stars'); ?>)</option>
                                    <option value="2">⭐⭐ (2 <?php echo __('stars'); ?>)</option>
                                    <option value="1">⭐ (1 <?php echo __('star'); ?>)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="review_text" class="form-label"><?php echo __('your_review'); ?></label>
                                <textarea class="form-control" id="review_text" name="review_text" rows="5" 
                                          placeholder="<?php echo __('write_your_review_here'); ?>" required></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i><?php echo __('submit_review'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reviews Tabs Section -->
<section class="reviews-tabs-section py-5">
    <div class="container">
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs reviews-tabs mb-4" id="reviewsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="site-reviews-tab" data-bs-toggle="tab" data-bs-target="#site-reviews" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i><?php echo __('site_reviews'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="yelp-reviews-tab" data-bs-toggle="tab" data-bs-target="#yelp-reviews" type="button" role="tab">
                    <i class="fab fa-yelp me-2"></i><?php echo __('yelp_reviews'); ?>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reviewsTabsContent">
            <!-- Site Reviews Tab -->
            <div class="tab-pane fade show active" id="site-reviews" role="tabpanel">
                <div class="row g-4" id="siteReviewsContainer">
                    <!-- Reseñas del sitio se cargarán aquí via AJAX -->
                    <div class="col-12 text-center">
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary-custom" role="status">
                                <span class="visually-hidden"><?php echo __('loading_reviews'); ?></span>
                            </div>
                            <p class="mt-3 text-muted"><?php echo __('collecting_best_experiences'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yelp Reviews Tab -->
            <div class="tab-pane fade" id="yelp-reviews" role="tabpanel">
                <div class="yelp-integration">
                    <!-- Yelp Widget Placeholder -->
                    <div class="yelp-widget-container">
                        <div class="yelp-widget-header">
                            <h3 class="yelp-widget-title">
                                <i class="fab fa-yelp me-2"></i><?php echo __('yelp_reviews_title'); ?>
                            </h3>
                            <p class="yelp-widget-description">
                                <?php echo __('see_all_yelp_reviews'); ?>
                            </p>
                        </div>
                        
                        <!-- Yelp Widget (se implementará con el código oficial) -->
                        <div class="yelp-widget-placeholder">
                            <div class="yelp-placeholder-content">
                                <i class="fab fa-yelp fa-3x text-warning mb-3"></i>
                                <h4><?php echo __('yelp_widget'); ?></h4>
                                <p class="text-muted">
                                    <?php echo __('yelp_widget_description'); ?>
                                </p>
                                <div class="yelp-actions">
                                    <a href="https://www.yelp.com/biz/horchata-mexican-food-culver-city" target="_blank" class="btn btn-warning">
                                        <i class="fab fa-yelp me-2"></i><?php echo __('view_on_yelp'); ?>
                                    </a>
                                    <button class="btn btn-outline-primary" onclick="loadYelpWidget()">
                                        <i class="fas fa-sync me-2"></i><?php echo __('load_widget'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Review Stats Section -->
<section class="review-stats-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-star fa-2x text-warning"></i>
                    </div>
                    <h3 class="stat-number">4.8</h3>
                    <p class="stat-label"><?php echo __('average_rating'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-comments fa-2x text-primary"></i>
                    </div>
                    <h3 class="stat-number" id="totalReviews">0</h3>
                    <p class="stat-label"><?php echo __('total_reviews'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-thumbs-up fa-2x text-success"></i>
                    </div>
                    <h3 class="stat-number">98%</h3>
                    <p class="stat-label"><?php echo __('satisfied_customers'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-heart fa-2x text-danger"></i>
                    </div>
                    <h3 class="stat-number">1000+</h3>
                    <p class="stat-label"><?php echo __('happy_customers'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (empty($review_token)): ?>
<!-- Call to Action Section -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="cta-title mb-4"><?php echo __('share_experience'); ?></h2>
                <p class="cta-description mb-4">
                    <?php echo __('share_experience_description'); ?>
                </p>
                <div class="cta-buttons">
                    <a href="https://www.yelp.com/biz/horchata-mexican-food-culver-city" target="_blank" class="btn btn-warning btn-lg me-3">
                        <i class="fab fa-yelp me-2"></i><?php echo __('review_on_yelp'); ?>
                    </a>
                    <a href="contact.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i><?php echo __('contact_us'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($review_token)): ?>
<script>
$(document).ready(function() {
    $('#reviewForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'ajax/reviews.ajax.php',
            method: 'POST',
            data: formData + '&action=submit_review',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('¡Gracias por tu reseña!');
                    window.location.href = 'reviews-public.php';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al enviar la reseña. Por favor intenta de nuevo.');
            }
        });
    });
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
