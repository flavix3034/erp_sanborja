-- Rediseño Módulo de Gastos
-- Agregar campos nuevos a tec_compras para seguimiento de pago y comprobante

ALTER TABLE tec_compras
  ADD COLUMN estado_pago ENUM('PAGADO','PENDIENTE') NOT NULL DEFAULT 'PAGADO' AFTER total,
  ADD COLUMN fecha_vencimiento DATE DEFAULT NULL AFTER estado_pago,
  ADD COLUMN comprobante_archivo VARCHAR(255) DEFAULT NULL AFTER fecha_vencimiento;
