<?php include("inc/seguridad.php"); ?>

<style>
    .filtro-row { background: #f9f9f9; }
    #referenciaColumnas { background: #e3f2fd; padding: 15px; border-radius: 5px; border-left: 4px solid #0084D9; }
    .columna-reference { padding: 8px; background: white; margin: 5px 0; border-left: 3px solid #0084D9; }
    
    /* Responsive TinyMCE y Editor */
    .tox-tinymce {
        max-width: 100% !important;
        border: 1px solid #bee5eb !important;
    }
    
    .card-header {
        border-bottom: 3px solid #0084D9 !important;
        background: linear-gradient(to right, rgba(0, 132, 217, 0.05), transparent) !important;
    }
    
    /* Container responsivo */
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Formulario responsive */
    .row.mb-3 {
        row-gap: 0.5rem;
    }
    
    /* Tabla responsive */
    #tablaPlantillas {
        margin-bottom: 0;
    }
    
    /* Tabla hover color azul */
    #tablaPlantillas tbody tr:hover {
        background-color: rgba(0, 132, 217, 0.05) !important;
    }
    
    /* Acentos azules */
    .form-control:focus,
    .form-select:focus {
        border-color: #0084D9 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 132, 217, 0.25) !important;
    }
    
    /* Responsive table */
    @media (max-width: 768px) {
        #tablaPlantillas thead {
            font-size: 0.85rem;
        }
        #tablaPlantillas td, #tablaPlantillas th {
            padding: 0.4rem !important;
            font-size: 0.9rem;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        .card-body {
            padding: 1rem;
        }
        h4 {
            font-size: 1.25rem;
        }
        h5 {
            font-size: 1.1rem;
        }
    }
    
    /* Full width inputs en mobile */
    @media (max-width: 576px) {
        .form-control, .form-select, textarea, input {
            font-size: 16px !important; /* Evita zoom en iOS */
        }
        .btn {
            margin-bottom: 0.5rem;
        }
        .table-responsive {
            margin-bottom: 1rem;
        }
    }
</style>

<!-- TABLA PLANTILLAS -->
<div id="tablaPantillas" style="display:block;">
    <div id="alertaPlantillasContainer"></div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-2" style="background: linear-gradient(to right, #0084D9, #0066B3); border-bottom: 3px solid #005fa3;">
            <h5 class="m-0 text-white"><i class="mdi mdi-file-document mr-2"></i>Gestionar Plantillas</h5>
            <button class="btn btn-sm font-weight-bold" onclick="abrirFormularioPlantillasNueva()" style="background:#fff;color:#0066B3;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                <i class="fas fa-plus"></i> Crear Plantilla
            </button>
        </div>
        <div class="card-body p-2">
    <div class="table-responsive">
        <table id="tablaPlantillas" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th style="min-width:100px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaPlantillas">
            </tbody>
        </table>
    </div>
        </div>
    </div>
</div>

<!-- FORMULARIO CREAR/EDITAR -->
<div id="formularioPlantillasSection" style="display:none;" class="mt-4">
    <div class="card">
        <div class="card-header" style="background: linear-gradient(to right, #0084D9, rgba(0, 132, 217, 0.7)); border-bottom: 3px solid #0084D9; color: white;">
            <h5 class="m-0" id="tituloFormularioPlantillas">Nueva Plantilla</h5>
        </div>
        <div class="card-body">
            <div id="alertaPlantillasForm"></div>

            <!-- DATOS BÁSICOS -->
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Código Plantilla *</label>
                        <input type="text" class="form-control" id="cod_plantilla_form" 
                               placeholder="ej: presupuesto_1" maxlength="50" required>
                        <div class="invalid-feedback" id="cod_error">Este código ya existe.</div>
                        <small class="text-muted">Sin espacios. Ej: presupuesto_1</small>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre *</label>
                        <input type="text" class="form-control" id="nombre_form" 
                               placeholder="Nombre de la plantilla" required>
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label class="font-weight-bold">Descripción</label>
                        <textarea class="form-control" id="descripcion_form" rows="2" 
                                  placeholder="Descripción breve de la plantilla"></textarea>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo Documento</label>
                        <input type="text" class="form-control" id="tipo_documento_form" 
                               placeholder="ej: PDF, Contrato">
                    </div>
                </div>
            </div>

            <!-- CONSULTA SQL -->
            <div class="form-group">
                <label class="font-weight-bold">Consulta SQL *</label>
                <small class="d-block text-muted mb-2">Usa parámetros: <code>[[nombre_filtro]]</code></small>
                <textarea class="form-control" id="sql_consulta_form" rows="4" 
                          placeholder="SELECT * FROM tabla WHERE id = [[id]]" required></textarea>
            </div>

            <!-- BOTÓN REFERENCIA COLUMNAS -->
            <div class="form-group">
                <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#referenciaColumnasPlantillas">
                    <i class="fas fa-info-circle"></i> Ver Columnas Disponibles
                </button>
            </div>

            <!-- REFERENCIA COLUMNAS -->
            <div class="collapse mb-4" id="referenciaColumnasPlantillas">
                <div class="card card-body">
                    <h6>Columnas Disponibles (según SQL):</h6>
                    <div id="columnasDisponiblesPlantillas" style="max-height: 200px; overflow-y: auto;">
                        <small class="text-muted">Escribe SQL arriba para ver columnas disponibles</small>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO HTML -->
            <div class="form-group">
                <label class="font-weight-bold">Contenido HTML *</label>
                <textarea id="contenido_form"></textarea>
            </div>

            <!-- ESTADO -->
            <div class="form-group">
                <label class="font-weight-bold">
                    <input type="checkbox" id="estado_form" checked> Plantilla Activa
                </label>
            </div>

            <!-- SECCIÓN: FILTROS -->
            <hr>
            <h5 class="mb-3">Filtros para Consulta SQL</h5>

            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered" id="tablaFiltrosPlantillas">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Filtro</th>
                            <th>Etiqueta</th>
                            <th>Tipo</th>
                            <th style="min-width: 300px;">Configuración</th>
                            <th>Orden</th>
                            <th class="text-center">Requerido</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyFiltrosPlantillas">
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-sm btn-success mb-4" onclick="agregarFilaFiltroPlantillas()">
                <i class="fas fa-plus"></i> Agregar Filtro
            </button>

            <!-- BOTONES -->
            <div class="mt-4 row g-2">
                <div class="col-12 col-sm-6 col-md-auto">
                    <button type="button" class="btn btn-primary w-100" onclick="guardarPlantillaForm()">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
                <div class="col-12 col-sm-6 col-md-auto">
                    <button type="button" class="btn btn-secondary w-100" onclick="cancelarFormularioPlantillas()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Usar var para permitir redeclaración cuando se recarga el módulo
var APIPantillas = 'inc/plantillas/ajax_plantillas.php';
var plantillaEnEdicionForm = null;

// Inicializar TinyMCE para formulario
function inicializarTinyMCEForm() {
    // Destruir instancia anterior si existe
    if (tinymce.get('contenido_form')) {
        tinymce.get('contenido_form').remove();
    }
    
    // Detectar altura según pantalla
    let altura = 600;
    if (window.innerWidth < 768) {
        altura = 350;
    }
    
    tinymce.init({
        selector: '#contenido_form',
        language: 'es',
        height: altura,
        menubar: 'file edit view insert format tools',
        plugins: 'advlist autolink lists link image charmap anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen | table',
        branding: false,
        statusbar: false,
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        entity_encoding: 'raw',
        placeholder: 'Contenido HTML'
    });
}

// Cargar plantillas al iniciar
function cargarPlantillasListado() {
    $.ajax({
        url: APIPantillas + '?action=listar',
        type: 'GET',
        dataType: 'text',
        success: function(rawText) {
            var data;
            try {
                data = JSON.parse(rawText);
            } catch(e) {
                $('#alertaPlantillasContainer').html('<div class="alert alert-danger">Error JSON - Respuesta recibida:<br><pre style="font-size:11px;max-height:150px;overflow:auto">' + $('<div>').text(rawText).html() + '</pre></div>');
                return;
            }
            if (data.success) {
                var html = '';
                data.data.forEach(function(plantilla) {
                    var estado = plantilla.estado == 1 ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-danger">Inactiva</span>';
                    html += '<tr>'
                        + '<td><code>' + plantilla.cod_plantilla + '</code></td>'
                        + '<td>' + plantilla.nombre + '</td>'
                        + '<td><small>' + (plantilla.descripcion || '') + '</small></td>'
                        + '<td><small>' + (plantilla.tipo_documento || '') + '</small></td>'
                        + '<td>' + estado + '</td>'
                        + '<td>'
                        + '<button class="btn btn-sm btn-primary mr-1" onclick="abrirFormularioPlantillasEditar(\'' + plantilla.cod_plantilla + '\')"><i class="fas fa-edit"></i></button>'
                        + '<button class="btn btn-sm btn-danger" onclick="eliminarPlantillaForm(\'' + plantilla.cod_plantilla + '\',\'' + plantilla.nombre + '\')"><i class="fas fa-trash"></i></button>'
                        + '</td>'
                        + '</tr>';
                });
                $('#cuerpoTablaPlantillas').html(html || `
                    <tr><td colspan="6">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-alt" style="font-size:2.5rem;opacity:0.25;"></i>
                            <p class="mt-3 mb-1 font-weight-bold">No hay plantillas creadas</p>
                            <small>Haz clic en <b>Crear Plantilla</b> para añadir la primera</small>
                        </div>
                    </td></tr>`);
                initDataTablePlantillas();
            } else {
                $('#alertaPlantillasContainer').html('<div class="alert alert-danger">Error del servidor: ' + (data.error || 'desconocido') + '</div>');
            }
        },
        error: function(xhr, status, err) {
            $('#alertaPlantillasContainer').html('<div class="alert alert-danger">Error HTTP ' + xhr.status + ': ' + err + '</div>');
        }
    });
}

// Abrir formulario NUEVA plantilla
function abrirFormularioPlantillasNueva() {
    plantillaEnEdicionForm = null;
    window._editandoCod = false;
    $('#tituloFormularioPlantillas').text('Nueva Plantilla');
    $('#cod_plantilla_form').prop('disabled', false);
    limpiarFormularioPlantillas();
    ocultarTablaPlantillas();
    setTimeout(() => inicializarTinyMCEForm(), 100);
    actualizarColumnasPlantillas();
}

// Abrir formulario EDITAR plantilla
function abrirFormularioPlantillasEditar(cod) {
    window._editandoCod = true;
    $.get(APIPantillas + '?action=obtener_completa&cod=' + cod, function(data) {
        if (data.success) {
            plantillaEnEdicionForm = cod;
            $('#tituloFormularioPlantillas').text('Editar Plantilla');
            $('#cod_plantilla_form').prop('disabled', true);
            
            $('#cod_plantilla_form').val(data.data.cod_plantilla);
            $('#nombre_form').val(data.data.nombre);
            $('#descripcion_form').val(data.data.descripcion || '');
            $('#tipo_documento_form').val(data.data.tipo_documento || '');
            $('#sql_consulta_form').val(data.data.sql_consulta || '');
            $('#estado_form').prop('checked', data.data.estado == 1);
            
            setTimeout(function() {
                inicializarTinyMCEForm();
                // Cargar contenido después de que TinyMCE esté listo
                setTimeout(function() {
                    tinymce.get('contenido_form').setContent(data.data.contenido || '');
                }, 200);
            }, 100);
            
            cargarFiltrosPlantillas(data.data.filtros || []);
            
            ocultarTablaPlantillas();
            actualizarColumnasPlantillas();
        }
    });
}

// Cargar filtros en formulario
function cargarFiltrosPlantillas(filtros) {
    const tbody = $('#bodyFiltrosPlantillas');
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
                <select class="form-control form-control-sm filtro-tipo" onchange="actualizarConfigFiltroPlantillas('${rowId}')">
                    <option value="select_table" ${filtro.tipo_filtro === 'select_table' ? 'selected' : ''}>SELECT Tabla</option>
                    <option value="select_sql" ${filtro.tipo_filtro === 'select_sql' ? 'selected' : ''}>SELECT SQL</option>
                    <option value="text" ${filtro.tipo_filtro === 'text' ? 'selected' : ''}>Texto</option>
                    <option value="number" ${filtro.tipo_filtro === 'number' ? 'selected' : ''}>Número</option>
                    <option value="date" ${filtro.tipo_filtro === 'date' ? 'selected' : ''}>Fecha</option>
                </select>
            </td>
            <td id="config-${rowId}">${configHtml}</td>
            <td><input type="number" class="form-control form-control-sm filtro-orden" value="${filtro.orden || 1}" min="1"></td>
            <td class="text-center align-middle"><input type="checkbox" class="filtro-requerido" style="width:18px;height:18px;cursor:pointer;" ${filtro.requerido === 1 ? 'checked' : ''}></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaFiltroPlantillas('${rowId}')"><i class="fas fa-trash"></i></button></td>
        </tr>`;
        
        tbody.append(fila);
    });
}

// Agregar fila de filtro
function agregarFilaFiltroPlantillas() {
    const tbody = $('#bodyFiltrosPlantillas');
    const rowId = 'filtro-' + Date.now();
    
    const fila = `<tr id="${rowId}" class="filtro-row">
        <td><input type="text" class="form-control form-control-sm filtro-nombre" placeholder="año, cliente, etc." required></td>
        <td><input type="text" class="form-control form-control-sm filtro-etiqueta" placeholder="Etiqueta visible" required></td>
        <td>
            <select class="form-control form-control-sm filtro-tipo" onchange="actualizarConfigFiltroPlantillas('${rowId}')">
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
        <td class="text-center align-middle"><input type="checkbox" class="filtro-requerido" style="width:18px;height:18px;cursor:pointer;" checked></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFilaFiltroPlantillas('${rowId}')"><i class="fas fa-trash"></i></button></td>
    </tr>`;
    
    tbody.append(fila);
}

// Actualizar configuración del filtro según tipo
function actualizarConfigFiltroPlantillas(rowId) {
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

// Eliminar fila de filtro
function eliminarFilaFiltroPlantillas(rowId) {
    $('#' + rowId).remove();
}

// Obtener filtros del formulario
function obtenerFiltrosPlantillas() {
    const filtros = [];
    
    $('#bodyFiltrosPlantillas tr').each(function() {
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

// Actualizar columnas disponibles según SQL
function actualizarColumnasPlantillas() {
    const sql = $('#sql_consulta_form').val().trim();
    const container = $('#columnasDisponiblesPlantillas');
    
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
                html += `<div class="columna-reference"><code>[[${columna}]]</code></div>`;
            });
            container.html(html);
        }
    } else {
        container.html('<small class="text-danger">SQL no válido. Debe ser: SELECT columnas FROM tabla</small>');
    }
}

// Guardar plantilla
function guardarPlantillaForm() {
    const cod     = $('#cod_plantilla_form').val().trim();
    const nombre  = $('#nombre_form').val().trim();
    const sql     = $('#sql_consulta_form').val().trim();
    const contenido = tinymce.get('contenido_form') ? tinymce.get('contenido_form').getContent() : '';

    // Validaciones con feedback visual
    var errores = [];
    if (!cod) {
        $('#cod_plantilla_form').addClass('is-invalid');
        errores.push('El código es obligatorio');
    } else {
        $('#cod_plantilla_form').removeClass('is-invalid').addClass('is-valid');
    }
    if (!nombre) {
        $('#nombre_form').addClass('is-invalid');
        errores.push('El nombre es obligatorio');
    } else {
        $('#nombre_form').removeClass('is-invalid').addClass('is-valid');
    }
    if (!sql) {
        $('#sql_consulta_form').addClass('is-invalid');
        errores.push('La consulta SQL es obligatoria');
    } else {
        $('#sql_consulta_form').removeClass('is-invalid').addClass('is-valid');
    }
    if (!contenido || contenido === '<p></p>') {
        errores.push('El contenido HTML del documento es obligatorio');
    }

    if (errores.length > 0) {
        mostrarAlertaPlantillas(errores.join('<br>'), 'warning');
        return;
    }
    
    const datos = {
        cod_plantilla: cod,
        nombre: nombre,
        descripcion: $('#descripcion_form').val(),
        tipo_documento: $('#tipo_documento_form').val(),
        sql_consulta: sql,
        contenido: contenido,
        estado: $('#estado_form').is(':checked') ? 1 : 0,
        filtros: obtenerFiltrosPlantillas()
    };
    
    const action = plantillaEnEdicionForm ? 'editar&cod=' + plantillaEnEdicionForm : 'crear';
    
    $.ajax({
        url: APIPantillas + '?action=' + action,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        success: function(data) {
            if (data.success) {
                mostrarAlertaPlantillas('Plantilla guardada correctamente', 'success');
                limpiarFormularioPlantillas();
                mostrarTablaPlantillas();
                cargarPlantillasListado();
            } else {
                mostrarAlertaPlantillas(data.error || 'Error al guardar', 'danger');
            }
        },
        error: function(err) {
            mostrarAlertaPlantillas('Error al guardar plantilla', 'danger');
            console.error(err);
        }
    });
}

// Eliminar plantilla
function eliminarPlantillaForm(cod, nombre) {
    Swal.fire({
        title: '¿Eliminar plantilla?',
        html: '<b>' + nombre + '</b><br><small class="text-muted">Esta acción no se puede deshacer</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.post(APIPantillas + '?action=eliminar&cod_plantilla=' + cod, function(data) {
                if (data.success) {
                    mostrarAlertaPlantillas('Plantilla eliminada correctamente', 'success');
                    cargarPlantillasListado();
                } else {
                    mostrarAlertaPlantillas(data.error || 'Error al eliminar', 'danger');
                }
            });
        }
    });
}

// Limpiar formulario
function limpiarFormularioPlantillas() {
    $('#cod_plantilla_form, #nombre_form, #sql_consulta_form').removeClass('is-valid is-invalid');
    $('#cod_plantilla_form').val('');
    $('#nombre_form').val('');
    $('#descripcion_form').val('');
    $('#tipo_documento_form').val('');
    $('#sql_consulta_form').val('SELECT * FROM tabla WHERE id = [[id]]');
    $('#estado_form').prop('checked', true);
    if (tinymce.get('contenido_form')) {
        tinymce.get('contenido_form').setContent('');
    }
    $('#bodyFiltrosPlantillas').html('');
    plantillaEnEdicionForm = null;
}

// Cancelar edición
function cancelarFormularioPlantillas() {
    var cod     = $('#cod_plantilla_form').val().trim();
    var nombre  = $('#nombre_form').val().trim();
    var tieneContenido = tinymce.get('contenido_form') ? tinymce.get('contenido_form').getContent() !== '' : false;

    if (cod || nombre || tieneContenido) {
        Swal.fire({
            title: '¿Descartar cambios?',
            text: 'Tienes datos sin guardar. Si sales ahora se perderán.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Seguir editando'
        }).then(function(result) {
            if (result.isConfirmed) {
                // Limpiar clases de validación
                $('#cod_plantilla_form, #nombre_form, #sql_consulta_form').removeClass('is-valid is-invalid');
                limpiarFormularioPlantillas();
                mostrarTablaPlantillas();
            }
        });
    } else {
        limpiarFormularioPlantillas();
        mostrarTablaPlantillas();
    }
}

// Mostrar/ocultar tabla
function ocultarTablaPlantillas() {
    $('#tablaPantillas').hide();
    $('#formularioPlantillasSection').show();
}

function mostrarTablaPlantillas() {
    $('#tablaPantillas').show();
    $('#formularioPlantillasSection').hide();
}

// Mostrar alerta con SweetAlert2
function mostrarAlertaPlantillas(mensaje, tipo) {
    var icon = tipo === 'danger' ? 'error' : tipo === 'warning' ? 'warning' : tipo === 'success' ? 'success' : 'info';
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: mensaje,
        showConfirmButton: false,
        timer: tipo === 'success' ? 3000 : 4500,
        timerProgressBar: true
    });
}

var dtPlantillas = null;

function initDataTablePlantillas() {
    if ($.fn.DataTable.fnIsDataTable('#tablaPlantillas')) {
        $('#tablaPlantillas').DataTable().destroy();
        // Eliminar fila de filtros clonada si existe
        if ($('#tablaPlantillas thead tr').length > 1) {
            $('#tablaPlantillas thead tr:eq(1)').remove();
        }
    }

    // Clonar cabecera para fila de búsqueda
    if ($('#tablaPlantillas thead tr').length < 2) {
        $('#tablaPlantillas thead tr').clone(true).appendTo('#tablaPlantillas thead');
    }

    // Inputs de búsqueda en columnas (excepto Acciones)
    $('#tablaPlantillas thead tr:eq(1) th').each(function(i) {
        if (i < 5) {
            var title = $('#tablaPlantillas thead tr:eq(0) th').eq(i).text();
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar ' + title + '" />');
            $('input', this).on('keyup change', function() {
                if (dtPlantillas.column(i).search() !== this.value) {
                    dtPlantillas.column(i).search(this.value).draw();
                }
            });
        } else {
            $(this).html('');
        }
    });

    dtPlantillas = $('#tablaPlantillas').DataTable({
        'sDom': '<"d-flex justify-content-between align-items-center mb-2"l>t<"d-flex justify-content-between align-items-center mt-2"ip>',
        'bPaginate': true,
        'bLengthChange': true,
        'bFilter': true,
        'bInfo': false,
        'bAutoWidth': false,
        'searching': true,
        'pageLength': 25,
        'orderCellsTop': true,
        'fixedHeader': true,
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        'columnDefs': [{ 'orderable': false, 'targets': 5 }],
        'language': {
            'emptyTable': 'No hay plantillas creadas',
            'info': 'Mostrando página _PAGE_ de _PAGES_',
            'infoEmpty': 'Sin resultados',
            'infoFiltered': '(Filtrando _MAX_ registros)',
            'lengthMenu': 'Mostrar _MENU_ registros',
            'loadingRecords': 'Cargando...',
            'processing': 'Procesando...',
            'search': 'Buscar:',
            'zeroRecords': 'No se encontraron resultados',
            'paginate': { 'first': 'Primero', 'last': 'Último', 'next': 'Siguiente', 'previous': 'Anterior' }
        }
    });
}

// Inicializar cuando se carga el formulario
$(document).ready(function() {
    cargarPlantillasListado();
    inicializarTinyMCEForm();
    $(document).on('input', '#sql_consulta_form', function() {
        actualizarColumnasPlantillas();
    });

    // Validación en tiempo real: código único y sin espacios
    $(document).on('input', '#cod_plantilla_form', function() {
        var val = $(this).val();
        // Reemplazar espacios automáticamente
        if (val !== val.replace(/\s/g, '_')) {
            $(this).val(val.replace(/\s/g, '_'));
        }
    });
    $(document).on('blur', '#cod_plantilla_form', function() {
        var cod      = $(this).val().trim();
        var esEditar = $('#cod_plantilla_form').prop('disabled') || window._editandoCod;
        if (!cod || esEditar) return;
        $.getJSON(API_PLANTILLAS + '?action=obtener_completa&cod=' + encodeURIComponent(cod), function(resp) {
            if (resp.success && resp.data) {
                $('#cod_plantilla_form').removeClass('is-valid').addClass('is-invalid');
                $('#cod_error').text('Este código ya existe: "' + cod + '"');
            } else {
                $('#cod_plantilla_form').removeClass('is-invalid').addClass('is-valid');
            }
        });
    });

    // Validación en tiempo real: nombre no vacío
    $(document).on('blur', '#nombre_form', function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else {
            $(this).addClass('is-valid').removeClass('is-invalid');
        }
    });
});
</script>
