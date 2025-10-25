/**
 * Products Management JavaScript
 * Funcionalidades para la gestión de productos en el panel de administración
 */

$(document).ready(function() {
    // Inicializar DataTable
    if ($.fn.DataTable) {
        $('#productsTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "Show _MENU_ products per page",
                "zeroRecords": "No products found",
                "info": "Showing _START_ to _END_ of _TOTAL_ products",
                "infoEmpty": "No products available",
                "infoFiltered": "(filtered from _MAX_ total products)",
                "search": "Search:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    }
    
    // Configurar formulario de producto
    setupProductForm();
    
    // Configurar validaciones
    setupValidations();
    
    // Configurar subida de imágenes
    setupImageUpload();
});

/**
 * Configurar formulario de producto
 */
function setupProductForm() {
    // Auto-save del formulario
    $('#productForm input, #productForm textarea, #productForm select').on('change', function() {
        autoSaveProduct();
    });
    
    // Manejar envío del formulario
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        saveProduct();
    });
}

/**
 * Configurar validaciones
 */
function setupValidations() {
    // Validar precio
    $('#price').on('input', function() {
        let price = parseFloat($(this).val());
        if (price < 0) {
            $(this).val(0);
        }
    });
    
    // Validar stock
    $('#stock').on('input', function() {
        let stock = parseInt($(this).val());
        if (stock < 0) {
            $(this).val(0);
        }
    });
}

/**
 * Configurar subida de imágenes
 */
function setupImageUpload() {
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validar tipo de archivo
            if (!file.type.match('image.*')) {
                showNotification('Please select a valid image file', 'error');
                $(this).val('');
                return;
            }
            
            // Validar tamaño (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('Image size must be less than 5MB', 'error');
                $(this).val('');
                return;
            }
            
            // Mostrar preview
            showImagePreview(file);
        }
    });
}

/**
 * Mostrar preview de imagen
 */
function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        let preview = $('#imagePreview');
        if (preview.length === 0) {
            preview = $('<div id="imagePreview" class="mt-2"></div>');
            $('#image').after(preview);
        }
        
        preview.html(`
            <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeImagePreview()">Remove</button>
        `);
    };
    reader.readAsDataURL(file);
}

/**
 * Remover preview de imagen
 */
function removeImagePreview() {
    $('#imagePreview').remove();
    $('#image').val('');
}

/**
 * Auto-save del producto
 */
function autoSaveProduct() {
    const formData = new FormData($('#productForm')[0]);
    formData.append('action', 'auto_save_product');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar indicador de guardado
                showSaveIndicator();
            }
        },
        error: function() {
            // No mostrar error en auto-save para no molestar al usuario
        }
    });
}

/**
 * Guardar producto
 */
function saveProduct() {
    const formData = new FormData($('#productForm')[0]);
    formData.append('action', 'save_product');
    
    // Mostrar loading
    const submitBtn = $('#productForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                
                // Redirigir a la lista de productos si es un producto nuevo
                if (response.redirect) {
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                }
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Connection error', 'error');
        },
        complete: function() {
            // Restaurar botón
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
}

/**
 * Eliminar producto
 */
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_product',
                product_id: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    // Recargar la página o remover la fila de la tabla
                    location.reload();
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Connection error', 'error');
            }
        });
    }
}

/**
 * Toggle estado del producto
 */
function toggleProductStatus(productId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this product?`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'toggle_product_status',
                product_id: productId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    // Actualizar el botón de estado
                    location.reload();
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Connection error', 'error');
            }
        });
    }
}

/**
 * Mostrar indicador de guardado
 */
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
    
    // Auto-hide después de 3 segundos
    setTimeout(function() {
        indicator.find('.alert').alert('close');
    }, 3000);
}

/**
 * Mostrar notificación
 */
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
    
    // Auto-hide después de 5 segundos
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}

/**
 * Actualizar estado del producto
 */
function updateProductStatus(productId, status) {
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: {
            action: 'update_product_status',
            product_id: productId,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(response.message, 'success');
                location.reload();
            } else {
                showNotification('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Connection error', 'error');
        }
    });
}
