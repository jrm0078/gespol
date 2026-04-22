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
  ayuda TEXT,
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

-- ============================================
-- TABLA INCIDENCIAS (módulo Gestión Policial)
-- ============================================

CREATE TABLE IF NOT EXISTS incidencias (
  id_incidencias  INT AUTO_INCREMENT PRIMARY KEY,
  fecha           DATE         NOT NULL,
  turno           VARCHAR(30)  DEFAULT NULL,
  dia_semana      VARCHAR(20)  DEFAULT NULL,
  num_agente      VARCHAR(150) DEFAULT NULL,
  num_agente1     VARCHAR(150) DEFAULT NULL,
  num_agente2     VARCHAR(150) DEFAULT NULL,
  incidencias     TEXT         DEFAULT NULL,
  encargado       VARCHAR(150) DEFAULT NULL,
  tipo            VARCHAR(50)  DEFAULT 'GENERAL',
  estado          TINYINT(1)   DEFAULT 1,
  fecha_creacion  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- PLANTILLAS INCIDENCIAS (3 modelos originales)
-- Se insertan solo la primera vez que se ejecuta el SQL.
-- ============================================

INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('informe_registro',
 'Informe de determinado Registro',
 'Ficha detallada de una incidencia: agentes, turno, descripción',
 'PDF',
 '<div style="font-family:Arial,sans-serif;font-size:13px;padding:30px;max-width:800px;margin:0 auto;"><h2 style="text-align:center;font-size:18px;margin-bottom:20px;">Informe de determinado Registro</h2><p><strong>Oficial:</strong> [[Encargado]]</p><table width="100%" cellpadding="6" style="border-collapse:collapse;margin-bottom:16px;"><thead><tr style="background:#f0f0f0;"><th style="border:1px solid #ccc;text-align:left;padding:6px;">Fecha</th><th style="border:1px solid #ccc;text-align:left;padding:6px;">Turno</th><th style="border:1px solid #ccc;text-align:left;padding:6px;">Día Semana</th><th style="border:1px solid #ccc;text-align:left;padding:6px;">Agentes actuantes</th><th style="border:1px solid #ccc;text-align:left;padding:6px;">Registro</th></tr></thead><tbody><tr><td style="border:1px solid #ccc;padding:6px;">[[Fecha]]</td><td style="border:1px solid #ccc;padding:6px;">[[Turno]]</td><td style="border:1px solid #ccc;padding:6px;">[[Dia_semana]]</td><td style="border:1px solid #ccc;padding:6px;">[[NumAgente]]<br>[[NumAgente1]]<br>[[NumAgente2]]</td><td style="border:1px solid #ccc;padding:6px;">[[Id_incidencias]]</td></tr></tbody></table><p>[[Incidencias]]</p><p><br><strong>EN RELACIÓN CON LA NOTA DE INCIDENCIA ANTERIOR:</strong></p></div>',
 'SELECT id_incidencias, DATE_FORMAT(fecha, ''%d/%m/%Y'') AS Fecha, turno AS Turno, dia_semana AS Dia_semana, IFNULL(num_agente,'''') AS NumAgente, IFNULL(num_agente1,'''') AS NumAgente1, IFNULL(num_agente2,'''') AS NumAgente2, incidencias AS Incidencias, IFNULL(encargado,'''') AS Encargado FROM incidencias WHERE id_incidencias = [[id_incidencia]]',
 1);

INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('incidencias_urbanismo',
 'Incidencias a Urbanismo',
 'Listado de incidencias remitidas al Negociado de Urbanismo',
 'PDF',
 '<div style="font-family:Arial,sans-serif;font-size:12px;padding:40px;max-width:750px;margin:0 auto;"><table width="100%" style="border:none;border-collapse:collapse;margin-bottom:20px;"><tr><td style="border:none;vertical-align:top;font-size:11px;"><strong>EXCMO. AYUNTAMIENTO<br>DE<br>MONTILLA<br>(CÓRDOBA)</strong><br>N.º E. L. 01140425</td><td style="border:none;text-align:right;font-weight:bold;font-size:14px;vertical-align:top;">POLICÍA LOCAL</td></tr></table><p style="margin-bottom:16px;font-size:11px;">N/Refª FJG/rh<br>Gex nº &nbsp;&nbsp;&nbsp;&nbsp;/</p><p style="margin-bottom:24px;"><strong>Negociado de Urbanismo</strong></p><br><p style="text-align:justify;padding-left:30px;margin-bottom:12px;">A continuación se transcriben notas de incidencias emitidas por distintos Oficiales dependientes de esta Jefatura, a los efectos que estime procedentes.</p><hr style="border:1px solid #000;margin:16px 40px;"><ul style="list-style-type:disc;padding-left:50px;margin:0;"><li style="font-style:italic;margin-bottom:4px;">(Refª [[id_incidencias]]).- [[Fecha]] &nbsp; [[Incidencias]]</li></ul><br><br><p style="text-align:center;"><strong>El Jefe de Policía</strong></p><br><br><br><hr style="border:0;border-top:1px solid #aaa;margin:0 10px;"><p style="font-size:9px;text-align:center;margin-top:3px;">C/. Conde de la Cortina, s/n - 14550 MONTILLA (Córdoba) – Tlfno.: 957 65 26 26, Fax 957 65 58 67 – e-mail: policia@montilla.es</p></div>',
 'SELECT id_incidencias, DATE_FORMAT(fecha, ''%d/%m/%Y'') AS Fecha, incidencias AS Incidencias FROM incidencias WHERE tipo = ''URBANISMO'' AND fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] AND estado = 1 ORDER BY fecha ASC',
 1);

INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('incidencias_senalizacion',
 'Incidencias a Señalización',
 'Listado de incidencias sobre señalización para reparación/colocación',
 'PDF',
 '<div style="font-family:Arial,sans-serif;font-size:12px;padding:40px;max-width:750px;margin:0 auto;"><table width="100%" style="border:none;border-collapse:collapse;margin-bottom:16px;"><tr><td style="border:none;vertical-align:top;font-size:11px;"><strong>EXCMO. AYUNTAMIENTO<br>DE<br>MONTILLA<br>(CÓRDOBA)</strong><br>N.º E. L. 01140425</td><td style="border:none;text-align:right;font-weight:bold;font-size:14px;vertical-align:top;">POLICÍA LOCAL</td></tr></table><p style="text-align:right;margin-bottom:16px;"><strong>Asunto</strong>: Incidencias señalización</p><p style="margin-bottom:24px;font-size:11px;">N/Refª FJG/rh<br>Gex nº &nbsp;&nbsp;&nbsp;&nbsp;/</p><br><p style="text-align:justify;padding-left:30px;margin-bottom:12px;">Con motivo de no disponer al día de la fecha por este negociado, de personal adscrito para realizar trabajos de señalización, a continuación le doy traslado de las INCIDENCIAS SOBRE SEÑALIZACIÓN surgidas, para que por personal de ese servicio se proceda a la mayor brevedad posible a su reparación/colocación, debiendo previamente, el personal que va a realizar los trabajos, ponerse en contacto con esta Jefatura para recibir instrucciones sobre los mismos.</p><hr style="border:1px solid #000;margin:16px 40px;"><ul style="list-style-type:disc;padding-left:50px;margin:0;"><li style="font-style:italic;margin-bottom:4px;">(Refª [[id_incidencias]]).- [[Fecha]] &nbsp; [[Incidencias]]</li></ul><br><br><p style="text-align:center;"><strong>El Jefe de Policía</strong></p><br><br><br><hr style="border:0;border-top:1px solid #aaa;margin:0 10px;"><p style="font-size:9px;text-align:center;margin-top:3px;">C/. Conde de la Cortina, s/n - 14550 MONTILLA (Córdoba) – Tlfno.: 957 65 26 26, Fax 957 65 58 67 – e-mail: policia@montilla.es</p></div>',
 'SELECT id_incidencias, DATE_FORMAT(fecha, ''%d/%m/%Y'') AS Fecha, incidencias AS Incidencias FROM incidencias WHERE tipo = ''SEÑALIZACION'' AND fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] AND estado = 1 ORDER BY fecha ASC',
 1);

-- Filtros plantilla 1: número de registro
INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, campo_clave, campo_valor, sql_query, orden, requerido, activo) VALUES
('informe_registro', 'id_incidencia', 'Nº Registro', 'number', NULL, NULL, NULL, NULL, 1, 1, 1);

-- Filtros plantillas 2 y 3: rango de fechas
INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, campo_clave, campo_valor, sql_query, orden, requerido, activo) VALUES
('incidencias_urbanismo', 'fecha_inicio', 'Fecha desde', 'date', NULL, NULL, NULL, NULL, 1, 1, 1),
('incidencias_urbanismo', 'fecha_fin',    'Fecha hasta', 'date', NULL, NULL, NULL, NULL, 2, 1, 1);

INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, campo_clave, campo_valor, sql_query, orden, requerido, activo) VALUES
('incidencias_senalizacion', 'fecha_inicio', 'Fecha desde', 'date', NULL, NULL, NULL, NULL, 1, 1, 1),
('incidencias_senalizacion', 'fecha_fin',    'Fecha hasta', 'date', NULL, NULL, NULL, NULL, 2, 1, 1);

-- ============================================
-- TABLA MÓDULO REPOSITORIO
-- ============================================
CREATE TABLE IF NOT EXISTS repositorio (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  descripcion     VARCHAR(255) NOT NULL,
  directorio      VARCHAR(255) DEFAULT '',
  nombre_original VARCHAR(255) NOT NULL,
  nombre_fichero  VARCHAR(255) NOT NULL,
  tipo            VARCHAR(100) DEFAULT '',
  tamano          BIGINT DEFAULT 0,
  fecha_subida    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLAS MÓDULO GESTIÓN POLICIAL
-- ============================================

CREATE TABLE IF NOT EXISTS agentes (
  numagente   INT PRIMARY KEY,
  nombre      VARCHAR(150) NOT NULL,
  indicativo  INT DEFAULT NULL,
  activo      TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS encargados (
  numencargado INT PRIMARY KEY,
  encargado    VARCHAR(150) NOT NULL,
  cargo        VARCHAR(100) DEFAULT NULL,
  estado       VARCHAR(50)  DEFAULT NULL,
  numagente    INT          DEFAULT NULL,
  FOREIGN KEY (numagente) REFERENCES agentes(numagente) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS servicios (
  numservicio          INT AUTO_INCREMENT PRIMARY KEY,
  fecha                DATETIME DEFAULT NULL,
  fecha2               DATETIME DEFAULT NULL,
  turno                VARCHAR(20)  DEFAULT NULL,
  tipodia              VARCHAR(50)  DEFAULT NULL,
  diasemana            VARCHAR(20)  DEFAULT NULL,
  numagenteencargado   INT          DEFAULT NULL,
  numagente            INT          DEFAULT NULL,
  numagente1           INT          DEFAULT NULL,
  numagente2           INT          DEFAULT NULL,
  numagente3           INT          DEFAULT NULL,
  numagente4           INT          DEFAULT NULL,
  numagente5           INT          DEFAULT NULL,
  numagente6           INT          DEFAULT NULL,
  numagente7           INT          DEFAULT NULL,
  numagente8           INT          DEFAULT NULL,
  numagente9           INT          DEFAULT NULL,
  numagente10          INT          DEFAULT NULL,
  numagente11          INT          DEFAULT NULL,
  numagente12          INT          DEFAULT NULL,
  numagente13          INT          DEFAULT NULL,
  numagente14          INT          DEFAULT NULL,
  numagente15          INT          DEFAULT NULL,
  agenteextra          INT          DEFAULT NULL,
  agenteextra1         INT          DEFAULT NULL,
  agenteextra2         INT          DEFAULT NULL,
  agenteextra3         INT          DEFAULT NULL,
  agenteextra4         INT          DEFAULT NULL,
  agenteextra5         INT          DEFAULT NULL,
  agenteextra6         INT          DEFAULT NULL,
  agenteextra7         INT          DEFAULT NULL,
  agenteextra8         INT          DEFAULT NULL,
  agenteextra9         INT          DEFAULT NULL,
  horainicio           DATETIME     DEFAULT NULL,
  horainicio1          DATETIME     DEFAULT NULL,
  horainicio2          DATETIME     DEFAULT NULL,
  horainicio3          DATETIME     DEFAULT NULL,
  horainicio4          DATETIME     DEFAULT NULL,
  horainicio5          DATETIME     DEFAULT NULL,
  horainicio6          DATETIME     DEFAULT NULL,
  horainicio7          DATETIME     DEFAULT NULL,
  horainicio8          DATETIME     DEFAULT NULL,
  horainicio9          DATETIME     DEFAULT NULL,
  horafinal            DATETIME     DEFAULT NULL,
  horafinal1           DATETIME     DEFAULT NULL,
  horafinal2           DATETIME     DEFAULT NULL,
  horafinal3           DATETIME     DEFAULT NULL,
  horafinal4           DATETIME     DEFAULT NULL,
  horafinal5           DATETIME     DEFAULT NULL,
  horafinal6           DATETIME     DEFAULT NULL,
  horafinal7           DATETIME     DEFAULT NULL,
  horafinal8           DATETIME     DEFAULT NULL,
  horafinal9           DATETIME     DEFAULT NULL,
  textoservicioextra   TEXT         DEFAULT NULL,
  valor                INT          DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS incidencias_pol (
  numincidencia        INT AUTO_INCREMENT PRIMARY KEY,
  numservicio          INT          DEFAULT NULL,
  incidencias          TEXT         DEFAULT NULL,
  destinatario         VARCHAR(255) DEFAULT NULL,
  etiquetas_filtro     VARCHAR(255) DEFAULT NULL,
  numagente            INT          DEFAULT NULL,
  numagente1           INT          DEFAULT NULL,
  numagente2           INT          DEFAULT NULL,
  numagente3           INT          DEFAULT NULL,
  historialincidencias TEXT         DEFAULT NULL,
  valor                INT          DEFAULT NULL,
  FOREIGN KEY (numservicio) REFERENCES servicios(numservicio) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
