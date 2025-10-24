/**
 * Admin Panel JavaScript
 * Horchata Mexican Food - Panel Administrativo
 */

$(document).ready(function() {
    // Inicializar funcionalidades del admin
    initAdminPanel();
});

function initAdminPanel() {
    // Configurar DataTables
    setupDataTables();
    
    // Configurar notificaciones
    setupNotifications();
    
    // Configurar sidebar
    setupSidebar();
    
    // Configurar confirmaciones
    setupConfirmations();
    
    // Configurar auto-save
    setupAutoSave();
}

/**
 * Configurar DataTables
 */
function setupDataTables() {
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }
}

/**
 * Configurar notificaciones
 */
function setupNotifications() {
    // Mostrar notificaciones de éxito/error
    if (typeof window.adminNotifications !== 'undefined') {
        window.adminNotifications.forEach(notification => {
            showNotification(notification.message, notification.type);
        });
    }
}

/**
 * Configurar sidebar
 */
function setupSidebar() {
    // Toggle sidebar en mobile
    $('.navbar-toggler').on('click', function() {
        $('.sidebar').toggleClass('show');
    });
    
    // Cerrar sidebar al hacer clic fuera en mobile
    $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.sidebar, .navbar-toggler').length) {
                $('.sidebar').removeClass('show');
            }
        }
    });
}

/**
 * Configurar confirmaciones
 */
function setupConfirmations() {
    // Confirmar eliminación
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        
        const itemName = $(this).data('item-name') || 'este elemento';
        const confirmMessage = `¿Estás seguro de que quieres eliminar ${itemName}? Esta acción no se puede deshacer.`;
        
        if (confirm(confirmMessage)) {
            const deleteUrl = $(this).attr('href');
            if (deleteUrl) {
                window.location.href = deleteUrl;
            }
        }
    });
    
    // Confirmar cambios de estado
    $('.btn-status').on('click', function(e) {
        e.preventDefault();
        
        const newStatus = $(this).data('status');
        const itemName = $(this).data('item-name') || 'este elemento';
        const confirmMessage = `¿Cambiar el estado de ${itemName} a ${newStatus}?`;
        
        if (confirm(confirmMessage)) {
            const statusUrl = $(this).attr('href');
            if (statusUrl) {
                window.location.href = statusUrl;
            }
        }
    });
}

/**
 * Configurar auto-save
 */
function setupAutoSave() {
    // Auto-save para formularios largos
    $('.auto-save').on('input change', function() {
        const form = $(this).closest('form');
        const formId = form.attr('id');
        
        if (formId) {
            clearTimeout(window.autoSaveTimeout);
            window.autoSaveTimeout = setTimeout(() => {
                saveFormData(formId);
            }, 2000);
        }
    });
}

/**
 * Guardar datos del formulario
 */
function saveFormData(formId) {
    const form = $(`#${formId}`);
    const formData = form.serialize();
    
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: {
            action: 'auto_save',
            form_id: formId,
            form_data: formData
        },
        success: function(response) {
            if (response.success) {
                showNotification('Cambios guardados automáticamente', 'success');
            }
        }
    });
}

/**
 * Mostrar notificación
 */
function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 70px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}

/**
 * Actualizar estado de elemento
 */
function updateStatus(elementId, newStatus, itemType = 'elemento') {
    $.ajax({
        url: '../ajax/admin.ajax.php',
        method: 'POST',
        data: {
            action: 'update_status',
            element_id: elementId,
            new_status: newStatus,
            item_type: itemType
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification(`Estado actualizado a ${newStatus}`, 'success');
                location.reload();
            } else {
                showNotification('Error al actualizar estado: ' + response.message, 'error');
            }
        },
        error: function() {
            showNotification('Error de conexión', 'error');
        }
    });
}

/**
 * Eliminar elemento
 */
function deleteElement(elementId, itemType = 'elemento') {
    if (confirm(`¿Estás seguro de que quieres eliminar este ${itemType}?`)) {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: {
                action: 'delete_element',
                element_id: elementId,
                item_type: itemType
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(`${itemType} eliminado exitosamente`, 'success');
                    location.reload();
                } else {
                    showNotification('Error al eliminar: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error de conexión', 'error');
            }
        });
    }
}

/**
 * Subir imagen
 */
function uploadImage(input, previewId) {
    const file = input.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_image');
        
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $(`#${previewId}`).attr('src', response.image_url);
                    showNotification('Imagen subida exitosamente', 'success');
                } else {
                    showNotification('Error al subir imagen: ' + response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error de conexión', 'error');
            }
        });
    }
}

/**
 * Exportar datos
 */
function exportData(format, table = 'all') {
    window.location.href = `../ajax/admin.ajax.php?action=export&format=${format}&table=${table}`;
}

/**
 * Imprimir página
 */
function printPage() {
    window.print();
}

/**
 * Refrescar dashboard
 */
function refreshDashboard() {
    location.reload();
}

/**
 * Configurar tooltips
 */
function setupTooltips() {
    $('[data-bs-toggle="tooltip"]').tooltip();
}

/**
 * Configurar popovers
 */
function setupPopovers() {
    $('[data-bs-toggle="popover"]').popover();
}

// Inicializar tooltips y popovers
$(document).ready(function() {
    setupTooltips();
    setupPopovers();
});

// Exportar funciones para uso global
window.AdminPanel = {
    showNotification,
    updateStatus,
    deleteElement,
    uploadImage,
    exportData,
    printPage,
    refreshDashboard
};
