-- ================================================================
-- Migracion 001: Eliminar columnas "modelo" y "color" de tec_products
-- Fecha: 2026-03-02
-- Descripcion: Se eliminan los campos modelo y color ya que no se usan
--              y fueron reemplazados por el sistema de variantes/atributos.
-- ================================================================

ALTER TABLE `tec_products` DROP COLUMN `modelo`;
ALTER TABLE `tec_products` DROP COLUMN `color`;
