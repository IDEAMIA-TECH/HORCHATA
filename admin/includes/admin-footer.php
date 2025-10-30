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
        // Solicitar permiso de notificaciones si no se tiene
        if ("Notification" in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
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

    function updateNotificationBadges(data) {
        if (data.pending_orders !== undefined) {
            const prev = window.__lastPendingOrders;
            const curr = parseInt(data.pending_orders, 10) || 0;
            $('#pendingOrdersBadge').text(curr);
            // Si hay incremento, reproducir sonido y notificación
            if (prev !== null && curr > prev) {
                try { alertAudio.currentTime = 0; alertAudio.play(); } catch(e) {}
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
