CREATE TABLE usuario (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(150),
email VARCHAR(150) UNIQUE,
contrasenia VARCHAR(255),
rol ENUM('Superadmin','Admin','Usuario') DEFAULT 'Usuario',
activo TINYINT(1) DEFAULT 1
);

INSERT INTO usuario(nombre,email,contrasenia,rol,activo)
VALUES('admin','admin@admin.com','$2y$10$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2','Superadmin',1);

-- ============================================
-- TABLAS MÓDULO PLANTILLAS
-- ============================================

CREATE TABLE plantillas_maestro (
  cod_plantilla VARCHAR(50) PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  tipo_documento VARCHAR(100),
  contenido LONGTEXT NOT NULL,
  tabla_origen VARCHAR(100),
  campo_clave VARCHAR(100),
  sql_consulta LONGTEXT,
  estado TINYINT(1) DEFAULT 1,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE plantillas_filtros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cod_plantilla VARCHAR(50) NOT NULL,
  nombre_filtro VARCHAR(100),
  etiqueta VARCHAR(255),
  tipo_filtro VARCHAR(20),
  tabla_datos VARCHAR(100),
  campo_clave VARCHAR(100),
  campo_valor VARCHAR(100),
  sql_query LONGTEXT,
  operador VARCHAR(20),
  orden INT DEFAULT 999,
  requerido TINYINT(1) DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE plantillas_variables (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cod_plantilla VARCHAR(50) NOT NULL,
  nombre_variable VARCHAR(100),
  etiqueta VARCHAR(255),
  tipo VARCHAR(50),
  requerido TINYINT(1) DEFAULT 0,
  orden INT DEFAULT 999,
  activo TINYINT(1) DEFAULT 1,
  FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE plantillas_documentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cod_plantilla VARCHAR(50) NOT NULL,
  id_usuario INT,
  contenido_final LONGTEXT,
  datos_json JSON,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de prueba
INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, estado) VALUES
('presupuesto_1', 'Presupuesto Standard', 'Plantilla de presupuesto básico', 'PDF', '<h2>PRESUPUESTO</h2><p>Cliente: {%%cliente%%}</p><p>Monto: {%%monto%%}</p>', 1),
('contrato_1', 'Contrato Standard', 'Plantilla de contrato básico', 'PDF', '<h2>CONTRATO</h2><p>Fecha: {%%fecha%%}</p><p>Partes: {%%partes%%}</p>', 1);
