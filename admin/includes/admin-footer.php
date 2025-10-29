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
    
    function updateNotificationBadges(data) {
        if (data.pending_orders !== undefined) {
            $('#pendingOrdersBadge').text(data.pending_orders);
        }
        if (data.pending_reviews !== undefined) {
            $('#pendingReviewsBadge').text(data.pending_reviews);
        }
        if (data.new_messages !== undefined) {
            $('#newMessagesBadge').text(data.new_messages);
        }
    }
    </script>
</body>
</html>
