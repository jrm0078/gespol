-- ============================================
-- DATOS DE PRUEBA - USUARIOS
-- ============================================

-- Usuarios de prueba adicionales (password: test123)
INSERT INTO usuario(nombre, email, contrasenia, rol, activo) 
VALUES('María García López', 'maria.garcia@gespol.es', '$2y$10$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2', 'Admin', 1)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), rol=VALUES(rol), activo=VALUES(activo);

INSERT INTO usuario(nombre, email, contrasenia, rol, activo) 
VALUES('Carlos Rodríguez Martín', 'carlos.rodriguez@gespol.es', '$2y$10$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2', 'Usuario', 1)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), rol=VALUES(rol), activo=VALUES(activo);

INSERT INTO usuario(nombre, email, contrasenia, rol, activo) 
VALUES('Laura Sánchez Pérez', 'laura.sanchez@gespol.es', '$2y$10$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2', 'Usuario', 0)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), rol=VALUES(rol), activo=VALUES(activo);

-- ============================================
-- PLANTILLA COMPLETA DE PRUEBA: INFORME DE USUARIO
-- ============================================

-- Primero borramos si ya existe para hacer limpia
DELETE FROM plantillas_filtros  WHERE cod_plantilla = 'informe_usuario';
DELETE FROM plantillas_maestro  WHERE cod_plantilla = 'informe_usuario';

INSERT INTO plantillas_maestro 
  (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado)
VALUES (
  'informe_usuario',
  'Informe de Usuario',
  'Informe detallado con los datos de un usuario del sistema',
  'PDF',
'<div style="font-family: Arial, sans-serif; padding: 30px; color: #333;">

  <!-- CABECERA -->
  <table width="100%" style="margin-bottom: 30px;">
    <tr>
      <td style="width:70%;">
        <h1 style="color: #0084D9; margin: 0; font-size: 24px;">GESPOL</h1>
        <p style="color: #666; margin: 4px 0 0 0; font-size: 12px;">Sistema de Gestión Policial</p>
      </td>
      <td style="text-align:right; vertical-align:top;">
        <span style="background:#0084D9; color:white; padding:6px 14px; border-radius:4px; font-size:13px; font-weight:bold;">INFORME DE USUARIO</span>
        <p style="color:#999; font-size:11px; margin:6px 0 0 0;">Generado: [[fecha_generacion]]</p>
      </td>
    </tr>
  </table>

  <!-- LÍNEA SEPARADORA -->
  <hr style="border: none; border-top: 3px solid #0084D9; margin-bottom: 25px;">

  <!-- DATOS PRINCIPALES -->
  <h3 style="color: #0084D9; font-size: 15px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
    Datos del Usuario
  </h3>

  <table width="100%" cellpadding="8" style="border-collapse: collapse; margin-bottom: 25px; font-size: 13px;">
    <tr style="background: #0084D9; color: white;">
      <th style="padding: 10px 14px; text-align:left; width:35%;">Campo</th>
      <th style="padding: 10px 14px; text-align:left;">Valor</th>
    </tr>
    <tr style="background: #f8fbff;">
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px; font-weight: bold;">Identificador</td>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px;">[[id]]</td>
    </tr>
    <tr>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px; font-weight: bold;">Nombre Completo</td>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px;">[[nombre]]</td>
    </tr>
    <tr style="background: #f8fbff;">
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px; font-weight: bold;">Correo Electrónico</td>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px;">[[email]]</td>
    </tr>
    <tr>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px; font-weight: bold;">Rol Asignado</td>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px;">
        <span style="background:#e8f4fd; color:#0084D9; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:bold;">[[rol]]</span>
      </td>
    </tr>
    <tr style="background: #f8fbff;">
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px; font-weight: bold;">Estado de la Cuenta</td>
      <td style="border: 1px solid #d0e4f5; padding: 9px 14px;">[[estado_texto]]</td>
    </tr>
  </table>

  <!-- OBSERVACIONES -->
  <h3 style="color: #0084D9; font-size: 15px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
    Observaciones
  </h3>
  <div style="background: #f8fbff; border-left: 4px solid #0084D9; padding: 14px 18px; font-size: 13px; color: #555; border-radius: 0 4px 4px 0;">
    Este informe ha sido generado automáticamente por el sistema GESPOL el día [[fecha_generacion]].
    Los datos corresponden al estado actual del registro en la base de datos.
    Cualquier modificación posterior no quedará reflejada en este documento.
  </div>

  <!-- PIE DE PÁGINA -->
  <hr style="border: none; border-top: 1px solid #ddd; margin-top: 40px; margin-bottom: 10px;">
  <table width="100%" style="font-size: 11px; color: #aaa;">
    <tr>
      <td>GESPOL — Sistema de Gestión</td>
      <td style="text-align:right;">Documento generado el [[fecha_generacion]] — Confidencial</td>
    </tr>
  </table>

</div>',
'SELECT 
  id,
  nombre,
  email,
  rol,
  IF(activo = 1, ''✓ Activo'', ''✗ Inactivo'') AS estado_texto,
  DATE_FORMAT(NOW(), ''%d/%m/%Y a las %H:%i'') AS fecha_generacion
FROM usuario
WHERE id = ?',
1
);

-- Filtro: desplegable con todos los usuarios
INSERT INTO plantillas_filtros 
  (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, campo_clave, campo_valor, sql_query, orden, requerido, activo)
VALUES 
  ('informe_usuario', 'id', 'Seleccionar Usuario', 'select_table', 'usuario', 'id', 'nombre', NULL, 1, 1, 1);


-- ============================================
-- DATOS DE PRUEBA - PLANTILLAS
-- ============================================

-- Plantilla 1: Presupuesto
INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, estado) 
VALUES(
    'presupuesto_demo',
    'Presupuesto Demo',
    'Plantilla de presupuesto para demostración',
    'PDF',
    '<div style="font-family: Arial, sans-serif; padding: 20px;">
        <h1 style="color: #0084D9; border-bottom: 3px solid #0084D9; padding-bottom: 10px;">PRESUPUESTO</h1>
        <div style="margin-top: 30px;">
            <p><strong>Cliente:</strong> {%%cliente%%}</p>
            <p><strong>Fecha de Emisión:</strong> {%%fecha%%}</p>
            <p><strong>Descripción del Servicio:</strong> {%%descripcion%%}</p>
            <p><strong>Monto Total:</strong> ${%%monto%%}</p>
            <p><strong>Validez:</strong> {%%validez%%} días</p>
        </div>
        <div style="margin-top: 40px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
            <p><strong>Términos y Condiciones:</strong></p>
            <p>Este presupuesto es válido por el período especificado. Los términos pueden cambiar según disponibilidad.</p>
        </div>
    </div>',
    1
)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Plantilla 2: Contrato
INSERT INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, estado) 
VALUES(
    'contrato_demo',
    'Contrato Demo',
    'Plantilla de contrato básico para demostración',
    'PDF',
    '<div style="font-family: Arial, sans-serif; padding: 20px;">
        <h1 style="color: #0084D9; text-align: center; border-bottom: 3px solid #0084D9; padding-bottom: 10px;">ACUERDO DE SERVICIO</h1>
        <div style="margin-top: 30px;">
            <p><strong>Fecha:</strong> {%%fecha%%}</p>
            <p><strong>Entre:</strong> {%%empresa%%}</p>
            <p><strong>Y:</strong> {%%cliente%%}</p>
        </div>
        <div style="margin-top: 30px;">
            <h3 style="color: #0084D9;">1. Servicios</h3>
            <p>{%%servicios%%}</p>
            
            <h3 style="color: #0084D9;">2. Tarifa</h3>
            <p>Se acuerda una tarifa de ${%%tarifa%%} por los servicios especificados.</p>
            
            <h3 style="color: #0084D9;">3. Duración</h3>
            <p>Este contrato tendrá una duración de {%%duracion%%} meses desde la fecha de firma.</p>
        </div>
        <div style="margin-top: 40px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
            <p>Firmado por las partes el día {%%fecha_firma%%}</p>
        </div>
    </div>',
    1
)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- ============================================
-- FILTROS PARA PLANTILLA PRESUPUESTO
-- ============================================

INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, operador, orden, requerido, activo)
VALUES
('presupuesto_demo', 'cliente', 'Cliente', 'text', '=', 1, 1, 1),
('presupuesto_demo', 'fecha', 'Fecha de Emisión', 'date', '=', 2, 1, 1),
('presupuesto_demo', 'descripcion', 'Descripción', 'text', 'LIKE', 3, 1, 1),
('presupuesto_demo', 'monto', 'Monto ($)', 'number', '=', 4, 1, 1),
('presupuesto_demo', 'validez', 'Días de Validez', 'number', '=', 5, 0, 1)
ON DUPLICATE KEY UPDATE etiqueta=VALUES(etiqueta);

-- ============================================
-- FILTROS PARA PLANTILLA CONTRATO
-- ============================================

INSERT INTO plantillas_filtros (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, operador, orden, requerido, activo)
VALUES
('contrato_demo', 'fecha', 'Fecha de Emisión', 'date', '=', 1, 1, 1),
('contrato_demo', 'empresa', 'Empresa', 'text', '=', 2, 1, 1),
('contrato_demo', 'cliente', 'Cliente', 'text', '=', 3, 1, 1),
('contrato_demo', 'servicios', 'Servicios', 'text', 'LIKE', 4, 1, 1),
('contrato_demo', 'tarifa', 'Tarifa ($)', 'number', '=', 5, 1, 1),
('contrato_demo', 'duracion', 'Duración (meses)', 'number', '=', 6, 1, 1),
('contrato_demo', 'fecha_firma', 'Fecha de Firma', 'date', '=', 7, 0, 1)
ON DUPLICATE KEY UPDATE etiqueta=VALUES(etiqueta);
