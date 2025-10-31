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
    
    // Audio de alerta (beep corto, inline base64)
    const alertAudio = (function(){
        const audio = new Audio('data:audio/mp3;base64,//uQZAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAACcQCA//////////////////////////////////////////////8AAAADTEFNRTMuMTAwA8MAAAAAAAAAAAAAABQAAAAAAAAAAAAAAAAAAAAAAAD///////////////////////////////8AAAA=');
        audio.preload = 'auto';
        audio.volume = 1.0;
        return audio;
    })();

    // WebAudio fallback (más confiable para disparar desde gestos del usuario)
    let audioCtx = null;
    function playBeep(durationMs = 500, freq = 1000, gainLevel = 0.7) {
        try {
            if (!audioCtx) {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                audioCtx = Ctx ? new Ctx() : null;
            }
            if (audioCtx && audioCtx.state === 'suspended') { audioCtx.resume(); }
            if (!audioCtx) {
                // Fallback a elemento <audio>
                alertAudio.currentTime = 0; alertAudio.play();
                return;
            }
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'square';
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.0001, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(gainLevel, audioCtx.currentTime + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + durationMs/1000);
            osc.connect(gain).connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + durationMs/1000 + 0.02);
        } catch(e) {}
    }

    function playAlarm(totalMs = 5000) {
        // Patrón de alarma: ráfagas ascendentes y descendentes en bucle
        try {
            if (!audioCtx) {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                audioCtx = Ctx ? new Ctx() : null;
            }
            if (audioCtx && audioCtx.state === 'suspended') { audioCtx.resume(); }
        } catch(e) {}
        const start = Date.now();
        (function loop() {
            const elapsed = Date.now() - start;
            if (elapsed >= totalMs) return;
            // sweep up
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

    // (OneSignal removido) – mantenemos solo sonido y notificación inline

    function updateNotificationBadges(data) {
        if (data.pending_orders !== undefined) {
            const prev = window.__lastPendingOrders;
            const curr = parseInt(data.pending_orders, 10) || 0;
            $('#pendingOrdersBadge').text(curr);
            // Si hay incremento, reproducir sonido y notificación
            if (prev !== null && curr > prev) {
                console.log('🔔 Nuevo pedido detectado! Incremento:', curr - prev);
                
                // Reproducir sonido PRIMERO (antes de la notificación)
                if (window.__alertsEnabled) {
                    console.log('🔊 Reproduciendo alarma...');
                    // Patrón de alarma más largo e intenso
                    playAlarm(6000);
                    try { 
                        alertAudio.currentTime = 0; 
                        alertAudio.play().catch(function(err) {
                            console.error('Error reproduciendo audio:', err);
                        });
                    } catch(e) { 
                        console.error('Error en audio:', e);
                    }
                } else {
                    console.log('⚠️ Alertas no habilitadas, usando vibración');
                    // Vibración como fallback en móviles
                    if (navigator.vibrate) { try { navigator.vibrate(200); } catch(e) {} }
                }
                
                // Pasar el ID de la orden más reciente y el incremento (manejar null/undefined)
                const orderId = data.latest_order_id || null;
                const orderNumber = data.latest_order_number || null;
                console.log('📋 Mostrando notificación. Order ID:', orderId, 'Order Number:', orderNumber);
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
            console.log('📬 showNewOrderNotification llamada con:', { increment, orderId, orderNumber });
            
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
                    console.error('Error en Notification API:', e); 
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
            console.log('✅ Notificación visual agregada correctamente');
            // NO cerrar automáticamente - solo cuando el usuario lo cierre manualmente
            // setTimeout removido - la notificación permanecerá hasta que se cierre manualmente
        } catch(e) { 
            console.error('❌ Error en showNewOrderNotification:', e);
        }
    }
    </script>
</body>
</html>
