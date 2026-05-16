-- ============================================
-- TABLA VEHICULOS
-- ============================================
CREATE TABLE IF NOT EXISTS vehiculos (
  idVehiculo     VARCHAR(20)  NOT NULL PRIMARY KEY,
  Matricula      VARCHAR(20)  DEFAULT NULL,
  marca_modelo   VARCHAR(100) DEFAULT NULL,
  clase          VARCHAR(50)  DEFAULT NULL,
  color          VARCHAR(30)  DEFAULT NULL,
  fecmat         DATE         DEFAULT NULL,
  bast           VARCHAR(50)  DEFAULT NULL,
  cia            VARCHAR(100) DEFAULT NULL,
  poliza         VARCHAR(50)  DEFAULT NULL,
  ValidezPoliza  VARCHAR(50)  DEFAULT NULL,
  FechaExpPoliza DATE         DEFAULT NULL,
  idhabitante    INT          DEFAULT NULL,
  dnitit         VARCHAR(15)  DEFAULT NULL,
  apetit         VARCHAR(100) DEFAULT NULL,
  nomtit         VARCHAR(100) DEFAULT NULL,
  domtit         VARCHAR(150) DEFAULT NULL,
  pobtit         VARCHAR(100) DEFAULT NULL,
  provtit        VARCHAR(100) DEFAULT NULL,
  tft            VARCHAR(20)  DEFAULT NULL,
  email          VARCHAR(150) DEFAULT NULL,
  CPostalVeh     VARCHAR(10)  DEFAULT NULL,
  FechaAlta      DATETIME     DEFAULT CURRENT_TIMESTAMP,
  Observaciones  TEXT         DEFAULT NULL,
  CONSTRAINT fk_vehiculo_habitante FOREIGN KEY (idhabitante)
    REFERENCES habitantes(idhabitante) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA HABITANTES
-- ============================================
CREATE TABLE IF NOT EXISTS habitantes (
  idhabitante   INT AUTO_INCREMENT PRIMARY KEY,
  dni           VARCHAR(15)  DEFAULT NULL,
  apel          VARCHAR(100) DEFAULT NULL,
  nom           VARCHAR(100) DEFAULT NULL,
  lugnac        VARCHAR(100) DEFAULT NULL,
  provnac       VARCHAR(100) DEFAULT NULL,
  fecnac        DATE         DEFAULT NULL,
  padre         VARCHAR(100) DEFAULT NULL,
  madre         VARCHAR(100) DEFAULT NULL,
  calle         VARCHAR(150) DEFAULT NULL,
  pob           VARCHAR(100) DEFAULT NULL,
  prov          VARCHAR(100) DEFAULT NULL,
  tf            VARCHAR(20)  DEFAULT NULL,
  tft           VARCHAR(20)  DEFAULT NULL,
  email         VARCHAR(150) DEFAULT NULL,
  sexo          VARCHAR(10)  DEFAULT NULL,
  Pais          VARCHAR(100) DEFAULT NULL,
  FechaAlta     DATETIME     DEFAULT CURRENT_TIMESTAMP,
  CPostal       VARCHAR(10)  DEFAULT NULL,
  Observaciones TEXT         DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- TABLA INCIDENCIAS UNIFICADA (fusión de incidencias + incidencias_pol)
-- ========================================================================

CREATE TABLE IF NOT EXISTS incidencias (
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
  fecha                DATE         DEFAULT NULL,
  turno                VARCHAR(30)  DEFAULT NULL,
  dia_semana           VARCHAR(20)  DEFAULT NULL,
  encargado            VARCHAR(150) DEFAULT NULL,
  tipo                 VARCHAR(50)  DEFAULT 'GENERAL',
  estado               TINYINT(1)   DEFAULT 1,
  fecha_creacion       DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (numservicio) REFERENCES servicios(numservicio) ON DELETE SET NULL
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
 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, s.turno AS Turno, s.diasemana AS Dia_semana, IFNULL(a.nombre,'''') AS NumAgente, IFNULL(a1.nombre,'''') AS NumAgente1, IFNULL(a2.nombre,'''') AS NumAgente2, i.incidencias AS Incidencias, IFNULL(e.encargado,'''') AS Encargado FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio LEFT JOIN encargados e ON s.numagenteencargado = e.numencargado LEFT JOIN agentes a ON i.numagente = a.numagente LEFT JOIN agentes a1 ON i.numagente1 = a1.numagente LEFT JOIN agentes a2 ON i.numagente2 = a2.numagente WHERE i.numincidencia = [[id_incidencia]]',
 1);

INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('incidencias_urbanismo',
 'Incidencias a Urbanismo',
 'Listado de incidencias remitidas al Negociado de Urbanismo',
 'PDF',
 '<div style="font-family:Arial,sans-serif;font-size:12px;padding:40px;max-width:750px;margin:0 auto;"><table width="100%" style="border:none;border-collapse:collapse;margin-bottom:20px;"><tr><td style="border:none;vertical-align:top;font-size:11px;"><strong>EXCMO. AYUNTAMIENTO<br>DE<br>MONTILLA<br>(CÓRDOBA)</strong><br>N.º E. L. 01140425</td><td style="border:none;text-align:right;font-weight:bold;font-size:14px;vertical-align:top;">POLICÍA LOCAL</td></tr></table><p style="margin-bottom:16px;font-size:11px;">N/Refª FJG/rh<br>Gex nº &nbsp;&nbsp;&nbsp;&nbsp;/</p><p style="margin-bottom:24px;"><strong>Negociado de Urbanismo</strong></p><br><p style="text-align:justify;padding-left:30px;margin-bottom:12px;">A continuación se transcriben notas de incidencias emitidas por distintos Oficiales dependientes de esta Jefatura, a los efectos que estime procedentes.</p><hr style="border:1px solid #000;margin:16px 40px;"><ul style="list-style-type:disc;padding-left:50px;margin:0;"><li style="font-style:italic;margin-bottom:4px;">(Refª [[id_incidencias]]).- [[Fecha]] &nbsp; [[Incidencias]]</li></ul><br><br><p style="text-align:center;"><strong>El Jefe de Policía</strong></p><br><br><br><hr style="border:0;border-top:1px solid #aaa;margin:0 10px;"><p style="font-size:9px;text-align:center;margin-top:3px;">C/. Conde de la Cortina, s/n - 14550 MONTILLA (Córdoba) – Tlfno.: 957 65 26 26, Fax 957 65 58 67 – e-mail: policia@montilla.es</p></div>',
 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, i.incidencias AS Incidencias FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio WHERE i.etiquetas_filtro LIKE ''%URBANISMO%'' AND s.fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] ORDER BY s.fecha ASC',
 1);

INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado) VALUES
('incidencias_senalizacion',
 'Incidencias a Señalización',
 'Listado de incidencias sobre señalización para reparación/colocación',
 'PDF',
 '<div style="font-family:Arial,sans-serif;font-size:12px;padding:40px;max-width:750px;margin:0 auto;"><table width="100%" style="border:none;border-collapse:collapse;margin-bottom:16px;"><tr><td style="border:none;vertical-align:top;font-size:11px;"><strong>EXCMO. AYUNTAMIENTO<br>DE<br>MONTILLA<br>(CÓRDOBA)</strong><br>N.º E. L. 01140425</td><td style="border:none;text-align:right;font-weight:bold;font-size:14px;vertical-align:top;">POLICÍA LOCAL</td></tr></table><p style="text-align:right;margin-bottom:16px;"><strong>Asunto</strong>: Incidencias señalización</p><p style="margin-bottom:24px;font-size:11px;">N/Refª FJG/rh<br>Gex nº &nbsp;&nbsp;&nbsp;&nbsp;/</p><br><p style="text-align:justify;padding-left:30px;margin-bottom:12px;">Con motivo de no disponer al día de la fecha por este negociado, de personal adscrito para realizar trabajos de señalización, a continuación le doy traslado de las INCIDENCIAS SOBRE SEÑALIZACIÓN surgidas, para que por personal de ese servicio se proceda a la mayor brevedad posible a su reparación/colocación, debiendo previamente, el personal que va a realizar los trabajos, ponerse en contacto con esta Jefatura para recibir instrucciones sobre los mismos.</p><hr style="border:1px solid #000;margin:16px 40px;"><ul style="list-style-type:disc;padding-left:50px;margin:0;"><li style="font-style:italic;margin-bottom:4px;">(Refª [[id_incidencias]]).- [[Fecha]] &nbsp; [[Incidencias]]</li></ul><br><br><p style="text-align:center;"><strong>El Jefe de Policía</strong></p><br><br><br><hr style="border:0;border-top:1px solid #aaa;margin:0 10px;"><p style="font-size:9px;text-align:center;margin-top:3px;">C/. Conde de la Cortina, s/n - 14550 MONTILLA (Córdoba) – Tlfno.: 957 65 26 26, Fax 957 65 58 67 – e-mail: policia@montilla.es</p></div>',
 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, i.incidencias AS Incidencias FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio WHERE i.etiquetas_filtro LIKE ''%SEÑALIZACION%'' AND s.fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] ORDER BY s.fecha ASC',
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
  numagente   INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(150) NOT NULL,
  indicativo  VARCHAR(20) DEFAULT NULL,
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

-- incidencias_pol fusionada en tabla 'incidencias' (ver arriba)

-- ============================================
-- MIGRACIÓN #9: unificar incidencias + incidencias_pol
-- Ejecutar UNA VEZ sobre BD existente
-- ============================================

-- 1. Añadir columnas de incidencias_pol que faltan en incidencias (si existen ambas tablas)
ALTER TABLE incidencias_pol
  ADD COLUMN IF NOT EXISTS fecha                DATE         DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS turno                VARCHAR(30)  DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS dia_semana           VARCHAR(20)  DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS encargado            VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS tipo                 VARCHAR(50)  DEFAULT 'GENERAL',
  ADD COLUMN IF NOT EXISTS estado               TINYINT(1)   DEFAULT 1,
  ADD COLUMN IF NOT EXISTS fecha_creacion       DATETIME     DEFAULT CURRENT_TIMESTAMP;

-- 2. Renombrar: incidencias → incidencias_backup, incidencias_pol → incidencias
RENAME TABLE incidencias TO incidencias_backup;
RENAME TABLE incidencias_pol TO incidencias;

-- 3. Actualizar queries de plantillas
UPDATE plantillas_maestro SET
  sql_consulta = 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, s.turno AS Turno, s.diasemana AS Dia_semana, IFNULL(a.nombre,'''') AS NumAgente, IFNULL(a1.nombre,'''') AS NumAgente1, IFNULL(a2.nombre,'''') AS NumAgente2, i.incidencias AS Incidencias, IFNULL(e.encargado,'''') AS Encargado FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio LEFT JOIN encargados e ON s.numagenteencargado = e.numencargado LEFT JOIN agentes a ON i.numagente = a.numagente LEFT JOIN agentes a1 ON i.numagente1 = a1.numagente LEFT JOIN agentes a2 ON i.numagente2 = a2.numagente WHERE i.numincidencia = [[id_incidencia]]'
WHERE cod_plantilla = 'informe_registro';

UPDATE plantillas_maestro SET
  sql_consulta = 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, i.incidencias AS Incidencias FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio WHERE i.etiquetas_filtro LIKE ''%URBANISMO%'' AND s.fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] ORDER BY s.fecha ASC'
WHERE cod_plantilla = 'incidencias_urbanismo';

UPDATE plantillas_maestro SET
  sql_consulta = 'SELECT i.numincidencia AS id_incidencias, DATE_FORMAT(s.fecha, ''%d/%m/%Y'') AS Fecha, i.incidencias AS Incidencias FROM incidencias i LEFT JOIN servicios s ON i.numservicio = s.numservicio WHERE i.etiquetas_filtro LIKE ''%SEÑALIZACION%'' AND s.fecha BETWEEN [[fecha_inicio]] AND [[fecha_fin]] ORDER BY s.fecha ASC'
WHERE cod_plantilla = 'incidencias_senalizacion';


-- ============================================
-- TABLA LOG_ACCESOS
-- ============================================
CREATE TABLE IF NOT EXISTS log_accesos (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  fecha     DATE         DEFAULT NULL,
  hora      TIME         DEFAULT NULL,
  usuario   VARCHAR(150) DEFAULT NULL,
  accion    TEXT         DEFAULT NULL,
  FechaHora DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- ACTUALIZAR PLANTILLAS: escudo desde Repositorio
-- Ejecutar DESPUÉS de subir el escudo al Repositorio
-- (directorio recomendado: images_plantillas)
-- ============================================
UPDATE plantillas_maestro
SET contenido = REPLACE(
    contenido,
    'src="images/escudo.png"',
    CONCAT('src="', (
        SELECT CONCAT(
            'repositorio/',
            IF(TRIM(BOTH '/' FROM directorio) != '',
               CONCAT(TRIM(BOTH '/' FROM directorio), '/'),
               ''),
            nombre_fichero)
        FROM repositorio
        WHERE tipo LIKE 'image/%'
          AND (directorio LIKE '%images_plantillas%'
               OR descripcion   LIKE '%escudo%'
               OR nombre_original LIKE '%escudo%')
        ORDER BY fecha_subida DESC
        LIMIT 1
    ), '"')
)
WHERE cod_plantilla IN ('incidencias_senalizacion', 'incidencias_urbanismo');


-- ============================================================
-- MÓDULO GESTIÓN DE TURNOS Y CUADRANTES
-- Fase 1 – Tablas base
-- ============================================================

CREATE TABLE IF NOT EXISTS turnos_ejercicio (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  ejercicio       INT NOT NULL UNIQUE,
  descripcion     VARCHAR(200) DEFAULT NULL,
  total_horas     DECIMAL(8,2) DEFAULT 1498.00,
  estado          ENUM('abierto','cerrado') DEFAULT 'abierto',
  fecha_creacion  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS turnos_equipo (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  codigo  VARCHAR(20)  NOT NULL UNIQUE,
  nombre  VARCHAR(100) NOT NULL,
  orden   INT DEFAULT 0,
  activo  TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS turnos_equipo_agente (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  id_equipo    INT NOT NULL,
  numagente    INT NOT NULL,
  orden        INT DEFAULT 0,
  fecha_desde  DATE DEFAULT NULL,
  fecha_hasta  DATE DEFAULT NULL,
  activo       TINYINT(1) DEFAULT 1,
  UNIQUE KEY uq_teqa (id_equipo, numagente, fecha_desde),
  CONSTRAINT fk_teqa_equipo FOREIGN KEY (id_equipo) REFERENCES turnos_equipo(id)  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_teqa_agente FOREIGN KEY (numagente)  REFERENCES agentes(numagente) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nomenclaturas / códigos de turno
CREATE TABLE IF NOT EXISTS turnos_codigo (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  codigo               VARCHAR(10)  NOT NULL UNIQUE,
  descripcion          VARCHAR(150) NOT NULL,
  color                VARCHAR(20)  DEFAULT '#cccccc',
  computa              TINYINT(1)   DEFAULT 1  COMMENT '1=Cuenta como jornada laboral',
  tipo_computo         ENUM('normal','extra','reducida','ninguno') DEFAULT 'normal',
  afecta_jornada       TINYINT(1)   DEFAULT 1,
  afecta_extra         TINYINT(1)   DEFAULT 0,
  requiere_observacion TINYINT(1)   DEFAULT 0,
  activo               TINYINT(1)   DEFAULT 1,
  orden                INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Días especiales del calendario laboral (festivos, convenio)
CREATE TABLE IF NOT EXISTS turnos_calendario_dia (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  ejercicio         INT NOT NULL,
  fecha             DATE NOT NULL,
  festivo_tipo      ENUM('nacional','local','convenio') NOT NULL,
  festivo_desc      VARCHAR(200) DEFAULT NULL,
  reduccion_minutos INT DEFAULT 0,
  observaciones     VARCHAR(500) DEFAULT NULL,
  UNIQUE KEY uq_cal_fecha (ejercicio, fecha),
  CONSTRAINT fk_cal_ejercicio FOREIGN KEY (ejercicio) REFERENCES turnos_ejercicio(ejercicio) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Periodos de reducción de jornada
CREATE TABLE IF NOT EXISTS turnos_reduccion (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  ejercicio         INT NOT NULL,
  descripcion       VARCHAR(200) NOT NULL,
  fecha_desde       DATE NOT NULL,
  fecha_hasta       DATE NOT NULL,
  reduccion_minutos INT NOT NULL,
  aplica_sabado     TINYINT(1) DEFAULT 0,
  aplica_domingo    TINYINT(1) DEFAULT 0,
  observaciones     VARCHAR(500) DEFAULT NULL,
  CONSTRAINT fk_red_ejercicio FOREIGN KEY (ejercicio) REFERENCES turnos_ejercicio(ejercicio) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cuadrante mensual (cabecera)
CREATE TABLE IF NOT EXISTS turnos_cuadrante (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  ejercicio      INT NOT NULL,
  mes            TINYINT NOT NULL COMMENT '1-12',
  estado         ENUM('borrador','cerrado','contabilizado') DEFAULT 'borrador',
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_cierre   DATETIME DEFAULT NULL,
  usuario_cierre VARCHAR(150) DEFAULT NULL,
  observaciones  TEXT DEFAULT NULL,
  UNIQUE KEY uq_cuadrante (ejercicio, mes),
  CONSTRAINT fk_cuad_ejercicio FOREIGN KEY (ejercicio) REFERENCES turnos_ejercicio(ejercicio) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cuadrante mensual (detalle por agente/día)
CREATE TABLE IF NOT EXISTS turnos_cuadrante_dia (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  id_cuadrante INT  NOT NULL,
  numagente    INT  NOT NULL,
  id_equipo    INT  NOT NULL,
  fecha        DATE NOT NULL,
  codigo       VARCHAR(10) DEFAULT NULL,
  horas        DECIMAL(4,2) DEFAULT NULL,
  es_excepcion TINYINT(1)  DEFAULT 0  COMMENT '1=Difiere del patrón del equipo',
  observaciones VARCHAR(500) DEFAULT NULL,
  UNIQUE KEY uq_cdia (id_cuadrante, numagente, fecha),
  CONSTRAINT fk_cdia_cuadrante FOREIGN KEY (id_cuadrante) REFERENCES turnos_cuadrante(id)  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cdia_agente    FOREIGN KEY (numagente)    REFERENCES agentes(numagente)      ON UPDATE CASCADE,
  CONSTRAINT fk_cdia_equipo    FOREIGN KEY (id_equipo)    REFERENCES turnos_equipo(id)       ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contabilización mensual por agente (campos calculados + manuales)
CREATE TABLE IF NOT EXISTS turnos_contabilidad_mes (
  id                      INT AUTO_INCREMENT PRIMARY KEY,
  id_cuadrante            INT NOT NULL,
  numagente               INT NOT NULL,
  -- Calculados automáticamente del cuadrante
  jornadas_mes            DECIMAL(6,2) DEFAULT 0,
  festivos_trabajados     INT          DEFAULT 0,
  fines_semana_trabajados INT          DEFAULT 0,
  vacaciones_dias         INT          DEFAULT 0,
  bajas_dias              INT          DEFAULT 0,
  permisos_dias           INT          DEFAULT 0,
  formacion_dias          INT          DEFAULT 0,
  horas_reduccion         DECIMAL(5,2) DEFAULT 0,
  p01_jornadas            DECIMAL(6,2) DEFAULT 0,
  p040_jornadas           DECIMAL(6,2) DEFAULT 0,
  -- Campos manuales (sin fórmula en el Excel)
  extras_horas            DECIMAL(5,2) DEFAULT 0,
  descuentos              DECIMAL(5,2) DEFAULT 0,
  ajuste_manual           DECIMAL(5,2) DEFAULT 0,
  observaciones           TEXT DEFAULT NULL,
  -- Trazabilidad
  calculado_en            DATETIME DEFAULT NULL,
  editado_en              DATETIME DEFAULT NULL,
  editado_por             VARCHAR(150) DEFAULT NULL,
  UNIQUE KEY uq_contab (id_cuadrante, numagente),
  CONSTRAINT fk_contab_cuadrante FOREIGN KEY (id_cuadrante) REFERENCES turnos_cuadrante(id) ON UPDATE CASCADE,
  CONSTRAINT fk_contab_agente    FOREIGN KEY (numagente)    REFERENCES agentes(numagente)    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Servicios extraordinarios vinculados al cuadrante
CREATE TABLE IF NOT EXISTS turnos_extraordinarios (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  numagente      INT NOT NULL,
  fecha          DATE NOT NULL,
  horas          DECIMAL(4,2) DEFAULT NULL,
  origen         ENUM('cuadrante','tablon','horas_sueltas') DEFAULT 'cuadrante',
  descripcion    VARCHAR(500) DEFAULT NULL,
  estado         ENUM('pendiente','revisado','consolidado') DEFAULT 'pendiente',
  id_cuadrante   INT DEFAULT NULL,
  observaciones  TEXT DEFAULT NULL,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ext_agente    FOREIGN KEY (numagente)    REFERENCES agentes(numagente)     ON UPDATE CASCADE,
  CONSTRAINT fk_ext_cuadrante FOREIGN KEY (id_cuadrante) REFERENCES turnos_cuadrante(id)  ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Auditoría de cambios en el cuadrante
CREATE TABLE IF NOT EXISTS turnos_auditoria (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  fecha_hora     DATETIME DEFAULT CURRENT_TIMESTAMP,
  usuario        VARCHAR(150) DEFAULT NULL,
  entidad        VARCHAR(50)  DEFAULT NULL,
  id_entidad     INT          DEFAULT NULL,
  campo          VARCHAR(100) DEFAULT NULL,
  valor_anterior TEXT DEFAULT NULL,
  valor_nuevo    TEXT DEFAULT NULL,
  observaciones  VARCHAR(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DATOS INICIALES: EJERCICIO 2026
-- ============================================================
INSERT IGNORE INTO turnos_ejercicio (ejercicio, descripcion, total_horas, estado)
VALUES (2026, 'Ejercicio 2026', 1498.00, 'abierto');

-- ============================================================
-- DATOS INICIALES: EQUIPOS (del Excel Cuadrantes 2026)
-- ============================================================
INSERT IGNORE INTO turnos_equipo (codigo, nombre, orden, activo) VALUES
('EQ1', 'Equipo 1', 1, 1),
('EQ2', 'Equipo 2', 2, 1),
('EQ3', 'Equipo 3', 3, 1),
('EQ4', 'Equipo 4', 4, 1);

-- ============================================================
-- DATOS INICIALES: ASIGNACIÓN AGENTES → EQUIPOS (2026)
-- Solo se insertan agentes que existan en la tabla agentes
-- ============================================================
INSERT IGNORE INTO turnos_equipo_agente (id_equipo, numagente, orden, fecha_desde, activo)
SELECT eq.id, a.numagente,
  CASE a.numagente WHEN 34 THEN 1 WHEN 9 THEN 2 WHEN 24 THEN 3 WHEN 50 THEN 4 WHEN 42 THEN 5 ELSE 99 END,
  '2026-01-01', 1
FROM turnos_equipo eq CROSS JOIN agentes a
WHERE eq.codigo = 'EQ1' AND a.numagente IN (34, 9, 24, 50, 42);

INSERT IGNORE INTO turnos_equipo_agente (id_equipo, numagente, orden, fecha_desde, activo)
SELECT eq.id, a.numagente,
  CASE a.numagente WHEN 57 THEN 1 WHEN 65 THEN 2 WHEN 67 THEN 3 WHEN 68 THEN 4 WHEN 27 THEN 5 ELSE 99 END,
  '2026-01-01', 1
FROM turnos_equipo eq CROSS JOIN agentes a
WHERE eq.codigo = 'EQ2' AND a.numagente IN (57, 65, 67, 68, 27);

INSERT IGNORE INTO turnos_equipo_agente (id_equipo, numagente, orden, fecha_desde, activo)
SELECT eq.id, a.numagente,
  CASE a.numagente WHEN 48 THEN 1 WHEN 44 THEN 2 WHEN 70 THEN 3 WHEN 72 THEN 4 WHEN 54 THEN 5 ELSE 99 END,
  '2026-01-01', 1
FROM turnos_equipo eq CROSS JOIN agentes a
WHERE eq.codigo = 'EQ3' AND a.numagente IN (48, 44, 70, 72, 54);

INSERT IGNORE INTO turnos_equipo_agente (id_equipo, numagente, orden, fecha_desde, activo)
SELECT eq.id, a.numagente,
  CASE a.numagente WHEN 26 THEN 1 WHEN 43 THEN 2 ELSE 99 END,
  '2026-01-01', 1
FROM turnos_equipo eq CROSS JOIN agentes a
WHERE eq.codigo = 'EQ4' AND a.numagente IN (26, 43);

-- ============================================================
-- DATOS INICIALES: CÓDIGOS / NOMENCLATURAS
-- (del Excel NOTAS y documento Notas sobre nomenclaturas)
-- ============================================================
INSERT IGNORE INTO turnos_codigo (codigo, descripcion, color, computa, tipo_computo, afecta_jornada, afecta_extra, requiere_observacion, activo, orden) VALUES
-- Turnos de servicio normales
('M',   'Mañana',                      '#28a745', 1, 'normal',   1, 0, 0, 1,  1),
('T',   'Tarde',                       '#fd7e14', 1, 'normal',   1, 0, 0, 1,  2),
('N',   'Noche',                       '#004085', 1, 'normal',   1, 0, 0, 1,  3),
-- Turnos con reducción de jornada
('m',   'Mañana (reducida)',           '#82c91e', 1, 'reducida', 1, 0, 0, 1,  4),
('t',   'Tarde (reducida)',            '#ffa94d', 1, 'reducida', 1, 0, 0, 1,  5),
('n',   'Noche (reducida)',            '#74c0fc', 1, 'reducida', 1, 0, 0, 1,  6),
-- Jornadas extraordinarias (cómputo aparte)
('(M)', 'Mañana extraordinaria',       '#155724', 0, 'extra',    0, 1, 1, 1,  7),
('(T)', 'Tarde extraordinaria',        '#7d3200', 0, 'extra',    0, 1, 1, 1,  8),
('(N)', 'Noche extraordinaria',        '#1a237e', 0, 'extra',    0, 1, 1, 1,  9),
-- Vacaciones y bajas
('V',   'Vacaciones',                  '#ffc107', 0, 'ninguno',  0, 0, 0, 1, 10),
('B',   'Baja',                        '#dc3545', 0, 'ninguno',  0, 0, 0, 1, 11),
-- Permisos (según nomenclatura Excel)
('P',   'Permiso',                     '#6c757d', 0, 'ninguno',  0, 0, 0, 1, 12),
('Pa',  'Permiso Particular (no suma)','#adb5bd', 0, 'ninguno',  0, 0, 0, 1, 13),
('Pas', 'Permiso Particular (saldo)',  '#868e96', 0, 'ninguno',  0, 0, 0, 1, 14),
('Pc',  'Permiso Compensación',        '#6f42c1', 0, 'ninguno',  0, 0, 0, 1, 15),
('Ps',  'Permiso Sindical',            '#20c997', 0, 'ninguno',  0, 0, 0, 1, 16),
('Pf',  'Permiso Form.-Comp.',         '#6610f2', 0, 'ninguno',  0, 0, 0, 1, 17),
-- Formación
('F',   'Formación (agente)',          '#17a2b8', 1, 'normal',   1, 0, 0, 1, 18),
('Fj',  'Formación (jefatura)',        '#0c7287', 1, 'normal',   1, 0, 0, 1, 19),
-- Otros
('E',   'Escuela / Otro organismo',    '#94d82d', 0, 'ninguno',  0, 0, 0, 1, 20),
('Jf',  'Jefatura',                    '#795548', 1, 'normal',   1, 0, 0, 1, 21),
('Sto', 'Santo / Onomástica',          '#e83e8c', 0, 'ninguno',  0, 0, 1, 1, 22);

-- ============================================================
-- DATOS INICIALES: CALENDARIO LABORAL 2026
-- Fuente: Calendario_Laboral_2026.pdf + doc Cuadrante contabilidad
-- ============================================================
INSERT IGNORE INTO turnos_calendario_dia (ejercicio, fecha, festivo_tipo, festivo_desc, reduccion_minutos) VALUES
-- Festivos nacionales
(2026, '2026-01-01', 'nacional', 'Año Nuevo',                         0),
(2026, '2026-01-06', 'nacional', 'Reyes Magos',                       0),
(2026, '2026-02-28', 'nacional', 'Día de Andalucía',                  0),
(2026, '2026-04-02', 'nacional', 'Jueves Santo',                      0),
(2026, '2026-04-03', 'nacional', 'Viernes Santo',                     0),
(2026, '2026-05-01', 'nacional', 'Día del Trabajo',                   0),
(2026, '2026-08-15', 'nacional', 'Asunción de la Virgen',             0),
(2026, '2026-10-12', 'nacional', 'Fiesta Nacional de España',         0),
(2026, '2026-11-02', 'nacional', 'Todos los Santos (traslado lunes)', 0),
(2026, '2026-12-07', 'nacional', 'Puente Inmaculada',                 0),
(2026, '2026-12-08', 'nacional', 'Inmaculada Concepción',             0),
(2026, '2026-12-24', 'nacional', 'Nochebuena',                        0),
(2026, '2026-12-25', 'nacional', 'Navidad',                           0),
(2026, '2026-12-31', 'nacional', 'Nochevieja',                        0),
-- Festivos locales (Montilla, Córdoba)
(2026, '2026-07-14', 'local',    'San Francisco Solano',              0),
(2026, '2026-09-07', 'local',    'Vendimia',                          0),
-- Días de convenio empresa
(2026, '2026-04-01', 'convenio', 'Convenio – Semana Santa',           0),
(2026, '2026-07-13', 'convenio', 'Convenio – Feria El Santo',         0);

-- ============================================================
-- DATOS INICIALES: REDUCCIONES DE JORNADA 2026
-- Fuente: Cuadrante contabilidad jornadas y descuentos 2026.doc
-- ============================================================
INSERT IGNORE INTO turnos_reduccion (ejercicio, descripcion, fecha_desde, fecha_hasta, reduccion_minutos, aplica_sabado, aplica_domingo) VALUES
(2026, 'Semana Santa 2026',      '2026-03-30', '2026-03-31', 60, 0, 0),
(2026, 'Feria El Santo 2026',    '2026-07-09', '2026-07-10', 60, 0, 0),
(2026, 'Horario de Verano 2026', '2026-06-16', '2026-09-15', 30, 0, 0);
