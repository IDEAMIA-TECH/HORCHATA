# 🍽️ Horchata Mexican Food - Sistema de Pedidos Web

Sistema web completo para gestión de pedidos pickup, reseñas verificadas y administración de restaurante.

## 🚀 Instalación del Sistema

### **Opción 1: Instalación Automática (Recomendada)**

1. **Subir archivos** al servidor
2. **Verificar conexión:** `http://tudominio.com/check-database.php`
3. **Crear tablas:** `http://tudominio.com/create-tables.php`
4. **Instalar menú:** `http://tudominio.com/install-menu.php`
5. **Configurar PayPal** en `config/development.php`
6. **¡Listo para recibir pedidos!**

### **Opción 2: Instalación Manual**

1. **Configurar base de datos:**
   ```bash
   mysql -h 173.231.22.109 -u ideamiadev_horchata -p ideamiadev_horchata < database/schema.sql
   mysql -h 173.231.22.109 -u ideamiadev_horchata -p ideamiadev_horchata < database/menu-data.sql
   ```

2. **Configurar credenciales** en `config/development.php`

3. **Configurar PayPal** para pagos

### **Credenciales de Acceso**

```php
Host: 173.231.22.109
Database: ideamiadev_horchata
Username: ideamiadev_horchata
Password: DfabGqB&gX3xM?ea

// Panel Administrativo
Usuario: admin@horchatamexicanfood.com
Contraseña: password
```

## 📋 Características del Sistema

### ✅ **Sistema Completo Implementado**
- [x] **Frontend público** con diseño moderno
- [x] **Sistema de pedidos** con PayPal integration
- [x] **Panel administrativo** completo
- [x] **Base de datos** con menú completo (150+ productos)
- [x] **Sistema de reseñas** verificadas
- [x] **Notificaciones** en tiempo real
- [x] **Multi-idioma** (ES/EN)
- [x] **Responsive design** para móviles
- [x] **Seguridad** implementada

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.x
- **Base de Datos:** MySQL 8.x
- **Frontend:** HTML5, CSS3, Bootstrap 5, jQuery
- **Pagos:** Stripe API
- **Email:** PHPMailer
- **Gráficas:** Chart.js

## 📊 Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `categories` - Categorías de productos
- `products` - Productos del menú
- `orders` - Pedidos de clientes
- `order_items` - Items de cada pedido
- `reviews` - Reseñas de clientes
- `review_tokens` - Tokens para reseñas verificadas
- `settings` - Configuración del sistema
- `notifications` - Notificaciones del sistema

## 🔐 Seguridad

- Validación y sanitización de inputs
- Tokens CSRF en formularios
- Sesiones seguras
- Rate limiting en endpoints
- Encriptación de contraseñas

## 🌐 Multi-idioma

- Soporte para Inglés y Español
- Traducciones almacenadas en BD
- Switch de idioma dinámico

## ♿ Accesibilidad

- Cumplimiento WCAG 2.1 AA
- Soporte para lectores de pantalla
- Navegación por teclado
- Alto contraste disponible

## 📞 Soporte

Para dudas o problemas, contactar al desarrollador principal.

---

**Desarrollado para Horchata Mexican Food**  
*Sistema de Pedidos Web - 2024*
