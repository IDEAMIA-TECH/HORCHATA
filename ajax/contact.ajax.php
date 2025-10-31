<?php
/**
 * AJAX Endpoint para Formulario de Contacto
 * Horchata Mexican Food
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexión a BD
require_once '../includes/db_connect.php';
require_once '../includes/init.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception(__('method_not_allowed'));
    }
    
    // Obtener datos del formulario
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validar campos requeridos
    if (empty($name)) {
        throw new Exception(__('name') . ' ' . __('is_required'));
    }
    
    if (empty($email)) {
        throw new Exception(__('email') . ' ' . __('is_required'));
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception(__('invalid_email'));
    }
    
    if (empty($subject)) {
        throw new Exception(__('subject') . ' ' . __('is_required'));
    }
    
    if (empty($message)) {
        throw new Exception(__('message') . ' ' . __('is_required'));
    }
    
    // Guardar mensaje en la base de datos
    $sql = "INSERT INTO contact_messages 
            (name, email, phone, subject, message, newsletter, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'new')";
    
    $stmt = executeQuery($sql, [
        $name,
        $email,
        $phone ?: null,
        $subject,
        $message,
        $newsletter
    ]);
    
    if ($stmt === false) {
        throw new Exception(__('error_saving_message'));
    }
    
    // Obtener email del restaurante desde settings
    $restaurant_email = getSetting('restaurant_email', 'contact@horchatamexicanfood.com');
    
    // Enviar email de notificación (opcional - puedes implementar PHPMailer aquí)
    // Por ahora solo logueamos
    error_log("Nuevo mensaje de contacto: {$name} ({$email}) - {$subject}");
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => __('contact_message_sent_successfully')
    ]);
    
} catch (Exception $e) {
    error_log("Error en contact.ajax.php: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

