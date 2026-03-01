-- ============================================================
-- Sistema de Variantes de Producto con Atributos
-- ERP SURCOC - Fase 1
-- ============================================================

-- Atributos definidos por el usuario (Color, Capacidad, Calidad, etc.)
CREATE TABLE IF NOT EXISTS tec_atributos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    activo CHAR(1) DEFAULT '1',
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Valores posibles para cada atributo
CREATE TABLE IF NOT EXISTS tec_atributo_valores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atributo_id INT NOT NULL,
    valor VARCHAR(80) NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (atributo_id) REFERENCES tec_atributos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Variantes de un producto (cada fila = 1 combinacion con su propio SKU/precio/stock)
CREATE TABLE IF NOT EXISTS tec_product_variantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sku VARCHAR(50) DEFAULT NULL,
    barcode VARCHAR(50) DEFAULT NULL,
    price DECIMAL(15,2) DEFAULT NULL,
    precio_x_mayor DECIMAL(15,2) DEFAULT NULL,
    imagen VARCHAR(200) DEFAULT NULL,
    activo CHAR(1) DEFAULT '1',
    FOREIGN KEY (product_id) REFERENCES tec_products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Relacion variante <-> valores de atributo
CREATE TABLE IF NOT EXISTS tec_variante_atributos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variante_id INT NOT NULL,
    atributo_id INT NOT NULL,
    valor_id INT NOT NULL,
    FOREIGN KEY (variante_id) REFERENCES tec_product_variantes(id) ON DELETE CASCADE,
    FOREIGN KEY (atributo_id) REFERENCES tec_atributos(id),
    FOREIGN KEY (valor_id) REFERENCES tec_atributo_valores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Columnas nuevas en tablas existentes (variant_id nullable)
ALTER TABLE tec_prod_store ADD COLUMN IF NOT EXISTS variant_id INT DEFAULT NULL;
ALTER TABLE tec_sale_items ADD COLUMN IF NOT EXISTS variant_id INT DEFAULT NULL;
ALTER TABLE tec_compra_items ADD COLUMN IF NOT EXISTS variant_id INT DEFAULT NULL;
ALTER TABLE tec_movim ADD COLUMN IF NOT EXISTS variant_id INT DEFAULT NULL;

-- Actualizar indice unico para incluir variant_id
ALTER TABLE tec_prod_store DROP INDEX product_id, ADD UNIQUE INDEX product_id (product_id, store_id, variant_id);

-- Datos iniciales: atributos comunes
INSERT INTO tec_atributos (nombre, activo, orden) VALUES
('Color', '1', 1),
('Capacidad', '1', 2),
('Calidad', '1', 3);

-- Valores iniciales para Color
INSERT INTO tec_atributo_valores (atributo_id, valor, orden) VALUES
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Negro', 1),
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Blanco', 2),
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Rojo', 3),
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Azul', 4),
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Verde', 5),
((SELECT id FROM tec_atributos WHERE nombre='Color'), 'Rosado', 6);

-- Valores iniciales para Capacidad
INSERT INTO tec_atributo_valores (atributo_id, valor, orden) VALUES
((SELECT id FROM tec_atributos WHERE nombre='Capacidad'), '8GB', 1),
((SELECT id FROM tec_atributos WHERE nombre='Capacidad'), '16GB', 2),
((SELECT id FROM tec_atributos WHERE nombre='Capacidad'), '32GB', 3),
((SELECT id FROM tec_atributos WHERE nombre='Capacidad'), '64GB', 4),
((SELECT id FROM tec_atributos WHERE nombre='Capacidad'), '128GB', 5);

-- Valores iniciales para Calidad
INSERT INTO tec_atributo_valores (atributo_id, valor, orden) VALUES
((SELECT id FROM tec_atributos WHERE nombre='Calidad'), 'Original', 1),
((SELECT id FROM tec_atributos WHERE nombre='Calidad'), 'AAA', 2),
((SELECT id FROM tec_atributos WHERE nombre='Calidad'), 'Generica', 3);
