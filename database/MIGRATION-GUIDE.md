# 🔄 Guía de Migración: Desarrollo → Producción

## 📋 Pasos para Migrar la Base de Datos

### ⚠️ IMPORTANTE: Hacer backup ANTES de migrar

### Paso 1: Backup de Producción
```bash
mysqldump -h [HOST_PRODUCTION] -u [USER] -p [DATABASE_NAME] > backup-production-$(date +%Y%m%d-%H%M%S).sql
```

### Paso 2: Exportar Base de Datos de Desarrollo
```bash
mysqldump -h 173.231.22.109 -u ideamiadev_horchata -p ideamiadev_horchata > export-dev-to-prod.sql
```

### Paso 3: Importar a Producción
```bash
mysql -h [HOST_PRODUCTION] -u [USER] -p [DATABASE_NAME] < export-dev-to-prod.sql
```

### Paso 4: Actualizar Configuraciones
```bash
mysql -h [HOST_PRODUCTION] -u [USER] -p [DATABASE_NAME] < database/prepare-production-migration.sql
```

## ✅ Checklist Pre-Migración

- [ ] Backup de producción realizado
- [ ] Backup guardado en lugar seguro
- [ ] Datos de prueba identificados y documentados
- [ ] Credenciales de producción verificadas

## ✅ Checklist Post-Migración

- [ ] URLs actualizadas en tabla `settings`
- [ ] Datos de prueba eliminados (si aplica)
- [ ] Usuarios de prueba eliminados (si aplica)
- [ ] Órdenes de prueba eliminadas (si aplica)
- [ ] Configuración de PayPal actualizada
- [ ] Sistema funcionando correctamente en producción
- [ ] SSL/HTTPS funcionando
- [ ] Dominio apuntando correctamente

## 🔧 Configuraciones a Actualizar

### En la Base de Datos:
- `settings.site_url` → `https://horchatamexfood.com`
- `settings.restaurant_website` → `https://horchatamexfood.com`

### En PayPal Developer:
- Return URL: `https://horchatamexfood.com/order-success.php`
- Cancel URL: `https://horchatamexfood.com/checkout.php`

### En el Servidor:
- SSL Certificate instalado
- Archivos subidos con permisos correctos
- Base de datos accesible

## ⚠️ Advertencias

1. **No migrar usuarios de prueba**: Eliminar antes o después de migrar
2. **Órdenes de prueba**: Considerar eliminarlas si no son reales
3. **Configuraciones sensibles**: Verificar que no expongan información de desarrollo
4. **Backup primero**: SIEMPRE hacer backup antes de migrar

