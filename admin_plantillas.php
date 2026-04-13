<?php include("inc/seguridad.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Plantillas</title>
    <link href="libs/summernote/dist/summernote-bs4.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table-responsive { margin-top: 20px; }
        .form-group label { font-weight: bold; color: #333; }
        .note-editor.note-frame { border: 1px solid #ddd; }
        .filtro-row { background: #f9f9f9; }
        #referenciaColumnas { background: #f0f8ff; padding: 15px; border-radius: 5px; }
        .columna-reference { padding: 8px; background: white; margin: 5px 0; border-left: 3px solid #007bff; }
    </style>
</head>
<body>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header bg-primary border-left-primary py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-cogs"></i> Administración de Plantillas</h6>
        </div>
        <div class="card-body">

            <div id="alertaContainer"></div>

            <!-- TABLA PLANTILLAS -->
            <div id="tablaPantillas" style="display:block;">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4>Gestionar Plantillas</h4>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-success btn-sm" onclick="abrirFormularioNueva()">
                            <i class="fas fa-plus"></i> Crear Plantilla
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTabla">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FORMULARIO CREAR/EDITAR -->
            <div id="formularioSection" style="display:none;" class="mt-4">

                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="m-0" id="tituloFormulario">Nueva Plantilla</h5>
                    </div>
                    <div class="card-body">

                            <!-- DATOS BÁSICOS -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Código Plantilla *</label>
                                        <input type="text" class="form-control" id="cod_plantilla" 
                                               placeholder="ej: presupuesto_1" maxlength="50" required>
                                        <small class="text-muted">Sin espacios. Ej: presupuesto_1</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre" 
                                               placeholder="Nombre de la plantilla" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Descripción</label>
                                        <textarea class="form-control" id="descripcion" rows="2" 
                                                  placeholder="Descripción breve de la plantilla"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tipo Documento</label>
                                        <input type="text" class="form-control" id="tipo_documento" 
                                               placeholder="ej: PDF, Contrato">
                                    </div>
                                </div>
                            </div>

                            <!-- CONSULTA SQL -->
                            <div class="form-group">
                                <label class="font-weight-bold">Consulta SQL *</label>
                                <small class="d-block text-muted mb-2">Usa parámetros: <code>[[nombre_filtro]]</code></small>
                                <textarea class="form-control" id="sql_consulta" rows="4" 
                                          placeholder="SELECT * FROM tabla WHERE id = [[id]]" required></textarea>
                            </div>

                            <!-- BOTÓN REFERENCIA COLUMNAS -->
                            <div class="form-group">
                                <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#referenciaColumnas">
                                    <i class="fas fa-info-circle"></i> Ver Columnas Disponibles
                                </button>
                            </div>

                            <!-- REFERENCIA COLUMNAS -->
                            <div class="collapse mb-4" id="referenciaColumnas">
                                <div class="card card-body">
                                    <h6>Columnas Disponibles (según SQL):</h6>
                                    <div id="columnasDisponibles" style="max-height: 200px; overflow-y: auto;">
                                        <small class="text-muted">Escribe SQL arriba para ver columnas disponibles</small>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTENIDO HTML CON EDITOR -->
                            <div class="form-group">
                                <label class="font-weight-bold">Contenido HTML con WYSIWYG *</label>
                                <small class="d-block text-muted mb-2">Usa variables: <code>{%%nombre_variable%%}</code></small>
                                <div id="summerNote">
                                    <textarea id="contenido" class="summernote" required></textarea>
                                </div>
                            </div>

                            <!-- ESTADO -->
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <input type="checkbox" id="estado" checked> Plantilla Activa
                                </label>
                            </div>

                            <!-- SECCIÓN: FILTROS -->
                            <hr>
                            <h5 class="mb-3">Filtros para Consulta SQL</h5>

                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered" id="tablaFiltros">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre Filtro</th>
                                            <th>Etiqueta</th>
                                            <th>Tipo</th>
                                            <th style="min-width: 300px;">Configuración</th>
                                            <th>Orden</th>
                                            <th>Requerido</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyFiltros">
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-sm btn-success mb-4" onclick="agregarFilaFiltro()">
                                <i class="fas fa-plus"></i> Agregar Filtro
                            </button>

                            <!-- BOTONES -->
                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" onclick="guardarPlantilla()">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                                <button type="button" class="btn btn-secondary mr-2" onclick="cancelarFormulario()">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<script src="libs/summernote/dist/summernote-bs4.js"></script>

<script>
const API_PLANTILLAS = 'inc/plantillas/ajax_plantillas.php';
let plantillaEnEdicion = null;

// ==========================================
// INICIALIZACIÓN
// ==========================================
$(document).ready(function() {
    cargarPlantillas();
    inicializarSummernote();
    
    // Actualizar columnas cuando cambia SQL
    $('#sql_consulta').on('input', function() {
        actualizarColumnasDisponibles();
    });
});

// ==========================================
// INICIALIZAR SUMMERNOTE
// ==========================================
function inicializarSummernote() {
    $('.summernote').summernote({
        height: 400,
        lang: 'es-ES',
        placeholder: 'Contenido en WYSIWYG con variables {%%variable%%}',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
}

// ==========================================
// CARGAR TABLA DE PLANTILLAS
// ==========================================
function cargarPlantillas() {
    $.get(API_PLANTILLAS + '?action=listar', function(data) {
        if (data.success) {
            let html = '';
            data.data.forEach(plantilla => {
                const estado = plantilla.estado == 1 ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-danger">Inactiva</span>';
                html += `<tr>
                    <td><code>${plantilla.cod_plantilla}</code></td>
                    <td>${plantilla.nombre}</td>
                    <td><small>${plantilla.descripcion || ''}</small></td>
                    <td><small>${plantilla.tipo_documento || ''}</small></td>
                    <td>${estado}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="abrirFormularioEditar('${plantilla.cod_plantilla}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPlantilla('${plantilla.cod_plantilla}', '${plantilla.nombre}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#cuerpoTabla').html(html);
        }
    });
}

// ==========================================
// ABRIR FORMULARIO NUEVA
// ==========================================
function abrirFormularioNueva() {
    plantillaEnEdicion = null;
    $('#tituloFormulario').text('Nueva Plantilla');
    $('#cod_plantilla').prop('disabled', false);
    limpiarFormulario();
    ocultarTabla();
    actualizarColumnasDisponibles();
}

// ==========================================
// ABRIR FORMULARIO EDITAR
// ==========================================
function abrirFormularioEditar(cod) {
    $.get(API_PLANTILLAS + '?action=obtener_completa&cod=' + cod, function(data) {
        if (data.success) {
            plantillaEnEdicion = cod;
            $('#tituloFormulario').text('Editar Plantilla');
            $('#cod_plantilla').prop('disabled', true);
            
            $('#cod_plantilla').val(data.data.cod_plantilla);
            $('#nombre').val(data.data.nombre);
            $('#descripcion').val(data.data.descripcion || '');
            $('#tipo_documento').val(data.data.tipo_documento || '');
            $('#sql_consulta').val(data.data.sql_consulta || '');
            $('#estado').prop('checked', data.data.estado == 1);
            
            $('.summernote').summernote('code', data.data.contenido || '');
            
            // Cargar filtros
            cargarFiltrosEnFormulario(data.data.filtros || []);
            
            ocultarTabla();
            actualizarColumnasDisponibles();
        }
    });
}

// ==========================================
// CARGAR FILTROS EN FORMULARIO
// ==========================================
function cargarFiltrosEnFormulario(filtros) {
    const tbody = $('#bodyFiltros');
    tbody.html('');
    
    filtros.forEach(filtro => {
        const rowId = 'filt_' + Date.now() + Math.random();
        let configHtml = '';
        
        if (filtro.tipo_filtro === 'select_table') {
            configHtml = `
                <input type="text" class="form-control form-control-sm mb-1 filtro-tabla" value="${filtro.tabla_datos || ''}">
                <input type="text" class="form-control form-control-sm mb-1 filtro-campo-clave" value="${filtro.campo_clave || 'id'}">
                <input type="text" class="form-control form-control-sm filtro-campo-valor" value="${filtro.campo_valor || 'nombre'}">
            `;
        } else if (filtro.tipo_filtro === 'select_sql') {
            configHtml = `<textarea class="form-control form-control-sm filtro-sql-query" rows="2">${filtro.sql_query || ''}</textarea>`;
        }
        
        const fila = `<tr id="${rowId}" class="filtro-row">
            <td><input type="text" class="form-control form-control-sm filtro-nombre" value="${filtro.nombre_filtro}"></td>
            <td><input type="text" class="form-control form-control-sm filtro-etiqueta" value="${filtro.etiqueta}"></td>
            <td>
                <select class="form-control form-control-sm filtro-tipo" onchange="actualizarConfiguracionFiltro('${rowId}')">
                    <option value="select_table" ${filtro.tipo_filtro === 'select_table' ? 'selected' : ''}>SELECT Tabla</option>
                    <option value="select_sql" ${filtro.tipo_filtro === 'select_sql' ? 'selected' : ''}>SELECT SQL</option>
                    <option value="text" ${filtro.tipo_filtro === 'text' ? 'selected' : ''}>Texto</option>
                    <option value="number" ${filtro.tipo_filtro === 'number' ? 'selected' : ''}>Número</option>
                    <option value="date" ${filtro.tipo_filtro === 'date' ? 'selected' : ''}>Fecha</option>
                </select>
            </td>
            <td id="config-${rowId}">${configHtml}</td>
            <td><input type="number" class="form-control form-control-sm filtro-orden" value="${filtro.orden || 1}" min="1"></td>
            <td><input type="checkbox" class="form-check-input filtro-requerido" ${filtro.requerido === 1 ? 'checked' : ''}></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaFiltro('${rowId}')"><i class="fas fa-trash"></i></button></td>
        </tr>`;
        
        tbody.append(fila);
    });
}

// ==========================================
// AGREGAR FILA FILTRO
// ==========================================
function agregarFilaFiltro() {
    const tbody = $('#bodyFiltros');
    const rowId = 'filtro-' + Date.now();
    
    const fila = `<tr id="${rowId}" class="filtro-row">
        <td><input type="text" class="form-control form-control-sm filtro-nombre" placeholder="año, cliente, etc." required></td>
        <td><input type="text" class="form-control form-control-sm filtro-etiqueta" placeholder="Etiqueta visible" required></td>
        <td>
            <select class="form-control form-control-sm filtro-tipo" onchange="actualizarConfiguracionFiltro('${rowId}')">
                <option value="select_table">SELECT Tabla</option>
                <option value="select_sql">SELECT SQL</option>
                <option value="text">Texto</option>
                <option value="number">Número</option>
                <option value="date">Fecha</option>
            </select>
        </td>
        <td id="config-${rowId}">
            <input type="text" class="form-control form-control-sm mb-1 filtro-tabla" placeholder="Tabla: años">
            <input type="text" class="form-control form-control-sm mb-1 filtro-campo-clave" placeholder="Clave: id" value="id">
            <input type="text" class="form-control form-control-sm filtro-campo-valor" placeholder="Valor: nombre">
        </td>
        <td><input type="number" class="form-control form-control-sm filtro-orden" value="1" min="1" required></td>
        <td><input type="checkbox" class="form-check-input filtro-requerido" checked></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaFiltro('${rowId}')"><i class="fas fa-trash"></i></button></td>
    </tr>`;
    
    tbody.append(fila);
}

// ==========================================
// ACTUALIZAR CONFIGURACIÓN FILTRO
// ==========================================
function actualizarConfiguracionFiltro(rowId) {
    const fila = $('#' + rowId);
    const tipo = fila.find('.filtro-tipo').val();
    const configDiv = $('#config-' + rowId);
    
    let html = '';
    
    switch(tipo) {
        case 'select_table':
            html = `
                <input type="text" class="form-control form-control-sm mb-1 filtro-tabla" placeholder="Tabla: años">
                <input type="text" class="form-control form-control-sm mb-1 filtro-campo-clave" placeholder="Clave: id" value="id">
                <input type="text" class="form-control form-control-sm filtro-campo-valor" placeholder="Valor: nombre">
            `;
            break;
        case 'select_sql':
            html = `<textarea class="form-control form-control-sm filtro-sql-query" placeholder="SELECT id, nombre FROM tabla" rows="2"></textarea>`;
            break;
        default:
            html = '';
    }
    
    configDiv.html(html);
}

// ==========================================
// ELIMINAR FILA FILTRO
// ==========================================
function eliminarFilaFiltro(rowId) {
    $('#' + rowId).remove();
}

// ==========================================
// OBTENER FILTROS DEL FORMULARIO
// ==========================================
function obtenerFiltros() {
    const filtros = [];
    
    $('#bodyFiltros tr').each(function() {
        const fila = $(this);
        const celdas = fila.find('td');
        
        const nombre = celdas.eq(0).find('input').val().trim();
        const etiqueta = celdas.eq(1).find('input').val().trim();
        const tipo = celdas.eq(2).find('select').val();
        const orden = parseInt(celdas.eq(4).find('input').val()) || 1;
        const requerido = celdas.eq(5).find('input[type="checkbox"]').is(':checked') ? 1 : 0;
        
        if (!nombre || !etiqueta) return;
        
        const filtro = {
            nombre_filtro: nombre,
            etiqueta: etiqueta,
            tipo_filtro: tipo,
            orden: orden,
            requerido: requerido
        };
        
        if (tipo === 'select_table') {
            filtro.tabla_datos = celdas.eq(3).find('.filtro-tabla').val();
            filtro.campo_clave = celdas.eq(3).find('.filtro-campo-clave').val() || 'id';
            filtro.campo_valor = celdas.eq(3).find('.filtro-campo-valor').val() || 'nombre';
        } else if (tipo === 'select_sql') {
            filtro.sql_query = celdas.eq(3).find('.filtro-sql-query').val();
        }
        
        filtros.push(filtro);
    });
    
    return filtros;
}

// ==========================================
// ACTUALIZAR COLUMNAS DISPONIBLES
// ==========================================
function actualizarColumnasDisponibles() {
    const sql = $('#sql_consulta').val().trim();
    const container = $('#columnasDisponibles');
    
    if (!sql) {
        container.html('<small class="text-muted">Escribe SQL para ver columnas</small>');
        return;
    }
    
    const regex = /SELECT\s+(.*?)\s+FROM/i;
    const match = sql.match(regex);
    
    if (match) {
        let columns = match[1];
        if (columns === '*') {
            container.html('<small class="text-warning">Selecciona todas las columnas (*). Especifica las columnas para ver referencias.</small>');
        } else {
            const cols = columns.split(',').map(c => c.trim());
            let html = '';
            cols.forEach(col => {
                const columna = col.split(' ').pop();
                html += `<div class="columna-reference"><code>{%%${columna}%%}</code></div>`;
            });
            container.html(html);
        }
    } else {
        container.html('<small class="text-danger">SQL no válido. Debe ser: SELECT columnas FROM tabla</small>');
    }
}

// ==========================================
// GUARDAR PLANTILLA
// ==========================================
function guardarPlantilla() {
    const cod = $('#cod_plantilla').val().trim();
    const nombre = $('#nombre').val().trim();
    const contenido = $('.summernote').summernote('code');
    const sql = $('#sql_consulta').val().trim();
    
    if (!cod || !nombre || !contenido || !sql) {
        mostrarAlerta('Todos los campos requeridos deben estar completos', 'warning');
        return;
    }
    
    const datos = {
        cod_plantilla: cod,
        nombre: nombre,
        descripcion: $('#descripcion').val(),
        tipo_documento: $('#tipo_documento').val(),
        sql_consulta: sql,
        contenido: contenido,
        estado: $('#estado').is(':checked') ? 1 : 0,
        filtros: obtenerFiltros()
    };
    
    const action = plantillaEnEdicion ? 'editar&cod=' + plantillaEnEdicion : 'crear';
    
    $.ajax({
        url: API_PLANTILLAS + '?action=' + action,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        success: function(data) {
            if (data.success || data.validacion === 'ok') {
                mostrarAlerta('Plantilla guardada correctamente', 'success');
                limpiarFormulario();
                mostrarTabla();
                cargarPlantillas();
            } else {
                mostrarAlerta(data.error || 'Error al guardar', 'danger');
            }
        },
        error: function(err) {
            mostrarAlerta('Error al guardar plantilla', 'danger');
            console.error(err);
        }
    });
}

// ==========================================
// ELIMINAR PLANTILLA
// ==========================================
function eliminarPlantilla(cod, nombre) {
    if (!confirm('¿Confirmas que quieres eliminar esta plantilla?')) return;
    
    $.post(API_PLANTILLAS + '?action=eliminar&cod_plantilla=' + cod, function(data) {
        if (data.success || data.validacion === 'ok') {
            mostrarAlerta('Plantilla eliminada correctamente', 'success');
            cargarPlantillas();
        } else {
            mostrarAlerta(data.error || 'Error al eliminar', 'danger');
        }
    });
}

// ==========================================
// LIMPIAR FORMULARIO
// ==========================================
function limpiarFormulario() {
    $('#cod_plantilla').val('');
    $('#nombre').val('');
    $('#descripcion').val('');
    $('#tipo_documento').val('');
    $('#sql_consulta').val('SELECT * FROM tabla WHERE id = [[id]]');
    $('#estado').prop('checked', true);
    $('.summernote').summernote('code', '');
    $('#bodyFiltros').html('');
    plantillaEnEdicion = null;
}

// ==========================================
// CANCELAR EDICIÓN
// ==========================================
function cancelarFormulario() {
    limpiarFormulario();
    mostrarTabla();
}

// ==========================================
// MOSTRAR/OCULTAR TABLA
// ==========================================
function ocultarTabla() {
    $('#tablaPantillas').hide();
    $('#formularioSection').show();
}

function mostrarTabla() {
    $('#tablaPantillas').show();
    $('#formularioSection').hide();
}

// ==========================================
// MOSTRAR ALERTA
// ==========================================
function mostrarAlerta(mensaje, tipo) {
    const alert = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${mensaje}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>`;
    
    $('#alertaContainer').html(alert);
    
    setTimeout(() => {
        $('#alertaContainer').html('');
    }, 5000);
}

</script>

</body>
</html>
