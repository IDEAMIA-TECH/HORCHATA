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
        
        // Auto-refresh cada 10 segundos para detectar nuevos pedidos con menor latencia
        setInterval(loadPendingNotifications, 10000);
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
    
    // Audio de alerta ya no se usa - WebAudio es más confiable
    // Mantenemos la variable para compatibilidad pero no la usamos
    const alertAudio = null;

    // WebAudio (método principal y más confiable)
    let audioCtx = null;
    function initAudioContext() {
        if (!audioCtx) {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (Ctx) {
                audioCtx = new Ctx();
            }
        }
        // Solo reanudar si está suspendido y después de interacción del usuario
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().catch(function() {
                // Ignorar error silenciosamente - el AudioContext se activará con la próxima interacción
            });
        }
        return audioCtx;
    }
    
    function playBeep(durationMs = 500, freq = 1000, gainLevel = 0.7) {
        try {
            const ctx = initAudioContext();
            if (!ctx) {
                return; // No hay soporte para WebAudio
            }
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'square';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.0001, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(gainLevel, ctx.currentTime + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + durationMs/1000);
            osc.connect(gain).connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + durationMs/1000 + 0.02);
        } catch(e) {
            // Silenciar error - puede fallar si no hay interacción del usuario
        }
    }

    function playAlarm(totalMs = 5000) {
        // Patrón de alarma: ráfagas ascendentes y descendentes en bucle
        // Asegurar que AudioContext esté inicializado
        initAudioContext();
        const start = Date.now();
        (function loop() {
            const elapsed = Date.now() - start;
            if (elapsed >= totalMs) return;
            // Patrón de alarma (dos tonos alternados)
            playBeep(250, 1200, 0.9);
            setTimeout(function(){ playBeep(250, 900, 0.9); }, 260);
            setTimeout(loop, 600);
        })();
    }

    // Guardar último conteo para detectar nuevos pedidos
    // Cargar último conteo desde localStorage para que persista entre recargas
    const lastStored = localStorage.getItem('admin_last_pending_orders');
    window.__lastPendingOrders = lastStored !== null ? parseInt(lastStored, 10) : null;
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
                // Sonido de confirmación inmediato
                playBeep(300, 1200, 0.9);
                $(this).fadeOut();
            });
        }
        // Primer interacción del usuario: desbloquear audio
        const onceHandler = function(e) {
            enableAlertsGesture();
            requestBrowserNotifications();
            
            // Limpiar listeners
            document.removeEventListener('click', onceHandler);
            document.removeEventListener('touchstart', onceHandler);
            document.removeEventListener('keydown', onceHandler);
            document.removeEventListener('mousemove', onceHandler);
        };
        // Agregar listeners para cualquier interacción del usuario
        document.addEventListener('click', onceHandler, { once: true });
        document.addEventListener('touchstart', onceHandler, { once: true });
        document.addEventListener('keydown', onceHandler, { once: true });
        document.addEventListener('mousemove', onceHandler, { once: true });

        // Mostrar botón si falta permiso o el audio aún no está habilitado
        setTimeout(function(){
            const needNotif = ("Notification" in window) && Notification.permission !== 'granted';
            if (!window.__alertsEnabled || needNotif) {
                $('#'+btnId).fadeIn();
            }
        }, 1000);

        // Botón de prueba para verificar audio/notifications
        const testId = 'testAlertsBtn';
        if ($('#' + testId).length === 0) {
            const testBtn = $(
                '<button id="'+testId+'" class="btn btn-outline-info shadow position-fixed no-print" \
                         style="bottom: 70px; right: 20px; z-index: 99999; display:none;">\
                         <i class="fas fa-volume-up me-2"></i>Probar alerta\
                 </button>'
            );
            $('body').append(testBtn);
            testBtn.on('click', function(){
                // Forzar habilitación y permisos
                enableAlertsGesture();
                requestBrowserNotifications();
                // Prueba sonido (WebAudio)
                window.__alertsEnabled = true;
                playAlarm(4000);
                // Notificación
                showNewOrderNotification(1);
            });
            // Mostrar botón de prueba siempre para facilitar verificación
            testBtn.fadeIn();
        }
    }

    function enableAlertsGesture() {
        if (window.__alertsEnabled) {
            return; // Ya está habilitado, no hacer nada
        }
        try {
            // Inicializar WebAudio (después de interacción del usuario)
            const ctx = initAudioContext();
            
            // Intentar reproducir un beep de prueba con WebAudio para "desbloquear"
            // Si funciona, significa que el audio está habilitado
            try {
                playBeep(50, 1000, 0.1);
                window.__alertsEnabled = true;
            } catch(e) {
                // Si falla, marcar como habilitado de todas formas
                // El audio funcionará en la próxima interacción
                window.__alertsEnabled = true;
            }
        } catch(e) {
            // Si hay un error, marcar como habilitado de todas formas
            window.__alertsEnabled = true;
        }
    }

    function requestBrowserNotifications() {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    // (OneSignal removido) – mantenemos solo sonido y notificación inline

    function updateNotificationBadges(data) {
        if (data.pending_orders !== undefined) {
            const prev = window.__lastPendingOrders;
            const curr = parseInt(data.pending_orders, 10) || 0;
            $('#pendingOrdersBadge').text(curr);
            // Si hay incremento, reproducir sonido y notificación
            if (prev !== null && curr > prev) {
                // Intentar habilitar alertas automáticamente si no están habilitadas
                // (Aunque el sonido funcionará de todas formas con WebAudio)
                if (!window.__alertsEnabled) {
                    enableAlertsGesture();
                }
                
                // Reproducir sonido (usar WebAudio que es más confiable)
                // Asegurar que AudioContext esté inicializado y activo
                try {
                    const ctx = initAudioContext();
                    if (ctx && ctx.state === 'suspended') {
                        ctx.resume().catch(function() {
                            // Ignorar error silenciosamente - se activará con la próxima interacción
                        });
                    }
                    playAlarm(6000);
                } catch(e) {
                    // Silenciar error - WebAudio puede fallar si no hay interacción previa
                }
                
                // Vibración como fallback en móviles
                if (navigator.vibrate) { 
                    try { 
                        navigator.vibrate([200, 100, 200]); 
                    } catch(e) {
                        // Silenciar error
                    }
                }
                
                // Pasar el ID de la orden más reciente y el incremento (manejar null/undefined)
                const orderId = data.latest_order_id || null;
                const orderNumber = data.latest_order_number || null;
                showNewOrderNotification(curr - prev, orderId, orderNumber);
            }
            window.__lastPendingOrders = curr;
            try { localStorage.setItem('admin_last_pending_orders', String(curr)); } catch(e) {}
        }
        if (data.pending_reviews !== undefined) {
            $('#pendingReviewsBadge').text(data.pending_reviews);
        }
        if (data.new_messages !== undefined) {
            $('#newMessagesBadge').text(data.new_messages);
        }
    }

    function showNewOrderNotification(increment, orderId, orderNumber) {
        try {
            const count = increment || 1;
            const title = count > 1 ? `${count} nuevos pedidos` : 'Nuevo pedido recibido';
            const orderInfo = (orderNumber && orderNumber !== 'null' && orderNumber !== null) ? ` (Pedido #${orderNumber})` : '';
            const options = {
                body: 'Abre el panel para revisar y confirmar el pedido.',
                icon: '../assets/images/LOGO.JPG',
                badge: '../assets/images/LOGO.JPG',
            };
            
            // Intentar Notification API (si está permitido)
            if ("Notification" in window && Notification.permission === 'granted') {
                try {
                    const n = new Notification(title, options);
                    // Mantener notificación más tiempo (30 segundos) pero aún con auto-cierre
                    setTimeout(() => n.close && n.close(), 30000);
                } catch(e) { 
                    // Silenciar error - las notificaciones pueden no estar disponibles
                }
            }
            
            // Mostrar SIEMPRE un aviso visual dentro del panel (compatible con iOS/Android)
            const id = 'inlineOrderNotice_' + Date.now();
            
            // Crear el contenido del botón/link si hay orderId (validar que sea válido)
            let actionButton = '';
            if (orderId && orderId !== null && orderId !== 'null' && orderId !== undefined && !isNaN(orderId)) {
                actionButton = `
                    <div class="mt-2">
                        <a href="orders.php?action=view&id=${orderId}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>Ver Orden
                        </a>
                    </div>
                `;
            }
            
            const notification = $(
                '<div id="'+id+'" class="alert alert-info alert-dismissible fade show position-fixed" \
                     style="top: 20px; right: 20px; z-index: 99999; min-width: 350px; max-width: 450px; box-shadow: 0 8px 20px rgba(0,0,0,0.15);" role="alert">' +
                    '<div class="d-flex align-items-start">' +
                        '<div class="flex-grow-1">' +
                            '<i class="fas fa-bell me-2"></i><strong>' + title + '</strong>' + orderInfo +
                            '<p class="mb-0 mt-2">Abre el panel para revisar y confirmar el pedido.</p>' +
                            actionButton +
                        '</div>' +
                        '<button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>' +
                '</div>'
            );
            $('body').append(notification);
            // NO cerrar automáticamente - solo cuando el usuario lo cierre manualmente
            // setTimeout removido - la notificación permanecerá hasta que se cierre manualmente
        } catch(e) { 
            // Silenciar error - solo loguear en caso de error crítico
            console.error('Error en showNewOrderNotification:', e);
        }
    }
    </script>
</body>
</html>
