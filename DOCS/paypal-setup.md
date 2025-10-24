# 💳 Configuración de PayPal - Horchata Mexican Food

## 🔧 Setup de PayPal

### **1. Crear Cuenta de PayPal Developer**
1. Ir a [PayPal Developer](https://developer.paypal.com/)
2. Crear cuenta o iniciar sesión
3. Crear nueva aplicación
4. Seleccionar "Web" como plataforma
5. Obtener Client ID y Secret

### **2. Configurar Credenciales**
```php
// En config/development.php
define('PAYPAL_CLIENT_ID', 'tu_client_id_aqui');
define('PAYPAL_CLIENT_SECRET', 'tu_client_secret_aqui');
define('PAYPAL_MODE', 'sandbox'); // Cambiar a 'live' en producción
```

### **3. URLs de Retorno**
- **Return URL:** `https://tudominio.com/order-success.php`
- **Cancel URL:** `https://tudominio.com/checkout.php`

## 🧪 Modo Sandbox vs Live

### **Sandbox (Desarrollo)**
- Usar credenciales de sandbox
- Transacciones de prueba
- No se procesan pagos reales

### **Live (Producción)**
- Credenciales de producción
- Transacciones reales
- Pagos procesados

## 📋 Checklist de Implementación

- [ ] Credenciales de PayPal configuradas
- [ ] SDK de PayPal cargado
- [ ] Botones de PayPal funcionando
- [ ] Validación de transacciones
- [ ] Página de confirmación
- [ ] Manejo de errores
- [ ] Testing en sandbox
- [ ] Testing en producción

---

**¡Sistema de Checkout con PayPal Completado!** 🎉
