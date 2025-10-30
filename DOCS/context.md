Perfecto, Jorge. Entonces adaptamos el documento para stack LAMP clásico:
👉 PHP 8.x + MySQL + jQuery/AJAX + Bootstrap 5 / CSS3 (sin frameworks JS modernos).
A continuación te dejo el Markdown actualizado, optimizado para tu arquitectura habitual (MVC con rutas modulares, panel admin en /admin/, frontend público en /, API AJAX con validaciones PHP, etc.).

⸻

🍽️ Horchata Mexican Food – Rediseño Web con Sistema de Pedidos

Stack Tecnológico: PHP 8.x, MySQL 8.x, JavaScript (jQuery/AJAX), HTML5, CSS3 (Bootstrap 5).
Objetivo: Rediseñar completamente el sitio web para hacerlo moderno, funcional, bilingüe (Inglés / Español) y accesible, con pedidos pickup, reseñas verificadas, reportes y notificaciones en tiempo real.

⸻

1. Objetivos del Proyecto
	1.	Rediseñar el sitio actual horchatamexicanfood.com con una estética moderna, elegante y responsiva.
	2.	Desarrollar un sistema de pedidos pickup:
	•	Menú interactivo con fotos, precios y descripciones.
	•	Pedido con opción de pagar online o al recoger.
	3.	Implementar un panel administrador seguro:
	•	Alta de platillos, fotos, precios, categorías.
	•	Reportes de ventas, productos más vendidos, horarios pico.
	4.	Permitir reseñas verificadas solo de clientes que compraron.
	5.	Añadir notificaciones en tiempo real para pedidos nuevos.
	6.	Cumplir accesibilidad WCAG 2.1 AA (para usuarios con lector de pantalla).
	7.	Idiomas: Inglés y Español.

⸻

2. Arquitectura General

/ (Front público)
├── index.php
├── menu.php
├── checkout.php
├── order-success.php
├── reviews.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── db_connect.php
│   └── functions.php
└── assets/
    ├── css/
    ├── js/
    └── images/

 /admin (Panel de administración)
├── index.php (login)
├── dashboard.php
├── products.php
├── orders.php
├── reviews.php
├── reports.php
└── ajax/
    ├── products.ajax.php
    ├── orders.ajax.php
    ├── reviews.ajax.php
    └── notifications.ajax.php


⸻

3. Módulos y Estimaciones

🧾 3.1. Frontend Público

Submódulo	Descripción	Horas
Diseño responsivo (Bootstrap 5)	Home, Menú, Checkout, Reviews	18
Menú interactivo	Carga dinámica vía AJAX, categorías, fotos, precios	20
Checkout + validaciones	Carrito, totales, selección método pago (Stripe / pagar en tienda)	25
Confirmación pedido	Ticket resumen + correo al cliente	8
Multi-idioma (EN/ES)	Sistema de traducción en BD + switch de idioma	12
Accesibilidad base	Lectores de pantalla, contraste, aria-labels	10

Subtotal Frontend Público: 93 horas

⸻

🧠 3.2. Backend / Core PHP + BD

Submódulo	Descripción	Horas
Modelo BD (usuarios, platillos, pedidos, reseñas)	Tablas + relaciones	12
Lógica de pedidos	Insert, update status, totales, validaciones	18
Integración pagos (Stripe API PHP)	Pago online + webhook confirmación	14
Token reseñas	Generar token único tras entrega	6
Envío correo de confirmación y reseña	PHPMailer + plantilla HTML	6
Seguridad (input sanitization, CSRF tokens, sesiones seguras)	Prevención de ataques	8

Subtotal Backend Core: 64 horas

⸻

🔐 3.3. Panel Administrador

Submódulo	Descripción	Horas
Login / Roles	Sesiones + roles (Admin / Staff)	8
Dashboard general	KPIs (ventas, pedidos nuevos, etc.)	10
CRUD Categorías / Platillos	Alta, edición, fotos, precios, multi-idioma	22
Gestión de Pedidos	Estatus, impresión de tickets, filtro por fecha	18
Moderación Reseñas	Aprobar / eliminar / responder	8
Reportes	Gráficas Chart.js + exportar CSV	16
Configuración restaurante	Horarios, mensaje temporal, usuarios staff	8

Subtotal Panel Admin: 90 horas

⸻

🔔 3.4. Notificaciones en Tiempo Real

Submódulo	Descripción	Horas
AJAX polling (cada 10 seg)	Verificar pedidos nuevos	6
Popup sonido visual “Nuevo pedido”	JavaScript dinámico	6
Impresión automática ticket	Integración con impresora térmica local	10

Subtotal Notificaciones: 22 horas

⸻

⭐ 3.5. Reseñas Verificadas

Submódulo	Descripción	Horas
Token único por pedido	Ligado a email o teléfono	6
Formulario reseña	Estrellas, comentario, foto opcional	8
Validación token	Una sola vez, anti-spam	6
Moderación admin	Publicar o rechazar reseñas	4

Subtotal Reseñas: 24 horas

⸻

📊 3.6. Reportes Avanzados

Submódulo	Descripción	Horas
Ventas por rango de fecha	Gráfica y tabla resumen	8
Top platillos vendidos	Ranking dinámico	6
Horas pico de pedidos	Heatmap horario	6
Export CSV	Descarga desde admin	4

Subtotal Reportes: 24 horas

⸻

4. Roadmap por Fases

🚀 Fase 1 – Rediseño + Pedidos Pickup + Admin Básico

Duración estimada: 5 semanas
Módulos:
	•	Front público completo
	•	Checkout / pago / pickup
	•	Panel Admin (productos, pedidos)
	•	Notificaciones básicas
	•	Multi-idioma EN/ES
	•	Accesibilidad inicial

Horas:
93 (frontend) + 64 (backend) + 60 (admin parcial) + 12 (notifs básicas)
👉 Total Fase 1: ~229 h

⸻

⭐ Fase 2 – Reseñas Verificadas + Reportes + Mejoras Admin

Duración estimada: 3 semanas
Módulos:
	•	Tokens y reseñas verificadas
	•	Reportes básicos
	•	Moderación reseñas
	•	Admin avanzado (staff, horarios, mensajes)

Horas:
24 (reseñas) + 24 (reportes) + 30 (admin restante)
👉 Total Fase 2: ~78 h

⸻

⚙️ Fase 3 – Notificaciones Avanzadas + Accesibilidad Total + Clover API

Duración estimada: 2–3 semanas
Módulos:
	•	Impresión automática pedidos
	•	Accesibilidad completa (lector de pantalla, alto contraste)
	•	Integración Clover POS (opcional si lo mantienen)

Horas:
10 (notifs avanzadas) + 15 (accesibilidad avanzada) + 20 (Clover API)
👉 Total Fase 3: ~45 h

⸻

5. Resumen General

Fase	Módulos	Horas	Entregable
1	Frontend + Pedidos + Admin básico	229 h	Sitio funcional con pedidos pickup
2	Reseñas + Reportes + Admin avanzado	78 h	Sistema completo de gestión y reputación
3	Notifs avanzadas + Clover + Accesibilidad	45 h	Integración POS + ADA Compliance
Total		352 horas aprox.	


⸻

6. Próximos pasos
	1.	Confirmar si Stripe será el procesador de pagos o Clover eCommerce.
	2.	Confirmar si impresión automática será desde PC local o impresora Clover.
	3.	Confirmar si hosting actual soporta PHP 8.1+ y MySQL 8.
	4.	Definir si accesibilidad avanzada (lectores NVDA/JAWS) se hace en Fase 1 o Fase 3.
	5.	Subir wireframes / branding para diseño visual inicial.

