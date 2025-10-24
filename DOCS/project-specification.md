# 🍽️ Horchata Mexican Food - Sistema de Pedidos Web

## 📋 Resumen del Proyecto

**Objetivo:** Rediseñar completamente el sitio web horchatamexicanfood.com para crear una plataforma moderna, funcional, bilingüe y accesible con sistema de pedidos pickup, reseñas verificadas, reportes y notificaciones en tiempo real.

**Stack Tecnológico:** PHP 8.x, MySQL 8.x, JavaScript (jQuery/AJAX), HTML5, CSS3 (Bootstrap 5)

---

## 🎯 Objetivos Principales

### 1. Rediseño Web Moderno
- Estética moderna, elegante y completamente responsiva
- Diseño bilingüe (Inglés/Español) con switch de idioma
- Cumplimiento de accesibilidad WCAG 2.1 AA
- Optimización para lectores de pantalla

### 2. Sistema de Pedidos Pickup
- Menú interactivo con fotos, precios y descripciones
- Carrito de compras dinámico
- Opciones de pago: online (Stripe) o al recoger
- Confirmación de pedidos con tickets y correos

### 3. Panel Administrador
- Gestión completa de productos (CRUD)
- Reportes de ventas y análisis
- Gestión de pedidos en tiempo real
- Moderación de reseñas

### 4. Reseñas Verificadas
- Sistema de tokens únicos por pedido
- Reseñas solo de clientes verificados
- Moderación administrativa

### 5. Notificaciones en Tiempo Real
- Alertas de pedidos nuevos
- Impresión automática de tickets
- Dashboard con KPIs en vivo

---

## 🏗️ Arquitectura del Sistema

### Estructura de Directorios

```
/ (Frontend Público)
├── index.php                 # Página principal
├── menu.php                 # Menú interactivo
├── checkout.php             # Proceso de compra
├── order-success.php        # Confirmación de pedido
├── reviews.php              # Reseñas de clientes
├── includes/
│   ├── header.php           # Header común
│   ├── footer.php           # Footer común
│   ├── db_connect.php       # Conexión a BD
│   └── functions.php        # Funciones auxiliares
└── assets/
    ├── css/                 # Estilos CSS
    ├── js/                  # JavaScript
    └── images/              # Imágenes del sitio

/admin (Panel Administración)
├── index.php                # Login administrativo
├── dashboard.php               # Dashboard principal
├── products.php             # Gestión de productos
├── orders.php               # Gestión de pedidos
├── reviews.php              # Moderación de reseñas
├── reports.php              # Reportes y analytics
└── ajax/                    # Endpoints AJAX
    ├── products.ajax.php
    ├── orders.ajax.php
    ├── reviews.ajax.php
    └── notifications.ajax.php
```

### Base de Datos

#### Tablas Principales
- `users` - Usuarios del sistema (admin/staff)
- `categories` - Categorías de productos
- `products` - Productos del menú
- `orders` - Pedidos de clientes
- `order_items` - Items de cada pedido
- `reviews` - Reseñas de clientes
- `review_tokens` - Tokens para reseñas verificadas
- `settings` - Configuración del restaurante

---

## 📊 Planificación por Módulos

### 🧾 Frontend Público (93 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| Diseño responsivo | Home, Menú, Checkout, Reviews con Bootstrap 5 | 18 |
| Menú interactivo | Carga dinámica AJAX, categorías, fotos, precios | 20 |
| Checkout + validaciones | Carrito, totales, métodos de pago | 25 |
| Confirmación pedido | Ticket resumen + correo al cliente | 8 |
| Multi-idioma | Sistema de traducción BD + switch idioma | 12 |
| Accesibilidad base | Lectores de pantalla, contraste, aria-labels | 10 |

### 🧠 Backend Core (64 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| Modelo BD | Tablas + relaciones + índices | 12 |
| Lógica de pedidos | Insert, update status, totales, validaciones | 18 |
| Integración pagos | Stripe API PHP + webhooks | 14 |
| Token reseñas | Generar token único tras entrega | 6 |
| Envío correo | PHPMailer + plantillas HTML | 6 |
| Seguridad | Input sanitization, CSRF, sesiones seguras | 8 |

### 🔐 Panel Administrador (90 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| Login / Roles | Autenticación + roles (Admin/Staff) | 8 |
| Dashboard | KPIs, ventas, pedidos nuevos | 10 |
| CRUD Productos | Alta, edición, fotos, precios, multi-idioma | 22 |
| Gestión Pedidos | Estatus, impresión tickets, filtros | 18 |
| Moderación Reseñas | Aprobar/eliminar/responder | 8 |
| Reportes | Gráficas Chart.js + exportar CSV | 16 |
| Configuración | Horarios, mensajes, usuarios staff | 8 |

### 🔔 Notificaciones Tiempo Real (22 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| AJAX polling | Verificar pedidos nuevos cada 10 seg | 6 |
| Popup notificaciones | Sonido + visual "Nuevo pedido" | 6 |
| Impresión automática | Integración impresora térmica | 10 |

### ⭐ Reseñas Verificadas (24 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| Token único | Ligado a email/teléfono del pedido | 6 |
| Formulario reseña | Estrellas, comentario, foto opcional | 8 |
| Validación token | Una sola vez, anti-spam | 6 |
| Moderación admin | Publicar o rechazar reseñas | 4 |

### 📊 Reportes Avanzados (24 horas)

| Módulo | Descripción | Horas |
|--------|-------------|-------|
| Ventas por fecha | Gráfica y tabla resumen | 8 |
| Top productos | Ranking dinámico | 6 |
| Horas pico | Heatmap horario | 6 |
| Export CSV | Descarga desde admin | 4 |

---

## 🚀 Roadmap de Desarrollo

### Fase 1: Core System (5 semanas - 229 horas)
**Entregable:** Sitio funcional con pedidos pickup

- ✅ Frontend público completo
- ✅ Sistema de checkout y pagos
- ✅ Panel admin básico (productos, pedidos)
- ✅ Notificaciones básicas
- ✅ Multi-idioma EN/ES
- ✅ Accesibilidad inicial

### Fase 2: Reputation & Analytics (3 semanas - 78 horas)
**Entregable:** Sistema completo de gestión y reputación

- ✅ Sistema de reseñas verificadas
- ✅ Reportes básicos y analytics
- ✅ Moderación de reseñas
- ✅ Admin avanzado (staff, horarios, mensajes)

### Fase 3: Advanced Features (2-3 semanas - 45 horas)
**Entregable:** Integración POS + ADA Compliance

- ✅ Notificaciones avanzadas
- ✅ Impresión automática
- ✅ Accesibilidad completa (NVDA/JAWS)
- ✅ Integración Clover POS (opcional)

---

## 📈 Resumen de Horas

| Fase | Módulos | Horas | Entregable |
|------|---------|-------|------------|
| 1 | Frontend + Pedidos + Admin básico | 229h | Sitio funcional con pedidos pickup |
| 2 | Reseñas + Reportes + Admin avanzado | 78h | Sistema completo de gestión y reputación |
| 3 | Notifs avanzadas + Clover + Accesibilidad | 45h | Integración POS + ADA Compliance |
| **Total** | | **352h** | **Sistema completo** |

---

## 🔧 Especificaciones Técnicas

### Requisitos del Servidor
- PHP 8.1+ con extensiones: PDO, MySQLi, GD, cURL
- MySQL 8.0+ con soporte para JSON
- Apache/Nginx con mod_rewrite
- SSL/HTTPS obligatorio
- Espacio mínimo: 2GB

### Configuración de Base de Datos
```php
// Configuración de conexión
DB_HOST: 173.231.22.109
DB_NAME: ideamiadev_horchata
DB_USER: ideamiadev_horchata
DB_PASS: DfabGqB&gX3xM?ea
```

### Dependencias PHP
- PHPMailer 6.x (envío de correos)
- Stripe PHP SDK (procesamiento de pagos)
- Chart.js (gráficas en admin)

### Seguridad
- Validación y sanitización de inputs
- Tokens CSRF en formularios
- Sesiones seguras con regeneración
- Rate limiting en endpoints AJAX
- Encriptación de datos sensibles

### Accesibilidad
- Cumplimiento WCAG 2.1 AA
- Soporte para lectores de pantalla
- Navegación por teclado
- Alto contraste disponible
- Texto alternativo en imágenes

---

## 📋 Próximos Pasos

### Decisiones Pendientes
1. **Procesador de pagos:** Confirmar Stripe vs Clover eCommerce
2. **Impresión:** PC local vs impresora Clover
3. **Hosting:** Verificar soporte PHP 8.1+ y MySQL 8
4. **Accesibilidad:** Definir si avanzada va en Fase 1 o 3
5. **Diseño:** Recibir wireframes y branding

### Setup Inicial
1. Configurar entorno de desarrollo
2. Crear estructura de base de datos
3. Implementar autenticación básica
4. Configurar sistema de rutas
5. Setup de assets (CSS/JS)

### Criterios de Aceptación
- [ ] Sitio completamente responsivo
- [ ] Proceso de pedido funcional end-to-end
- [ ] Panel admin con CRUD completo
- [ ] Sistema de reseñas verificadas
- [ ] Reportes básicos funcionando
- [ ] Notificaciones en tiempo real
- [ ] Multi-idioma implementado
- [ ] Accesibilidad WCAG 2.1 AA

---

## 📞 Contacto y Soporte

**Desarrollador Principal:** Jorge
**Stack:** LAMP (PHP 8.x + MySQL + jQuery/AJAX + Bootstrap 5)
**Metodología:** Desarrollo por fases con entregables incrementales

---

*Documento creado para el desarrollo del sistema de pedidos de Horchata Mexican Food*
*Última actualización: [Fecha actual]*
