-- Rediseño completo del Módulo de Gastos
-- Tablas independientes (no usa tec_compras)

-- Tabla de categorías de gasto
CREATE TABLE IF NOT EXISTS tec_gastos_categorias (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  color VARCHAR(7) NOT NULL DEFAULT '#6c757d',
  activo CHAR(1) NOT NULL DEFAULT '1',
  orden INT(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tec_gastos_categorias (nombre, color, activo, orden) VALUES
('Alquiler', '#337ab7', '1', 1),
('Salarios y Planilla', '#5cb85c', '1', 2),
('Servicios (Luz, Agua, Internet)', '#f0ad4e', '1', 3),
('Suscripciones', '#5bc0de', '1', 4),
('Suministros de Oficina', '#d9534f', '1', 5),
('Transporte y Combustible', '#ff7043', '1', 6),
('Impuestos y Tributos', '#795548', '1', 7),
('Mantenimiento y Reparaciones', '#9c27b0', '1', 8),
('Marketing y Publicidad', '#e91e63', '1', 9),
('Otros', '#6c757d', '1', 10);

-- Tabla principal de gastos (cabecera = 1 factura)
CREATE TABLE IF NOT EXISTS tec_gastos (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  store_id INT(11) NOT NULL,
  fecha DATETIME NOT NULL,
  tipoDoc VARCHAR(10) DEFAULT NULL,
  nroDoc VARCHAR(50) DEFAULT NULL,
  proveedor_id INT(11) DEFAULT NULL,
  monto_base DECIMAL(12,2) NOT NULL DEFAULT 0,
  igv DECIMAL(12,2) NOT NULL DEFAULT 0,
  por_igv DECIMAL(5,2) NOT NULL DEFAULT 18,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  redondeo DECIMAL(8,2) DEFAULT 0,
  estado_pago ENUM('PAGADO','PENDIENTE') NOT NULL DEFAULT 'PAGADO',
  fecha_vencimiento DATE DEFAULT NULL,
  comprobante_archivo VARCHAR(255) DEFAULT NULL,
  observaciones TEXT DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_store (store_id),
  KEY idx_fecha (fecha),
  KEY idx_proveedor (proveedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de items de gasto (cada línea de la factura)
CREATE TABLE IF NOT EXISTS tec_gastos_items (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  gasto_id INT(11) NOT NULL,
  categoria_id INT(11) DEFAULT NULL,
  descripcion VARCHAR(255) NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(12,4) NOT NULL DEFAULT 0,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  KEY idx_gasto (gasto_id),
  CONSTRAINT fk_gasto_item FOREIGN KEY (gasto_id) REFERENCES tec_gastos(id) ON DELETE CASCADE,
  CONSTRAINT fk_gasto_cat FOREIGN KEY (categoria_id) REFERENCES tec_gastos_categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
