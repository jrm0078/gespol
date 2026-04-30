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
    
    .card-header:not(.card-header-blue) {
        border-bottom: 3px solid #0084D9 !important;
        background: linear-gradient(to right, rgba(0, 132, 217, 0.05), transparent) !important;
    }
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

    /* Tabla de filtros SQL: botón eliminar centrado verticalmente */
    #tablaFiltrosPlantillas td {
        vertical-align: middle;
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
        <div class="card-header card-header-blue py-2">
            <h5 class="m-0 text-white"><i class="mdi mdi-file-document mr-2"></i>Gestionar Plantillas</h5>
        </div>
        <div class="card-body p-2">
    <!-- TOOLBAR ERP -->
    <div class="tabla-toolbar">
        <div class="btn-group btn-group-sm" role="group">
            <button id="btnTbAddPlantilla" class="btn btn-toolbar-action" title="Nueva plantilla">
                <i class="fas fa-plus"></i><span class="d-none d-sm-inline ml-1">A&ntilde;adir</span>
            </button>
            <button id="btnTbEditPlantilla" class="btn btn-toolbar-action" title="Editar plantilla seleccionada" disabled>
                <i class="fas fa-edit"></i><span class="d-none d-sm-inline ml-1">Editar</span>
            </button>
        </div>
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-toolbar-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exportar">
                <i class="fas fa-file-export"></i><span class="d-none d-sm-inline ml-1">Exportar</span>
            </button>
            <div class="dropdown-menu shadow-sm">
                <a class="dropdown-item" href="#" data-exp="excel"><i class="fas fa-file-excel mr-2 text-success"></i>Excel</a>
                <a class="dropdown-item" href="#" data-exp="csv"><i class="fas fa-file-alt mr-2 text-info"></i>CSV</a>
                <a class="dropdown-item" href="#" data-exp="pdf"><i class="fas fa-file-pdf mr-2 text-danger"></i>PDF</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-exp="print"><i class="fas fa-print mr-2 text-secondary"></i>Imprimir</a>
                <a class="dropdown-item" href="#" data-exp="copy"><i class="fas fa-copy mr-2 text-muted"></i>Copiar</a>
            </div>
        </div>
    </div>
    <!-- FIN TOOLBAR -->
    <div class="table-responsive">
        <table id="tablaPlantillas" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Estado</th>
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
        <div class="card-header card-header-blue" style="color: white;">
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
            <!-- AYUDA / NOTAS -->
            <div class="form-group">
                <label class="font-weight-bold"><i class="fas fa-info-circle text-info mr-1"></i>Ayuda / Notas</label>
                <small class="d-block text-muted mb-2">Documenta aqu&iacute; el prop&oacute;sito de la SQL, las variables disponibles y cualquier aclaraci&oacute;n para otros usuarios.</small>
                <textarea class="form-control" id="ayuda_form" rows="3" 
                          placeholder="Ej: Esta consulta devuelve los datos del expediente. El par&aacute;metro [[id]] es el ID del registro seleccionado..."></textarea>
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
            <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
                <button type="button" class="btn btn-success" onclick="guardarPlantillaForm()">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
                <button type="button" id="btnEliminarPlantilla" class="btn btn-danger" onclick="eliminarPlantillaFormActual()" style="display:none;">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                </button>
                <button type="button" class="btn btn-secondary ml-auto" onclick="cancelarFormularioPlantillas()">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXT MENU PLANTILLAS -->
<div id="ctxMenuPlantillas" class="ctx-menu">
    <div class="ctx-menu-item" data-ctx-action="add"><i class="fas fa-plus"></i> A&ntilde;adir</div>
    <div class="ctx-menu-item" data-ctx-action="edit"><i class="fas fa-edit"></i> Editar</div>
    <div class="ctx-menu-separator"></div>
    <div class="ctx-menu-label">Exportar</div>
    <div class="ctx-menu-item" data-ctx-action="excel"><i class="fas fa-file-excel text-success"></i> Excel</div>
    <div class="ctx-menu-item" data-ctx-action="csv"><i class="fas fa-file-alt text-info"></i> CSV</div>
    <div class="ctx-menu-item" data-ctx-action="pdf"><i class="fas fa-file-pdf text-danger"></i> PDF</div>
    <div class="ctx-menu-item" data-ctx-action="print"><i class="fas fa-print text-secondary"></i> Imprimir</div>
    <div class="ctx-menu-item" data-ctx-action="copy"><i class="fas fa-copy text-muted"></i> Copiar</div>
</div>

<script>
// Usar var para permitir redeclaración cuando se recarga el módulo
var APIPantillas = 'inc/plantillas/ajax_plantillas.php';
var plantillaEnEdicionForm = null;
var formDirty = false;

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
        placeholder: 'Contenido HTML',
        image_list: function(success) {
            $.getJSON('inc/repositorio/ajax_repositorio.php?action=listar_imagenes', function(resp) {
                if (!resp.ok) { success([]); return; }
                var base = window.location.href.replace(/\/[^\/]*(\?.*)?$/, '/');
                var items = resp.data.map(function(item) {
                    return { title: item.title, value: base + item.value };
                });
                success(items);
            }).fail(function() { success([]); });
        },
        setup: function(editor) {
            editor.on('init', function() {
                // Adjuntamos el listener DESPUÉS del init para que los eventos
                // de inicialización (NodeChange, etc.) no marquen el form como sucio.
                setTimeout(function() {
                    editor.on('input keyup ExecCommand', function() {
                        formDirty = true;
                    });
                }, 300);
            });
        }
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
                    html += '<tr data-id="' + plantilla.cod_plantilla + '">'
                        + '<td><code>' + plantilla.cod_plantilla + '</code></td>'
                        + '<td>' + plantilla.nombre + '</td>'
                        + '<td><small>' + (plantilla.descripcion || '') + '</small></td>'
                        + '<td><small>' + (plantilla.tipo_documento || '') + '</small></td>'
                        + '<td>' + estado + '</td>'
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
    formDirty = false;
    $('#tituloFormularioPlantillas').text('Nueva Plantilla');
    $('#cod_plantilla_form').prop('disabled', false);
    $('#btnEliminarPlantilla').hide();
    limpiarFormularioPlantillas();
    ocultarTablaPlantillas();
    setTimeout(() => inicializarTinyMCEForm(), 100);
}

// Abrir formulario EDITAR plantilla
function abrirFormularioPlantillasEditar(cod) {
    window._editandoCod = true;
    formDirty = false;
    $.get(APIPantillas + '?action=obtener_completa&cod=' + cod, function(data) {
        if (data.success) {
            plantillaEnEdicionForm = cod;
            $('#tituloFormularioPlantillas').text('Editar Plantilla');
            $('#cod_plantilla_form').prop('disabled', true);
            $('#btnEliminarPlantilla').show();
            
            $('#cod_plantilla_form').val(data.data.cod_plantilla);
            $('#nombre_form').val(data.data.nombre);
            $('#descripcion_form').val(data.data.descripcion || '');
            $('#tipo_documento_form').val(data.data.tipo_documento || '');
            $('#sql_consulta_form').val(data.data.sql_consulta || '');
            $('#ayuda_form').val(data.data.ayuda || '');
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
        }
    });
}

// ============================================================
// GESTIÓN DE FILTROS EN MEMORIA
// ============================================================
var _filtrosMemoria = [];

function _renderFiltrosTabla() {
    const tbody = $('#bodyFiltrosPlantillas');
    tbody.html('');

    if (_filtrosMemoria.length === 0) {
        tbody.html('<tr><td colspan="7" class="text-center text-muted py-2"><small>Sin filtros. Pulsa "Agregar Filtro" para añadir uno.</small></td></tr>');
        return;
    }

    _filtrosMemoria.forEach((filtro, idx) => {
        let configHtml = '';
        if (filtro.tipo_filtro === 'select_sql') {
            const sqlEsc = (filtro.sql_query || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            configHtml = `<textarea class="form-control form-control-sm filtro-sql-query" rows="2" placeholder="SELECT id, nombre FROM tabla"
                onchange="_filtrosMemoria[${idx}].sql_query = this.value">${sqlEsc}</textarea>`;
        }

        const nombre = (filtro.nombre_filtro || '').replace(/"/g,'&quot;');
        const etiqueta = (filtro.etiqueta || '').replace(/"/g,'&quot;');

        const fila = `<tr>
            <td class="align-middle">
                <input type="text" class="form-control form-control-sm" value="${nombre}"
                    onchange="_filtrosMemoria[${idx}].nombre_filtro = this.value" placeholder="ej: id_cliente">
            </td>
            <td class="align-middle">
                <input type="text" class="form-control form-control-sm" value="${etiqueta}"
                    onchange="_filtrosMemoria[${idx}].etiqueta = this.value" placeholder="Etiqueta visible">
            </td>
            <td class="align-middle">
                <select class="form-control form-control-sm" onchange="_cambiarTipoFiltro(${idx}, this.value)">
                    <option value="select_sql" ${filtro.tipo_filtro === 'select_sql' ? 'selected' : ''}>SELECT SQL</option>
                    <option value="text" ${filtro.tipo_filtro === 'text' ? 'selected' : ''}>Texto</option>
                    <option value="number" ${filtro.tipo_filtro === 'number' ? 'selected' : ''}>Número</option>
                    <option value="date" ${filtro.tipo_filtro === 'date' ? 'selected' : ''}>Fecha</option>
                </select>
            </td>
            <td class="align-middle">${configHtml}</td>
            <td class="align-middle" style="width:80px;">
                <input type="number" class="form-control form-control-sm" value="${filtro.orden || 1}" min="1"
                    onchange="_filtrosMemoria[${idx}].orden = parseInt(this.value) || 1">
            </td>
            <td class="text-center align-middle" style="width:80px;">
                <input type="checkbox" style="width:18px;height:18px;cursor:pointer;"
                    ${filtro.requerido ? 'checked' : ''}
                    onchange="_filtrosMemoria[${idx}].requerido = this.checked ? 1 : 0">
            </td>
            <td class="text-center align-middle" style="width:60px;">
                <button type="button" class="btn btn-sm btn-danger" onclick="_eliminarFiltroMemoria(${idx})" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

        tbody.append(fila);
    });
}

function _cambiarTipoFiltro(idx, nuevoTipo) {
    _filtrosMemoria[idx].tipo_filtro = nuevoTipo;
    _filtrosMemoria[idx].sql_query = '';
    _renderFiltrosTabla();
}

function _eliminarFiltroMemoria(idx) {
    _filtrosMemoria.splice(idx, 1);
    _renderFiltrosTabla();
}

// Cargar filtros en formulario
function cargarFiltrosPlantillas(filtros) {
    _filtrosMemoria = (filtros || []).map(f => ({
        nombre_filtro: f.nombre_filtro || '',
        etiqueta: f.etiqueta || '',
        tipo_filtro: f.tipo_filtro || 'text',
        sql_query: f.sql_query || '',
        orden: f.orden || 1,
        requerido: f.requerido || 0
    }));
    _renderFiltrosTabla();
}

// Agregar fila de filtro
function agregarFilaFiltroPlantillas() {
    _filtrosMemoria.push({
        nombre_filtro: '',
        etiqueta: '',
        tipo_filtro: 'text',
        sql_query: '',
        orden: _filtrosMemoria.length + 1,
        requerido: 1
    });
    _renderFiltrosTabla();
}

// Obtener filtros del array en memoria
function obtenerFiltrosPlantillas() {
    return _filtrosMemoria.filter(f => f.nombre_filtro.trim() && f.etiqueta.trim());
}



// Guardar plantilla
function guardarPlantillaForm() {
    const cod     = $('#cod_plantilla_form').val().trim();
    const nombre  = $('#nombre_form').val().trim();
    const sql     = $('#sql_consulta_form').val().trim();
    const ayuda   = $('#ayuda_form').val().trim();
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
        ayuda: ayuda,
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

// Eliminar plantilla desde el formulario de edición
function eliminarPlantillaFormActual() {
    if (!plantillaEnEdicionForm) return;
    var nombre = $('#nombre_form').val() || plantillaEnEdicionForm;
    var cod = plantillaEnEdicionForm;
    eliminarPlantillaForm(cod, nombre);
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
                    mostrarTablaPlantillas();
                    cargarPlantillasListado();
                    toastMsg('Plantilla eliminada correctamente', 'success');
                } else {
                    mostrarAlertaPlantillas(data.error || 'Error al eliminar', 'danger');
                }
            });
        }
    });
}

// Limpiar formulario
function limpiarFormularioPlantillas() {
    formDirty = false;
    $('#cod_plantilla_form, #nombre_form, #sql_consulta_form').removeClass('is-valid is-invalid');
    $('#cod_plantilla_form').val('');
    $('#nombre_form').val('');
    $('#descripcion_form').val('');
    $('#tipo_documento_form').val('');
    $('#sql_consulta_form').val('SELECT * FROM tabla WHERE id = [[id]]');
    $('#ayuda_form').val('');
    $('#estado_form').prop('checked', true);
    if (tinymce.get('contenido_form')) {
        tinymce.get('contenido_form').setContent('');
    }
    $('#bodyFiltrosPlantillas').html('');
    _filtrosMemoria = [];
    _renderFiltrosTabla();
    plantillaEnEdicionForm = null;
}

// Cancelar edición
function cancelarFormularioPlantillas() {
    if (!formDirty) {
        limpiarFormularioPlantillas();
        mostrarTablaPlantillas();
        return;
    }

    // TinyMCE 6 tiene un focus-trap que hace que los botones de Swal no respondan.
    // La única solución fiable es destruir el editor antes de abrir el diálogo,
    // y reiniciarlo si el usuario decide quedarse.
    var contenidoGuardado = '';
    var editor = tinymce.get('contenido_form');
    if (editor) {
        contenidoGuardado = editor.getContent();
        editor.destroy();
    }

    Swal.fire({
        title: '¿Descartar cambios?',
        text: 'Tienes datos sin guardar. Si sales ahora se perderán.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, descartar',
        cancelButtonText: 'Seguir editando',
        returnFocus: false
    }).then(function(result) {
        if (result.isConfirmed) {
            formDirty = false;
            $('#cod_plantilla_form, #nombre_form, #sql_consulta_form').removeClass('is-valid is-invalid');
            // TinyMCE ya está destruido — llamar a limpiar sin intentar usarlo
            $('#cod_plantilla_form').val('');
            $('#nombre_form').val('');
            $('#descripcion_form').val('');
            $('#tipo_documento_form').val('');
            $('#sql_consulta_form').val('SELECT * FROM tabla WHERE id = [[id]]');
            $('#estado_form').prop('checked', true);
            $('#bodyFiltrosPlantillas').html('');
            plantillaEnEdicionForm = null;
            mostrarTablaPlantillas();
        } else {
            // Reinicializar el editor con el contenido que tenía
            inicializarTinyMCEForm();
            setTimeout(function() {
                var ed = tinymce.get('contenido_form');
                if (ed) ed.setContent(contenidoGuardado);
            }, 500);
        }
    });
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
var toolbarPlantillas = null;

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

    // Inputs de búsqueda en todas las columnas
    $('#tablaPlantillas thead tr:eq(1) th').each(function(i) {
        var title = $('#tablaPlantillas thead tr:eq(0) th').eq(i).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function() {
            if (dtPlantillas.column(i).search() !== this.value) {
                dtPlantillas.column(i).search(this.value).draw();
            }
        });
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
        'columnDefs': [],
        'buttons': [
            { extend: 'excel',  text: 'Excel'    },
            { extend: 'csv',    text: 'CSV'      },
            { extend: 'pdf',    text: 'PDF'      },
            { extend: 'print',  text: 'Imprimir' },
            { extend: 'copy',   text: 'Copiar'   }
        ],
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

    // Inicializar toolbar ERP
    toolbarPlantillas = initTablaToolbar({
        tableId:   '#tablaPlantillas',
        ctxMenuId: '#ctxMenuPlantillas',
        btnAdd:    '#btnTbAddPlantilla',
        btnEdit:   '#btnTbEditPlantilla',
        getDt:     function () { return dtPlantillas; },
        onAdd:     function () { abrirFormularioPlantillasNueva(); },
        onEdit:    function (tr) { abrirFormularioPlantillasEditar($(tr).data('id')); }
    });
}

// Inicializar cuando se carga el formulario
$(document).ready(function() {
    cargarPlantillasListado();
    inicializarTinyMCEForm();

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
        $.getJSON(APIPantillas + '?action=obtener_completa&cod=' + encodeURIComponent(cod), function(resp) {
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
    // Marcar dirty al cambiar campos de texto
    $(document).on('input change', '#cod_plantilla_form, #nombre_form, #descripcion_form, #tipo_documento_form, #sql_consulta_form, #estado_form', function() {
        formDirty = true;
    });
});
</script>
