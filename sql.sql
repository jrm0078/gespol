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
INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('ficha_usuario', 'Ficha de Usuario', 'Ficha con datos del usuario seleccionado', 'PDF',
'<h2 style="color:#0084D9;border-bottom:2px solid #0084D9;padding-bottom:8px;">FICHA DE USUARIO</h2>
<table width="100%" cellpadding="6" style="border-collapse:collapse;margin-top:12px;">
  <tr><td style="background:#f0f8ff;font-weight:bold;width:30%;border:1px solid #ccc;">Nombre</td><td style="border:1px solid #ccc;">[[nombre]]</td></tr>
  <tr><td style="background:#f0f8ff;font-weight:bold;border:1px solid #ccc;">Email</td><td style="border:1px solid #ccc;">[[email]]</td></tr>
  <tr><td style="background:#f0f8ff;font-weight:bold;border:1px solid #ccc;">Rol</td><td style="border:1px solid #ccc;">[[rol]]</td></tr>
  <tr><td style="background:#f0f8ff;font-weight:bold;border:1px solid #ccc;">Estado</td><td style="border:1px solid #ccc;">[[activo]]</td></tr>
</table>
<p style="margin-top:20px;font-size:11px;color:#999;">Generado el [[fecha_generacion]]</p>',
'SELECT nombre, email, rol, IF(activo=1,''Activo'',''Inactivo'') as activo, DATE_FORMAT(NOW(),''%d/%m/%Y'') as fecha_generacion FROM usuario WHERE id = ?',
1);

INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, campo_clave, campo_valor, sql_query, orden, requerido, activo) VALUES
('ficha_usuario', 'id_usuario', 'Usuario', 'select_table', 'usuario', 'id', 'nombre', NULL, 1, 1, 1);

