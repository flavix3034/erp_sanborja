-- ============================================
-- MODULO CAJA CHICA - Tablas y datos iniciales
-- Base de datos: erp_surcoc
-- ============================================

-- 1. Categorias de gasto
CREATE TABLE IF NOT EXISTS `tec_cajachica_categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `color` VARCHAR(7) NOT NULL DEFAULT '#6c757d',
  `activo` CHAR(1) NOT NULL DEFAULT '1',
  `orden` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Categorias por defecto
INSERT INTO `tec_cajachica_categorias` (`nombre`, `color`, `activo`, `orden`) VALUES
('Artículos de Limpieza', '#17a2b8', '1', 1),
('Papelería y Oficina', '#ffc107', '1', 2),
('Transporte / Pasajes', '#28a745', '1', 3),
('Mantenimiento y Reparaciones menores', '#fd7e14', '1', 4),
('Alimentos y Bebidas', '#dc3545', '1', 5),
('Otros / Emergencias', '#6c757d', '1', 6);

-- 2. Periodos de caja chica (apertura/cierre)
CREATE TABLE IF NOT EXISTS `tec_cajachica_periodos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) NOT NULL,
  `monto_inicial` DECIMAL(10,2) NOT NULL,
  `saldo_actual` DECIMAL(10,2) NOT NULL,
  `fecha_apertura` DATETIME NOT NULL,
  `fecha_cierre` DATETIME DEFAULT NULL,
  `estado` ENUM('ABIERTO','CERRADO') NOT NULL DEFAULT 'ABIERTO',
  `usuario_apertura` INT(11) NOT NULL,
  `usuario_cierre` INT(11) DEFAULT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_store_estado` (`store_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 3. Gastos individuales
CREATE TABLE IF NOT EXISTS `tec_cajachica_gastos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `periodo_id` INT(11) NOT NULL,
  `categoria_id` INT(11) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `descripcion` VARCHAR(255) NOT NULL,
  `beneficiario` VARCHAR(150) NOT NULL DEFAULT '',
  `comprobante` VARCHAR(255) DEFAULT NULL,
  `fecha_gasto` DATETIME NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_periodo` (`periodo_id`),
  KEY `idx_categoria` (`categoria_id`),
  CONSTRAINT `fk_cajachica_gasto_periodo` FOREIGN KEY (`periodo_id`) REFERENCES `tec_cajachica_periodos`(`id`),
  CONSTRAINT `fk_cajachica_gasto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `tec_cajachica_categorias`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
