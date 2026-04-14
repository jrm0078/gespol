-- ============================================
-- DATOS DE PRUEBA - USUARIOS
-- ============================================

-- Usuario de prueba (password: test123)
INSERT INTO usuario(nombre, email, contrasenia, rol, activo) 
VALUES('Usuario Prueba', 'prueba@test.com', '$2y$10$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2', 'Superadmin', 1)
ON DUPLICATE KEY UPDATE email=VALUES(email);

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
