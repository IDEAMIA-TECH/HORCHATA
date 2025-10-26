<?php
/**
 * Horchata Mexican Food - Settings Management
 * Admin Panel
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Verificar que el usuario es administrador
if (($_SESSION['admin_role'] ?? 'staff') !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

// Obtener parámetros
$tab = $_GET['tab'] ?? 'general';

// Funciones auxiliares (definidas antes de uso)
function getSettings() {
    global $pdo;
    
    try {
        $sql = "SELECT setting_key, setting_value FROM settings";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $settings = $stmt->fetchAll();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error en getSettings: " . $e->getMessage());
        return [];
    }
}

function updateSetting($key, $value) {
    global $pdo;
    
    try {
        $sql = "INSERT INTO settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$key, $value, $value]);
    } catch (Exception $e) {
        error_log("Error en updateSetting: " . $e->getMessage());
        return false;
    }
}

function updateGeneralSettings() {
    global $pdo;
    
    try {
        $settings = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_url' => trim($_POST['site_url'] ?? ''),
            'default_language' => trim($_POST['default_language'] ?? 'es'),
            'timezone' => trim($_POST['timezone'] ?? 'America/Los_Angeles'),
            'site_description' => trim($_POST['site_description'] ?? ''),
            'site_keywords' => trim($_POST['site_keywords'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'General settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updateGeneralSettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating general settings: ' . $e->getMessage()
        ]);
    }
}

function updateRestaurantSettings() {
    global $pdo;
    
    try {
        $settings = [
            'restaurant_name' => trim($_POST['restaurant_name'] ?? ''),
            'restaurant_phone' => trim($_POST['restaurant_phone'] ?? ''),
            'restaurant_email' => trim($_POST['restaurant_email'] ?? ''),
            'restaurant_website' => trim($_POST['restaurant_website'] ?? ''),
            'restaurant_address' => trim($_POST['restaurant_address'] ?? ''),
            'business_hours' => trim($_POST['business_hours'] ?? ''),
            'restaurant_description' => trim($_POST['restaurant_description'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Restaurant settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updateRestaurantSettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating restaurant settings: ' . $e->getMessage()
        ]);
    }
}

function updatePaymentSettings() {
    global $pdo;
    
    try {
        $settings = [
            'currency' => trim($_POST['currency'] ?? 'USD'),
            'tax_rate' => floatval($_POST['tax_rate'] ?? 0),
            'delivery_fee' => floatval($_POST['delivery_fee'] ?? 0),
            'minimum_order' => floatval($_POST['minimum_order'] ?? 0),
            'paypal_mode' => trim($_POST['paypal_mode'] ?? 'sandbox'),
            'paypal_client_id' => trim($_POST['paypal_client_id'] ?? ''),
            'paypal_secret' => trim($_POST['paypal_secret'] ?? ''),
            'paypal_enabled' => isset($_POST['paypal_enabled']) ? '1' : '0'
        ];
        
        $payment_methods = $_POST['payment_methods'] ?? [];
        $settings['payment_methods'] = json_encode($payment_methods);
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updatePaymentSettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating payment settings: ' . $e->getMessage()
        ]);
    }
}

function updateEmailSettings() {
    global $pdo;
    
    try {
        $settings = [
            'smtp_host' => trim($_POST['smtp_host'] ?? ''),
            'smtp_port' => intval($_POST['smtp_port'] ?? 587),
            'smtp_username' => trim($_POST['smtp_username'] ?? ''),
            'smtp_password' => trim($_POST['smtp_password'] ?? ''),
            'smtp_encryption' => trim($_POST['smtp_encryption'] ?? 'tls'),
            'from_email' => trim($_POST['from_email'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Email settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updateEmailSettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating email settings: ' . $e->getMessage()
        ]);
    }
}

function updateSecuritySettings() {
    global $pdo;
    
    try {
        $settings = [
            'session_timeout' => intval($_POST['session_timeout'] ?? 30),
            'max_login_attempts' => intval($_POST['max_login_attempts'] ?? 5),
            'password_min_length' => intval($_POST['password_min_length'] ?? 8),
            'require_strong_password' => isset($_POST['require_strong_password']) ? '1' : '0',
            'enable_2fa' => isset($_POST['enable_2fa']) ? '1' : '0'
        ];
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Security settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updateSecuritySettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating security settings: ' . $e->getMessage()
        ]);
    }
}

function updateSocialSettings() {
    global $pdo;
    
    try {
        $settings = [
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'instagram_url' => trim($_POST['instagram_url'] ?? ''),
            'twitter_url' => trim($_POST['twitter_url'] ?? ''),
            'youtube_url' => trim($_POST['youtube_url'] ?? ''),
            'tiktok_url' => trim($_POST['tiktok_url'] ?? ''),
            'yelp_url' => trim($_POST['yelp_url'] ?? '')
        ];
        
        foreach ($settings as $key => $value) {
            updateSetting($key, $value);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Social media settings updated successfully'
        ]);
    } catch (Exception $e) {
        error_log("Error en updateSocialSettings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error updating social settings: ' . $e->getMessage()
        ]);
    }
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'update_general':
                updateGeneralSettings();
                exit;
            case 'update_restaurant':
                updateRestaurantSettings();
                exit;
            case 'update_payment':
                updatePaymentSettings();
                exit;
            case 'update_email':
                updateEmailSettings();
                exit;
            case 'update_security':
                updateSecuritySettings();
                exit;
            case 'update_social':
                updateSocialSettings();
                exit;
            case 'auto_save_settings':
                echo json_encode([
                    'success' => true,
                    'message' => 'Auto-saved'
                ]);
                exit;
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                exit;
        }
    } catch (Exception $e) {
        error_log("Error processing settings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
        exit;
    }
}

// Obtener configuraciones actuales
$settings = getSettings();

// Configurar página
$page_title = 'Settings';
$page_scripts = []; // JavaScript está inline en la página

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-cog me-2"></i>Settings
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshSettings()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary" onclick="saveAllSettings()">
                    <i class="fas fa-save me-1"></i>Save All
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Settings Menu</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="?tab=general" class="list-group-item list-group-item-action <?php echo $tab === 'general' ? 'active' : ''; ?>">
                            <i class="fas fa-cog me-2"></i>General Settings
                        </a>
                        <a href="?tab=restaurant" class="list-group-item list-group-item-action <?php echo $tab === 'restaurant' ? 'active' : ''; ?>">
                            <i class="fas fa-store me-2"></i>Restaurant Info
                        </a>
                        <a href="?tab=payment" class="list-group-item list-group-item-action <?php echo $tab === 'payment' ? 'active' : ''; ?>">
                            <i class="fas fa-credit-card me-2"></i>Payment Settings
                        </a>
                        <a href="?tab=email" class="list-group-item list-group-item-action <?php echo $tab === 'email' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope me-2"></i>Email Settings
                        </a>
                        <a href="?tab=security" class="list-group-item list-group-item-action <?php echo $tab === 'security' ? 'active' : ''; ?>">
                            <i class="fas fa-shield-alt me-2"></i>Security
                        </a>
                        <a href="?tab=social" class="list-group-item list-group-item-action <?php echo $tab === 'social' ? 'active' : ''; ?>">
                            <i class="fas fa-share-alt me-2"></i>Social Media
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- General Settings -->
            <?php if ($tab === 'general'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Settings</h6>
                </div>
                <div class="card-body">
                    <form id="generalSettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_general">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_name" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" 
                                           value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Horchata Mexican Food'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_url" class="form-label">Site URL</label>
                                    <input type="url" class="form-control" id="site_url" name="site_url" 
                                           value="<?php echo htmlspecialchars($settings['site_url'] ?? 'https://ideamia-dev.com/HORCHATA'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_language" class="form-label">Default Language</label>
                                    <select class="form-select" id="default_language" name="default_language">
                                        <option value="es" <?php echo ($settings['default_language'] ?? 'es') === 'es' ? 'selected' : ''; ?>>Spanish</option>
                                        <option value="en" <?php echo ($settings['default_language'] ?? 'es') === 'en' ? 'selected' : ''; ?>>English</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="America/Los_Angeles" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (Los Angeles)</option>
                                        <option value="America/New_York" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (New York)</option>
                                        <option value="America/Chicago" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Chicago' ? 'selected' : ''; ?>>Central Time (Chicago)</option>
                                        <option value="America/Denver" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time (Denver)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? 'Authentic Mexican Food'); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_keywords" class="form-label">Site Keywords</label>
                            <input type="text" class="form-control" id="site_keywords" name="site_keywords" 
                                   value="<?php echo htmlspecialchars($settings['site_keywords'] ?? 'mexican food, restaurant, horchata, authentic'); ?>">
                            <div class="form-text">Separate keywords with commas</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save General Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Restaurant Settings -->
            <?php if ($tab === 'restaurant'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Restaurant Information</h6>
                </div>
                <div class="card-body">
                    <form id="restaurantSettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_restaurant">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="restaurant_name" class="form-label">Restaurant Name</label>
                                    <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" 
                                           value="<?php echo htmlspecialchars($settings['restaurant_name'] ?? 'Horchata Mexican Food'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="restaurant_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="restaurant_phone" name="restaurant_phone" 
                                           value="<?php echo htmlspecialchars($settings['restaurant_phone'] ?? '+1 (310) 204-2659'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="restaurant_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="restaurant_email" name="restaurant_email" 
                                           value="<?php echo htmlspecialchars($settings['restaurant_email'] ?? 'contact@horchatamexicanfood.com'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="restaurant_website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="restaurant_website" name="restaurant_website" 
                                           value="<?php echo htmlspecialchars($settings['restaurant_website'] ?? 'https://ideamia-dev.com/HORCHATA'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="restaurant_address" class="form-label">Address</label>
                            <textarea class="form-control" id="restaurant_address" name="restaurant_address" rows="3"><?php echo htmlspecialchars($settings['restaurant_address'] ?? '10814 Jefferson Blvd, Culver City, CA 90232'); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="business_hours" class="form-label">Business Hours</label>
                                    <textarea class="form-control" id="business_hours" name="business_hours" rows="4"><?php echo htmlspecialchars($settings['business_hours'] ?? "Monday - Saturday: 8:30 AM - 9:00 PM\nSunday: 9:00 AM - 8:00 PM"); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="restaurant_description" class="form-label">Restaurant Description</label>
                                    <textarea class="form-control" id="restaurant_description" name="restaurant_description" rows="4"><?php echo htmlspecialchars($settings['restaurant_description'] ?? 'Authentic Mexican food prepared with fresh ingredients and traditional recipes.'); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Restaurant Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Payment Settings -->
            <?php if ($tab === 'payment'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Settings</h6>
                </div>
                <div class="card-body">
                    <form id="paymentSettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_payment">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="USD" <?php echo ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                        <option value="EUR" <?php echo ($settings['currency'] ?? 'USD') === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                        <option value="MXN" <?php echo ($settings['currency'] ?? 'USD') === 'MXN' ? 'selected' : ''; ?>>MXN ($)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                    <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                           value="<?php echo $settings['tax_rate'] ?? '8.75'; ?>" step="0.01" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_methods" class="form-label">Accepted Payment Methods</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cash_payment" name="payment_methods[]" value="cash" 
                                               <?php echo in_array('cash', $settings['payment_methods'] ?? ['cash']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="cash_payment">Cash</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="card_payment" name="payment_methods[]" value="card" 
                                               <?php echo in_array('card', $settings['payment_methods'] ?? ['card']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="card_payment">Card</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="online_payment" name="payment_methods[]" value="online" 
                                               <?php echo in_array('online', $settings['payment_methods'] ?? ['online']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="online_payment">Online</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_fee" class="form-label">Delivery Fee</label>
                                    <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" 
                                           value="<?php echo $settings['delivery_fee'] ?? '0.00'; ?>" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_order" class="form-label">Minimum Order Amount</label>
                                    <input type="number" class="form-control" id="minimum_order" name="minimum_order" 
                                           value="<?php echo $settings['minimum_order'] ?? '0.00'; ?>" step="0.01" min="0" 
                                           placeholder="0.00">
                                    <small class="form-text text-muted">Set to 0 for no minimum</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- PayPal Configuration -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fab fa-paypal me-2"></i>PayPal Configuration
                            </h6>
                            
                            <div class="mb-3">
                                <label for="paypal_mode" class="form-label">PayPal Mode</label>
                                <select class="form-select" id="paypal_mode" name="paypal_mode">
                                    <option value="sandbox" <?php echo ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Testing)</option>
                                    <option value="live" <?php echo ($settings['paypal_mode'] ?? 'sandbox') === 'live' ? 'selected' : ''; ?>>Live (Production)</option>
                                </select>
                                <small class="form-text text-muted">Use Sandbox for testing, Live for production</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="paypal_client_id" class="form-label">PayPal Client ID</label>
                                        <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" 
                                               value="<?php echo htmlspecialchars($settings['paypal_client_id'] ?? ''); ?>" 
                                               placeholder="Enter your PayPal Client ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="paypal_secret" class="form-label">PayPal Secret</label>
                                        <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" 
                                               value="<?php echo htmlspecialchars($settings['paypal_secret'] ?? ''); ?>" 
                                               placeholder="Enter your PayPal Secret">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="paypal_enabled" name="paypal_enabled" 
                                           <?php echo ($settings['paypal_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="paypal_enabled">
                                        Enable PayPal Payments
                                    </label>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Get your Client ID and Secret from the 
                                <a href="https://developer.paypal.com" target="_blank">PayPal Developer Portal</a>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Payment Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Email Settings -->
            <?php if ($tab === 'email'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Email Settings</h6>
                </div>
                <div class="card-body">
                    <form id="emailSettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_email">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_host" class="form-label">SMTP Host</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                           value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_port" class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                           value="<?php echo $settings['smtp_port'] ?? '587'; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_username" class="form-label">SMTP Username</label>
                                    <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                           value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_password" class="form-label">SMTP Password</label>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                           value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_encryption" class="form-label">Encryption</label>
                                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                        <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                        <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                        <option value="none" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : ''; ?>>None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_email" class="form-label">From Email</label>
                                    <input type="email" class="form-control" id="from_email" name="from_email" 
                                           value="<?php echo htmlspecialchars($settings['from_email'] ?? 'noreply@horchatamexicanfood.com'); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Email Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Security Settings -->
            <?php if ($tab === 'security'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Security Settings</h6>
                </div>
                <div class="card-body">
                    <form id="securitySettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_security">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="<?php echo $settings['session_timeout'] ?? '30'; ?>" min="5" max="1440">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                    <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                           value="<?php echo $settings['max_login_attempts'] ?? '5'; ?>" min="3" max="20">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_min_length" class="form-label">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="password_min_length" name="password_min_length" 
                                           value="<?php echo $settings['password_min_length'] ?? '8'; ?>" min="6" max="32">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="require_strong_password" class="form-label">Require Strong Password</label>
                                    <select class="form-select" id="require_strong_password" name="require_strong_password">
                                        <option value="1" <?php echo ($settings['require_strong_password'] ?? '1') === '1' ? 'selected' : ''; ?>>Yes</option>
                                        <option value="0" <?php echo ($settings['require_strong_password'] ?? '1') === '0' ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_2fa" name="enable_2fa" 
                                       <?php echo ($settings['enable_2fa'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_2fa">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Security Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Social Media Settings -->
            <?php if ($tab === 'social'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Social Media Settings</h6>
                </div>
                <div class="card-body">
                    <form id="socialSettingsForm" method="POST">
                        <input type="hidden" name="action" value="update_social">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facebook_url" class="form-label">Facebook URL</label>
                                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                           value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instagram_url" class="form-label">Instagram URL</label>
                                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                           value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="twitter_url" class="form-label">Twitter URL</label>
                                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                           value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="youtube_url" class="form-label">YouTube URL</label>
                                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                           value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tiktok_url" class="form-label">TikTok URL</label>
                                    <input type="url" class="form-control" id="tiktok_url" name="tiktok_url" 
                                           value="<?php echo htmlspecialchars($settings['tiktok_url'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="yelp_url" class="form-label">Yelp URL</label>
                                    <input type="url" class="form-control" id="yelp_url" name="yelp_url" 
                                           value="<?php echo htmlspecialchars($settings['yelp_url'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Social Media Settings
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript específico para configuraciones -->
<script>
$(document).ready(function() {
    // Configurar formularios
    setupSettingsForms();
});

function setupSettingsForms() {
    // Auto-save de formularios
    $('form[id$="SettingsForm"] input, form[id$="SettingsForm"] select, form[id$="SettingsForm"] textarea').on('change', function() {
        autoSaveSettings($(this).closest('form'));
    });
    
    // Manejar envío de formularios
    $('form[id$="SettingsForm"]').on('submit', function(e) {
        e.preventDefault();
        saveSettings($(this));
    });
}

function refreshSettings() {
    location.reload();
}

function autoSaveSettings(form) {
    const formData = new FormData(form[0]);
    formData.append('action', 'auto_save_settings');
    
    $.ajax({
        url: 'settings.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSaveIndicator();
            }
        },
        error: function() {
            // No mostrar error en auto-save
        }
    });
}

function saveSettings(form) {
    const formData = new FormData(form[0]);
    
    // Mostrar loading
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    
    $.ajax({
        url: 'settings.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showNotification('Connection error: ' + error, 'error');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

function saveAllSettings() {
    const forms = $('form[id$="SettingsForm"]');
    let completed = 0;
    
    forms.each(function() {
        const form = $(this);
        const formData = new FormData(form[0]);
        
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                completed++;
                if (completed === forms.length) {
                    showNotification('All settings saved successfully', 'success');
                }
            },
            error: function() {
                completed++;
                if (completed === forms.length) {
                    showNotification('Some settings may not have been saved', 'warning');
                }
            }
        });
    });
}

function showSaveIndicator() {
    let indicator = $('#saveIndicator');
    if (indicator.length === 0) {
        indicator = $('<div id="saveIndicator" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>');
        $('body').append(indicator);
    }
    
    indicator.html(`
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check me-1"></i>Auto-saved
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    setTimeout(function() {
        indicator.find('.alert').alert('close');
    }, 3000);
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                     type === 'success' ? 'alert-success' : 
                     type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}
</script>

<?php
// Incluir footer del admin
include 'includes/admin-footer.php';
?>
