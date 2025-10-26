<?php
/**
 * Horchata Mexican Food - QR Scanner for Orders
 * Admin Panel
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Incluir configuración
require_once '../includes/db_connect.php';

// Configurar página
$page_title = 'QR Scanner';
$page_scripts = []; // JavaScript está inline

// Incluir header del admin
include 'includes/admin-header.php';
?>

<!-- Content -->
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-qrcode me-2"></i>QR Scanner
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="orders.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- QR Scanner Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-qrcode me-2"></i>Scan QR Code
                    </h6>
                </div>
                <div class="card-body text-center">
                    <!-- Camera Preview -->
                    <div id="scanner-container" class="mb-3" style="position: relative; width: 100%; max-width: 500px; margin: 0 auto;">
                        <video id="scanner-video" autoplay playsinline style="width: 100%; height: auto; border-radius: 10px; background: #000; display: none;"></video>
                        <canvas id="scanner-canvas" style="display: none;"></canvas>
                        <div id="scanner-placeholder" style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; flex-direction: column;">
                            <i class="fas fa-camera fa-3x mb-3"></i>
                            <p>Click "Start Scanner" to begin</p>
                        </div>
                    </div>
                    
                    <!-- Manual Input -->
                    <div class="mb-3">
                        <label for="order-id-input" class="form-label">Or Enter Order ID Manually</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="order-id-input" 
                                   placeholder="Enter order ID" min="1">
                            <button class="btn btn-primary" onclick="loadOrderManually()">
                                <i class="fas fa-search me-1"></i>Load Order
                            </button>
                        </div>
                    </div>
                    
                    <!-- Scanner Controls -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" id="startScannerBtn" onclick="startScanner()">
                            <i class="fas fa-play me-1"></i>Start Scanner
                        </button>
                        <button type="button" class="btn btn-danger" id="stopScannerBtn" onclick="stopScanner()" style="display: none;">
                            <i class="fas fa-stop me-1"></i>Stop Scanner
                        </button>
                    </div>
                    
                    <!-- Scanner Status -->
                    <div id="scanner-status" class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="scanner-status-text">Ready to scan QR code</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Preview -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt me-2"></i>Order Preview
                    </h6>
                </div>
                <div class="card-body" id="order-preview">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-qrcode fa-4x mb-3"></i>
                        <p>Scan a QR code or enter an order ID to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let scannerActive = false;
let scannerStream = null;

function startScanner() {
    const video = document.getElementById('scanner-video');
    const canvas = document.getElementById('scanner-canvas');
    const placeholder = document.getElementById('scanner-placeholder');
    
    console.log('Starting scanner...');
    
    // Get camera stream
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment', // Use back camera on mobile
            width: { ideal: 1280 },
            height: { ideal: 720 }
        } 
    })
    .then(function(stream) {
        console.log('Camera access granted');
        scannerActive = true;
        scannerStream = stream;
        video.srcObject = stream;
        
        // Show video and hide placeholder
        video.style.display = 'block';
        placeholder.style.display = 'none';
        
        // Show/hide buttons
        document.getElementById('startScannerBtn').style.display = 'none';
        document.getElementById('stopScannerBtn').style.display = 'inline-block';
        
        // Update status
        document.getElementById('scanner-status-text').textContent = 'Scanning...';
        document.getElementById('scanner-status').querySelector('.alert').className = 'alert alert-success';
        
        video.onloadedmetadata = function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            scanQR();
        };
        
        video.onplay = function() {
            console.log('Video is playing');
        };
    })
    .catch(function(error) {
        console.error('Error accessing camera:', error);
        document.getElementById('scanner-status-text').textContent = 'Error: Could not access camera. Please check permissions. Error: ' + error.message;
        document.getElementById('scanner-status').querySelector('.alert').className = 'alert alert-danger';
    });
}

function stopScanner() {
    console.log('Stopping scanner...');
    scannerActive = false;
    
    if (scannerStream) {
        scannerStream.getTracks().forEach(track => track.stop());
    }
    
    const video = document.getElementById('scanner-video');
    const placeholder = document.getElementById('scanner-placeholder');
    
    video.srcObject = null;
    video.style.display = 'none';
    placeholder.style.display = 'flex';
    
    // Show/hide buttons
    document.getElementById('startScannerBtn').style.display = 'inline-block';
    document.getElementById('stopScannerBtn').style.display = 'none';
    
    // Update status
    document.getElementById('scanner-status-text').textContent = 'Scanner stopped';
    document.getElementById('scanner-status').querySelector('.alert').className = 'alert alert-info';
}

function scanQR() {
    if (!scannerActive) return;
    
    const video = document.getElementById('scanner-video');
    const canvas = document.getElementById('scanner-canvas');
    const context = canvas.getContext('2d');
    
    // Draw video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Get image data
    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    
    // Scan for QR code
    const code = jsQR(imageData.data, imageData.width, imageData.height);
    
    if (code) {
        console.log('QR Code detected:', code.data);
        
        // Parse the URL to get order ID
        const url = new URL(code.data);
        const orderId = url.searchParams.get('id');
        
        if (orderId) {
            loadOrder(orderId);
        } else {
            console.error('No order ID found in QR code');
            document.getElementById('scanner-status-text').textContent = 'Invalid QR code. No order ID found.';
            document.getElementById('scanner-status').querySelector('.alert').className = 'alert alert-warning';
        }
        
        stopScanner();
    } else {
        // Continue scanning
        requestAnimationFrame(scanQR);
    }
}

function loadOrder(orderId) {
    if (!orderId) {
        orderId = document.getElementById('order-id-input').value;
    }
    
    if (!orderId) {
        alert('Please enter an order ID');
        return;
    }
    
    // Show loading
    document.getElementById('order-preview').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading order...</p>
        </div>
    `;
    
    // Load order via AJAX
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: {
            action: 'get_order_details',
            order_id: orderId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayOrderPreview(response.order);
            } else {
                document.getElementById('order-preview').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${response.message || 'Error loading order'}
                    </div>
                `;
            }
        },
        error: function() {
            document.getElementById('order-preview').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Could not connect to server
                </div>
            `;
        }
    });
}

function loadOrderManually() {
    const orderId = document.getElementById('order-id-input').value;
    loadOrder(orderId);
}

function displayOrderPreview(order) {
    const previewHtml = `
        <div class="order-preview-content">
            <div class="mb-3">
                <h6 class="text-muted">Order Number</h6>
                <p class="h4 text-primary">${order.order_number}</p>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <h6 class="text-muted">Customer</h6>
                    <p>${order.customer_name}</p>
                </div>
                <div class="col-6">
                    <h6 class="text-muted">Total</h6>
                    <p class="h5 text-success">$${parseFloat(order.total).toFixed(2)}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <h6 class="text-muted">Status</h6>
                    <span class="badge bg-${getStatusColor(order.status)} fs-6">${order.status.toUpperCase()}</span>
                </div>
                <div class="col-6">
                    <h6 class="text-muted">Payment</h6>
                    <span class="badge bg-${order.payment_status === 'paid' ? 'success' : 'warning'}">
                        ${order.payment_status.toUpperCase()}
                    </span>
                </div>
            </div>
            
            <div class="mb-3">
                <h6 class="text-muted">Items</h6>
                <p class="text-primary">${order.item_count} item(s)</p>
            </div>
            
            <div class="mt-4">
                <a href="orders.php?action=view&id=${order.id}" class="btn btn-primary w-100">
                    <i class="fas fa-eye me-2"></i>View Full Order Details
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('order-preview').innerHTML = previewHtml;
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'info',
        'preparing': 'primary',
        'ready': 'success',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

// Allow Enter key to load order
document.getElementById('order-id-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadOrderManually();
    }
});

// Handle page visibility change (pause/resume scanner)
document.addEventListener('visibilitychange', function() {
    if (document.hidden && scannerActive) {
        // Pause scanning when page is hidden
        stopScanner();
    }
});
</script>

<!-- Estilos adicionales -->
<style>
#scanner-container {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
}

#scanner-video {
    display: block;
    width: 100%;
    background: #000;
}

.card {
    border: none;
    box-shadow: var(--shadow);
    border-radius: 15px;
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), #f4d37f);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    font-weight: 600;
}

.order-preview-content {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Auto-load order if order_id is in URL (when coming from QR scan)
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    
    if (orderId) {
        console.log('Order ID found in URL:', orderId);
        document.getElementById('order-id-input').value = orderId;
        loadOrder(orderId);
    }
});
</script>

<?php
// Incluir footer del admin
include 'includes/admin-footer.php';
?>

