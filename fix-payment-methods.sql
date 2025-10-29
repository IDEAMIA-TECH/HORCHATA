-- Script para corregir payment_method en órdenes existentes
-- Ejecutar manualmente desde el admin o base de datos

-- Para órdenes pagadas sin método, asumir PayPal
UPDATE orders 
SET payment_method = 'paypal' 
WHERE (payment_method IS NULL OR payment_method = '') 
  AND payment_status = 'paid';

-- Para órdenes pendientes sin método, verificar manualmente
-- Si sabes que una orden específica fue Wire Transfer, actualízala así:
-- UPDATE orders SET payment_method = 'wire_transfer' WHERE id = 12;
-- UPDATE orders SET payment_method = 'wire_transfer' WHERE id = 13;

-- Ver órdenes que necesitan corrección
SELECT id, order_number, payment_method, payment_status, created_at 
FROM orders 
WHERE payment_method IS NULL OR payment_method = ''
ORDER BY id DESC;

