-- Módulo Servicio Técnico - ERP SURCOC
-- Creación de tablas principales

-- Tabla de técnicos
CREATE TABLE IF NOT EXISTS tec_tecnicos (
    id int(11) NOT NULL AUTO_INCREMENT,
    codigo varchar(20) NOT NULL,
    nombre varchar(200) NOT NULL,
    especialidad varchar(100) DEFAULT NULL,
    telefono varchar(50) DEFAULT NULL,
    email varchar(100) DEFAULT NULL,
    activo char(1) NOT NULL DEFAULT '1',
    fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla principal de servicios técnicos
CREATE TABLE IF NOT EXISTS tec_servicios_tecnicos (
    id int(11) NOT NULL AUTO_INCREMENT,
    codigo varchar(50) NOT NULL,
    cliente_nombre varchar(200) NOT NULL,
    cliente_telefono varchar(50) DEFAULT NULL,
    cliente_email varchar(100) DEFAULT NULL,
    equipo_tipo enum('Computadora','Laptop','Celular','Tablet','Otro') DEFAULT 'Otro',
    equipo_descripcion text,
    marca varchar(100) DEFAULT NULL,
    modelo varchar(100) DEFAULT NULL,
    numero_serie varchar(100) DEFAULT NULL,
    problema_reportado text NOT NULL,
    diagnostico text,
    estado enum('RECIBIDO','EN DIAGNOSTICO','EN REPARACION','ESPERA REPUESTOS','REPARADO','ENTREGADO','CANCELADO') DEFAULT 'RECIBIDO',
    prioridad enum('BAJA','NORMAL','ALTA','URGENTE') DEFAULT 'NORMAL',
    tecnico_asignado int(11) DEFAULT NULL,
    fecha_recepcion datetime DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega datetime DEFAULT NULL,
    costo_presupuesto decimal(10,2) DEFAULT 0.00,
    costo_final decimal(10,2) DEFAULT 0.00,
    observaciones text,
    usuario_registra int(11) DEFAULT NULL,
    activo char(1) NOT NULL DEFAULT '1',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY codigo (codigo),
    KEY tecnico_asignado (tecnico_asignado),
    KEY estado (estado),
    KEY fecha_recepcion (fecha_recepcion),
    FOREIGN KEY (tecnico_asignado) REFERENCES tec_tecnicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de historial de estados
CREATE TABLE IF NOT EXISTS tec_servicios_estados (
    id int(11) NOT NULL AUTO_INCREMENT,
    servicio_id int(11) NOT NULL,
    estado_anterior varchar(50) DEFAULT NULL,
    estado_nuevo varchar(50) NOT NULL,
    tecnico_id int(11) DEFAULT NULL,
    comentarios text,
    fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
    usuario_id int(11) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY servicio_id (servicio_id),
    KEY tecnico_id (tecnico_id),
    KEY fecha_registro (fecha_registro),
    FOREIGN KEY (servicio_id) REFERENCES tec_servicios_tecnicos(id),
    FOREIGN KEY (tecnico_id) REFERENCES tec_tecnicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de notas por servicio
CREATE TABLE IF NOT EXISTS tec_servicios_notas (
    id int(11) NOT NULL AUTO_INCREMENT,
    servicio_id int(11) NOT NULL,
    tipo_nota varchar(50) DEFAULT 'GENERAL',
    nota text NOT NULL,
    tecnico_id int(11) DEFAULT NULL,
    fecha_registro datetime DEFAULT CURRENT_TIMESTAMP,
    usuario_id int(11) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY servicio_id (servicio_id),
    KEY tecnico_id (tecnico_id),
    KEY fecha_registro (fecha_registro),
    FOREIGN KEY (servicio_id) REFERENCES tec_servicios_tecnicos(id),
    FOREIGN KEY (tecnico_id) REFERENCES tec_tecnicos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertar técnicos de ejemplo
INSERT INTO tec_tecnicos (codigo, nombre, especialidad, telefono, email) VALUES
('TEC-001', 'CARLOS MENDOZA', 'Computadoras y Laptops', '987654321', 'carlos@email.com'),
('TEC-002', 'MARÍA LÓPEZ', 'Celulares y Tablets', '987654322', 'maria@email.com'),
('TEC-003', 'JUAN PEREZ', 'Hardware y Redes', '987654323', 'juan@email.com'),
('TEC-004', 'ANA GARCÍA', 'Software y Sistemas', '987654324', 'ana@email.com'),
('TEC-005', 'LUIS ROJAS', 'Todo tipo de equipos', '987654325', 'luis@email.com');

-- Insertar módulo en sistema de permisos (asumiendo estructura similar)
INSERT IGNORE INTO tec_modulos (modulo, descripcion) VALUES ('servicios', 'Servicio Técnico');