<?php
/**
 * Horchata Mexican Food - Página de Reseñas
 * Permite a los usuarios dejar reseñas después de recibir su pedido
 */

// Incluir configuración
require_once 'includes/db_connect.php';

// Obtener token de reseña
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header('Location: index.php');
    exit;
}

// Verificar token y obtener información de la orden
$order = fetchOne("
    SELECT o.*, 
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.review_token = ? AND o.status = 'completed'
    GROUP BY o.id
", [$token]);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Verificar si ya existe una reseña para esta orden
$existing_review = fetchOne("
    SELECT id FROM reviews 
    WHERE order_id = ? AND is_approved = 1
", [$order['id']]);

$has_reviewed = $existing_review ? true : false;

// Configurar página
$page_title = 'Dejar Reseña';
$page_scripts = ['assets/js/reviews.js'];
$page_styles = ['assets/css/reviews.css'];

// Incluir header
include 'includes/header.php';
?>

<!-- Reviews Section -->
<section class="reviews-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($has_reviewed): ?>
                    <!-- Ya ha dejado reseña -->
                    <div class="review-already-submitted">
                        <div class="text-center">
                            <div class="success-icon mb-4">
                                <i class="fas fa-check-circle fa-4x text-success"></i>
                            </div>
                            <h2 class="mb-3">¡Gracias por tu Reseña!</h2>
                            <p class="lead mb-4">
                                Ya has dejado una reseña para tu pedido #<?php echo htmlspecialchars($order['order_number']); ?>.
                                Tu opinión es muy importante para nosotros.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="index.php" class="btn btn-primary-custom">
                                    <i class="fas fa-home me-2"></i>Volver al Inicio
                                </a>
                                <a href="menu.php" class="btn btn-outline-primary-custom">
                                    <i class="fas fa-utensils me-2"></i>Hacer Otro Pedido
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Formulario de reseña -->
                    <div class="review-form-container">
                        <div class="text-center mb-5">
                            <div class="review-icon mb-4">
                                <i class="fas fa-star fa-3x text-primary-custom"></i>
                            </div>
                            <h2 class="mb-3">¿Cómo fue tu Experiencia?</h2>
                            <p class="lead">
                                Tu opinión nos ayuda a mejorar nuestro servicio. 
                                Comparte tu experiencia con el pedido #<?php echo htmlspecialchars($order['order_number']); ?>.
                            </p>
                        </div>

                        <form id="reviewForm" class="review-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <!-- Calificación -->
                            <div class="rating-section mb-4">
                                <label class="form-label h5">Calificación General</label>
                                <div class="rating-stars">
                                    <div class="star-rating" data-rating="0">
                                        <i class="fas fa-star" data-value="1"></i>
                                        <i class="fas fa-star" data-value="2"></i>
                                        <i class="fas fa-star" data-value="3"></i>
                                        <i class="fas fa-star" data-value="4"></i>
                                        <i class="fas fa-star" data-value="5"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="0">
                                    <div class="rating-text mt-2">
                                        <span id="ratingText">Selecciona una calificación</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Aspectos específicos -->
                            <div class="aspects-section mb-4">
                                <label class="form-label h5">Aspectos Específicos</label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Calidad de la Comida</label>
                                        <div class="aspect-rating">
                                            <div class="star-rating small" data-rating="0" data-aspect="food_quality">
                                                <i class="fas fa-star" data-value="1"></i>
                                                <i class="fas fa-star" data-value="2"></i>
                                                <i class="fas fa-star" data-value="3"></i>
                                                <i class="fas fa-star" data-value="4"></i>
                                                <i class="fas fa-star" data-value="5"></i>
                                            </div>
                                            <input type="hidden" name="food_quality" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tiempo de Preparación</label>
                                        <div class="aspect-rating">
                                            <div class="star-rating small" data-rating="0" data-aspect="preparation_time">
                                                <i class="fas fa-star" data-value="1"></i>
                                                <i class="fas fa-star" data-value="2"></i>
                                                <i class="fas fa-star" data-value="3"></i>
                                                <i class="fas fa-star" data-value="4"></i>
                                                <i class="fas fa-star" data-value="5"></i>
                                            </div>
                                            <input type="hidden" name="preparation_time" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Presentación</label>
                                        <div class="aspect-rating">
                                            <div class="star-rating small" data-rating="0" data-aspect="presentation">
                                                <i class="fas fa-star" data-value="1"></i>
                                                <i class="fas fa-star" data-value="2"></i>
                                                <i class="fas fa-star" data-value="3"></i>
                                                <i class="fas fa-star" data-value="4"></i>
                                                <i class="fas fa-star" data-value="5"></i>
                                            </div>
                                            <input type="hidden" name="presentation" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Servicio</label>
                                        <div class="aspect-rating">
                                            <div class="star-rating small" data-rating="0" data-aspect="service">
                                                <i class="fas fa-star" data-value="1"></i>
                                                <i class="fas fa-star" data-value="2"></i>
                                                <i class="fas fa-star" data-value="3"></i>
                                                <i class="fas fa-star" data-value="4"></i>
                                                <i class="fas fa-star" data-value="5"></i>
                                            </div>
                                            <input type="hidden" name="service" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comentarios -->
                            <div class="comments-section mb-4">
                                <label for="comments" class="form-label h5">Comentarios Adicionales</label>
                                <textarea 
                                    class="form-control" 
                                    id="comments" 
                                    name="comments" 
                                    rows="4" 
                                    placeholder="Cuéntanos más sobre tu experiencia. ¿Qué te gustó más? ¿Hay algo que podamos mejorar?"
                                ></textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Los comentarios nos ayudan a mejorar nuestro servicio.
                                </div>
                            </div>

                            <!-- Recomendación -->
                            <div class="recommendation-section mb-4">
                                <label class="form-label h5">¿Recomendarías Horchata Mexican Food?</label>
                                <div class="recommendation-options">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="recommend" id="recommend_yes" value="1">
                                        <label class="form-check-label" for="recommend_yes">
                                            <i class="fas fa-thumbs-up me-2 text-success"></i>Sí, definitivamente
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="recommend" id="recommend_maybe" value="0">
                                        <label class="form-check-label" for="recommend_maybe">
                                            <i class="fas fa-question-circle me-2 text-warning"></i>Tal vez
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="recommend" id="recommend_no" value="-1">
                                        <label class="form-check-label" for="recommend_no">
                                            <i class="fas fa-thumbs-down me-2 text-danger"></i>No
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="form-actions text-center">
                                <button type="submit" class="btn btn-primary-custom btn-lg me-3">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Reseña
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary-custom mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h5>Enviando tu reseña...</h5>
                <p class="text-muted">Por favor espera un momento</p>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir footer
include 'includes/footer.php';
?>
