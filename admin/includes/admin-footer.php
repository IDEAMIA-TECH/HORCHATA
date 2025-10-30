    </div>
    
    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> Horchata Mexican Food. Panel Administrativo.
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0">
                        Usuario: <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>
                        | Rol: <strong><?php echo ucfirst($_SESSION['admin_role']); ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Admin JS -->
    <script src="../assets/js/admin.js"></script>
    
    <!-- Page specific scripts -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Auto-refresh notifications -->
    <script>
    $(document).ready(function() {
        // Cargar notificaciones pendientes
        loadPendingNotifications();
        
        // Auto-refresh cada 30 segundos
        setInterval(loadPendingNotifications, 30000);
        // Preparar permisos de notificación/sonido
        initAlertPermissionsUI();
    });
    
    function loadPendingNotifications() {
        $.ajax({
            url: '../ajax/admin.ajax.php',
            method: 'GET',
            data: { action: 'get_notifications' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateNotificationBadges(response.data);
                }
            }
        });
    }
    
    // Audio de alerta (beep corto, inline base64)
    const alertAudio = (function(){
        const audio = new Audio('data:audio/mp3;base64,//uQZAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAACcQCA//////////////////////////////////////////////8AAAADTEFNRTMuMTAwA8MAAAAAAAAAAAAAABQAAAAAAAAAAAAAAAAAAAAAAAD///////////////////////////////8AAAA=');
        audio.preload = 'auto';
        audio.volume = 0.8;
        return audio;
    })();

    // Guardar último conteo para detectar nuevos pedidos
    window.__lastPendingOrders = typeof window.__lastPendingOrders === 'number' ? window.__lastPendingOrders : null;
    window.__alertsEnabled = false;

    function initAlertPermissionsUI() {
        // Botón flotante para habilitar sonido/notificaciones si están bloqueadas
        const btnId = 'enableAlertsBtn';
        if ($('#' + btnId).length === 0) {
            const btn = $(
                '<button id="'+btnId+'" class="btn btn-warning shadow position-fixed no-print" \
                         style="bottom: 20px; right: 20px; z-index: 99999; display:none;">\
                         <i class="fas fa-bell me-2"></i>Habilitar alertas\
                 </button>'
            );
            $('body').append(btn);
            btn.on('click', function(){
                enableAlertsGesture();
                requestBrowserNotifications();
                $(this).fadeOut();
            });
        }
        // Primer interacción del usuario: desbloquear audio
        const onceHandler = function() {
            enableAlertsGesture();
            requestBrowserNotifications();
            document.removeEventListener('click', onceHandler);
            document.removeEventListener('touchstart', onceHandler);
            document.removeEventListener('keydown', onceHandler);
        };
        document.addEventListener('click', onceHandler, { once: true });
        document.addEventListener('touchstart', onceHandler, { once: true });
        document.addEventListener('keydown', onceHandler, { once: true });

        // Mostrar botón si falta permiso o el audio aún no está habilitado
        setTimeout(function(){
            const needNotif = ("Notification" in window) && Notification.permission !== 'granted';
            if (!window.__alertsEnabled || needNotif) {
                $('#'+btnId).fadeIn();
            }
        }, 1000);
    }

    function enableAlertsGesture() {
        if (window.__alertsEnabled) return;
        try {
            // Intentar reproducir en silencio para desbloquear por gesto del usuario
            const prevVol = alertAudio.volume; 
            alertAudio.volume = 0.0;
            const p = alertAudio.play();
            if (p && typeof p.then === 'function') {
                p.then(function(){
                    alertAudio.pause();
                    alertAudio.currentTime = 0;
                    alertAudio.volume = prevVol;
                    window.__alertsEnabled = true;
                }).catch(function(){
                    // Si falla, mostrar botón para reintentar
                    $('#enableAlertsBtn').fadeIn();
                });
            } else {
                window.__alertsEnabled = true;
            }
        } catch(e) {
            $('#enableAlertsBtn').fadeIn();
        }
    }

    function requestBrowserNotifications() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    function updateNotificationBadges(data) {
        if (data.pending_orders !== undefined) {
            const prev = window.__lastPendingOrders;
            const curr = parseInt(data.pending_orders, 10) || 0;
            $('#pendingOrdersBadge').text(curr);
            // Si hay incremento, reproducir sonido y notificación
            if (prev !== null && curr > prev) {
                // Reproducir solo si alertsEnabled
                if (window.__alertsEnabled) {
                    try { alertAudio.currentTime = 0; alertAudio.play(); } catch(e) {}
                } else {
                    // Vibración como fallback en móviles
                    if (navigator.vibrate) { try { navigator.vibrate(200); } catch(e) {} }
                }
                showNewOrderNotification(curr - prev);
            }
            window.__lastPendingOrders = curr;
        }
        if (data.pending_reviews !== undefined) {
            $('#pendingReviewsBadge').text(data.pending_reviews);
        }
        if (data.new_messages !== undefined) {
            $('#newMessagesBadge').text(data.new_messages);
        }
    }

    function showNewOrderNotification(increment) {
        const count = increment || 1;
        const title = count > 1 ? `${count} nuevos pedidos` : 'Nuevo pedido recibido';
        const options = {
            body: 'Abre el panel para revisar y confirmar el pedido.',
            icon: '../assets/images/LOGO.JPG',
            badge: '../assets/images/LOGO.JPG',
        };
        if ("Notification" in window && Notification.permission === 'granted') {
            try {
                const n = new Notification(title, options);
                setTimeout(() => n.close && n.close(), 5000);
            } catch(e) {}
        } else {
            // Fallback visual
            try {
                const notification = $(
                    '<div class="alert alert-info alert-dismissible fade show position-fixed" \
                         style="top: 20px; right: 20px; z-index: 99999; min-width: 280px;" role="alert">' +
                        '<i class="fas fa-bell me-2"></i>' + title +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>'
                );
                $('body').append(notification);
                setTimeout(function(){ notification.alert('close'); }, 5000);
            } catch(e) {}
        }
    }
    </script>
</body>
</html>
