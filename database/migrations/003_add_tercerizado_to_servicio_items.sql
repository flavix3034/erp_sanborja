-- Migracion: Agregar campos de tercerizacion a tec_servicio_items
-- Fecha: 2026-03-26
-- Descripcion: Permite registrar cuando un item de servicio es tercerizado,
--              incluyendo proveedor y costo del proveedor para calcular margen.

ALTER TABLE tec_servicio_items
  ADD COLUMN es_tercerizado TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=No, 1=Si' AFTER observaciones,
  ADD COLUMN proveedor_id INT NULL DEFAULT NULL COMMENT 'FK opcional a tec_proveedores' AFTER es_tercerizado,
  ADD COLUMN proveedor_nombre VARCHAR(200) NULL DEFAULT NULL COMMENT 'Nombre libre del proveedor' AFTER proveedor_id,
  ADD COLUMN costo_proveedor DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Costo unitario que cobra el proveedor externo' AFTER proveedor_nombre,
  ADD COLUMN tipo_doc_proveedor TINYINT(1) NULL DEFAULT NULL COMMENT '5=Ticket (sin IGV), 1=Factura (con IGV)' AFTER costo_proveedor;
