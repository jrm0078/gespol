<?php include("inc/seguridad.php"); ?>

<style>
    /* Responsive TinyMCE */
    .tox-tinymce {
        max-width: 100% !important;
        border: 1px solid #0084D9 !important;
    }
    
    .card-header {
        border-bottom: 3px solid #0084D9 !important;
        background: linear-gradient(to right, rgba(0, 132, 217, 0.05), transparent) !important;
    }
    
    /* Acentos azules */
    .form-control:focus,
    .form-select:focus {
        border-color: #0084D9 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 132, 217, 0.25) !important;
    }
    
    /* Container responsivo */
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Cards responsive */
    .card {
        margin-bottom: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    /* Responsive botones */
    @media (max-width: 768px) {
        .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        .card-header {
            padding: 0.75rem 1rem;
        }
        .card-body {
            padding: 0.75rem;
        }
        h5 {
            font-size: 1.1rem;
        }
    }
    
    /* Full width inputs y botones en mobile */
    @media (max-width: 576px) {
        .form-control, .form-select, textarea {
            font-size: 16px !important; /* Evita zoom en iOS */
        }
        .row.g-2 {
            row-gap: 0.75rem;
        }
        .btn {
            padding: 0.5rem 0.5rem;
            font-size: 0.85rem;
        }
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header bg-primary border-left-primary py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-document"></i> Generador de Documentos</h6>
        </div>
        <div class="card-body">

            <!-- SECCIÓN 1: SELECCIONAR PLANTILLA -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="m-0"><i class="fas fa-list"></i> 1. Seleccionar Plantilla</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12 col-md-8">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Plantilla *</label>
                                <select id="selectPlantilla" class="form-control form-control-lg" onchange="cargarPlantilla()">
                                    <option value="">-- Seleccionar una plantilla --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold" style="display: block; height: 24px;">&nbsp;</label>
                                <button class="btn btn-warning btn-sm w-100" onclick="limpiar()" title="Limpiar formulario">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: FILTROS DINÁMICOS -->
            <div id="filtroSection" style="display:none;" class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="m-0"><i class="fas fa-filter"></i> 2. Aplicar Filtros</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2" id="filtrosContainer">
                        <!-- Los filtros se cargan dinámicamente -->
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" onclick="aplicarFiltro()">
                                <i class="fas fa-search"></i> Cargar Documento
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: EDITOR DE DOCUMENTO -->
            <div id="editorSection" style="display:none;" class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="m-0"><i class="fas fa-edit"></i> 3. Contenido HTML</h5>
                </div>
                <div class="card-body">
                    <textarea id="documento-editor"></textarea>
                </div>
            </div>

            <!-- SECCIÓN 4: ACCIONES -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="row g-2">
                        <div class="col-12 col-sm-6 col-lg-auto">
                            <button class="btn btn-danger w-100" onclick="generarDocumento()">
                                <i class="fas fa-sync"></i> Actualizar
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-auto">
                            <button class="btn btn-primary w-100" onclick="descargarPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-auto">
                            <button class="btn btn-secondary w-100" onclick="imprimirDocumento()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-auto">
                            <button class="btn btn-success w-100" onclick="guardarDocumento()">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-auto">
                            <button class="btn btn-light w-100" onclick="limpiar()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>

var plantillaActual = null;
var datosFormulario  = {};
var API_PLANTILLAS   = 'inc/plantillas/ajax_plantillas.php';

// ============================================================
// INICIALIZACIÓN
// ============================================================
$(document).ready(function() {
    cargarPlantillasDisponibles();
    inicializarTinyMCE();
});

// ============================================================
// TINYMCE
// ============================================================
function inicializarTinyMCE() {
    if (tinymce.get('documento-editor')) {
        tinymce.get('documento-editor').remove();
    }
    tinymce.init({
        selector: '#documento-editor',
        language: 'es',
        height: (window.innerWidth < 768) ? 300 : 500,
        menubar: 'file edit view insert format tools',
        plugins: 'advlist autolink lists link image charmap anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen | table',
        branding: false,
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        entity_encoding: 'raw'
    });
}

// ============================================================
// CARGAR LISTA DE PLANTILLAS
// ============================================================
function cargarPlantillasDisponibles() {
    $.ajax({
        url: API_PLANTILLAS + '?action=listar_activas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const select = document.getElementById('selectPlantilla');
                select.innerHTML = '<option value="">-- Seleccionar una plantilla --</option>';
                response.data.forEach(function(p) {
                    const opt = document.createElement('option');
                    opt.value = p.cod_plantilla;
                    opt.textContent = p.nombre + (p.descripcion ? ' - ' + p.descripcion : '');
                    select.appendChild(opt);
                });
            }
        }
    });
}

// ============================================================
// SELECCIONAR PLANTILLA
// ============================================================
function cargarPlantilla() {
    const cod = document.getElementById('selectPlantilla').value;

    if (!cod) {
        limpiar();
        return;
    }

    $.ajax({
        url: API_PLANTILLAS + '?action=obtener_completa&cod=' + encodeURIComponent(cod),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                plantillaActual = response.data;
                datosFormulario  = {};

                // Cargar filtros dinámicos desde el endpoint enriched
                cargarFiltrosDinamicos(cod);

                // Mostrar editor con contenido base
                document.getElementById('editorSection').style.display = 'block';
                setTimeout(function() {
                    inicializarTinyMCE();
                    setTimeout(function() {
                        var ed = tinymce.get('documento-editor');
                        if (ed) ed.setContent(plantillaActual.contenido || '');
                    }, 150);
                }, 100);

            } else {
                mostrarAlerta('Error al cargar plantilla: ' + response.error, 'danger');
            }
        }
    });
}

// ============================================================
// CARGAR FILTROS DINÁMICAMENTE (CON VALORES)
// ============================================================
function cargarFiltrosDinamicos(cod) {
    $.ajax({
        url: API_PLANTILLAS + '?action=obtener_filtros&cod=' + encodeURIComponent(cod),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (!response.success || !response.data || response.data.length === 0) {
                document.getElementById('filtroSection').style.display = 'none';
                return;
            }

            const container = document.getElementById('filtrosContainer');
            container.innerHTML = '';

            response.data.forEach(function(filtro) {
                const col = document.createElement('div');
                col.className = 'col-12 col-md-6 mb-3';

                const tipo = filtro.tipo_filtro || 'text';
                let html = '<div class="form-group">';
                html += '<label class="font-weight-bold">' + escHtml(filtro.etiqueta) + (filtro.requerido ? ' <span class="text-danger">*</span>' : '') + '</label>';

                if (tipo === 'select_table' || tipo === 'select_sql') {
                    if (filtro.tiene_parametros && filtro.parametros_requeridos && filtro.parametros_requeridos.length > 0) {
                        // Inputs para los parámetros
                        filtro.parametros_requeridos.forEach(function(pname) {
                            html += '<input type="text" class="form-control form-control-sm mb-1 param-input" '
                                  + 'data-param-name="' + escHtml(pname) + '" '
                                  + 'data-filtro-parent="' + escHtml(filtro.nombre_filtro) + '" '
                                  + 'placeholder="Valor para [[' + escHtml(pname) + ']]">';
                        });
                        html += '<select id="filtro_' + escHtml(filtro.nombre_filtro) + '" '
                              + 'class="form-control filtro-input" '
                              + 'data-filtro="' + escHtml(filtro.nombre_filtro) + '" '
                              + 'data-tipo="' + tipo + '" '
                              + 'data-tiene-parametros="true">'
                              + '<option value="">-- Completa los parámetros arriba --</option></select>';
                    } else {
                        html += '<select id="filtro_' + escHtml(filtro.nombre_filtro) + '" '
                              + 'class="form-control filtro-input" '
                              + 'data-filtro="' + escHtml(filtro.nombre_filtro) + '" '
                              + 'data-tipo="' + tipo + '">'
                              + '<option value="">-- Seleccionar ' + escHtml(filtro.etiqueta.toLowerCase()) + ' --</option>';
                        if (filtro.valores && filtro.valores.length > 0) {
                            filtro.valores.forEach(function(v) {
                                html += '<option value="' + escHtml(String(v.id)) + '">' + escHtml(String(v.valor)) + '</option>';
                            });
                        }
                        html += '</select>';
                    }
                } else if (tipo === 'number') {
                    html += '<input type="number" id="filtro_' + escHtml(filtro.nombre_filtro) + '" '
                          + 'class="form-control filtro-input" data-filtro="' + escHtml(filtro.nombre_filtro) + '" '
                          + 'placeholder="' + escHtml(filtro.etiqueta) + '">';
                } else if (tipo === 'date') {
                    html += '<input type="date" id="filtro_' + escHtml(filtro.nombre_filtro) + '" '
                          + 'class="form-control filtro-input" data-filtro="' + escHtml(filtro.nombre_filtro) + '">';
                } else {
                    html += '<input type="text" id="filtro_' + escHtml(filtro.nombre_filtro) + '" '
                          + 'class="form-control filtro-input" data-filtro="' + escHtml(filtro.nombre_filtro) + '" '
                          + 'placeholder="' + escHtml(filtro.etiqueta) + '">';
                }

                html += '</div>';
                col.innerHTML = html;
                container.appendChild(col);
            });

            // Listener para param-inputs (select_sql con parámetros dinámicos)
            container.addEventListener('blur', function(e) {
                if (e.target.classList.contains('param-input')) {
                    var parentFiltro = e.target.getAttribute('data-filtro-parent');
                    cargarOpcionesConParametros(parentFiltro, container);
                }
            }, true);

            document.getElementById('filtroSection').style.display = 'block';
        }
    });
}

// Cargar opciones de un select_sql con parámetros
function cargarOpcionesConParametros(nombreFiltro, container) {
    if (!plantillaActual) return;

    var paramInputs = container.querySelectorAll('.param-input[data-filtro-parent="' + nombreFiltro + '"]');
    var params = {};
    var allFilled = true;
    paramInputs.forEach(function(inp) {
        var pname = inp.getAttribute('data-param-name');
        var val   = inp.value.trim();
        params[pname] = val;
        if (!val) allFilled = false;
    });
    if (!allFilled) return;

    var url = API_PLANTILLAS + '?action=ejecutar_select_filtro'
            + '&cod='    + encodeURIComponent(plantillaActual.cod_plantilla)
            + '&filtro=' + encodeURIComponent(nombreFiltro)
            + '&parametros=' + encodeURIComponent(JSON.stringify(params));

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (!data.success) return;
            var sel = document.getElementById('filtro_' + nombreFiltro);
            if (!sel) return;
            sel.innerHTML = '<option value="">-- Seleccionar --</option>';
            data.data.forEach(function(v) {
                var opt = document.createElement('option');
                opt.value   = v.id;
                opt.textContent = v.valor;
                sel.appendChild(opt);
            });
        }
    });
}

// ============================================================
// APLICAR FILTRO → obtener datos y reemplazar variables
// ============================================================
function aplicarFiltro() {
    if (!plantillaActual) {
        mostrarAlerta('Selecciona una plantilla primero', 'warning');
        return;
    }

    var filtros = {};
    var todosCompletos = true;

    document.querySelectorAll('.filtro-input').forEach(function(el) {
        var nombre = el.getAttribute('data-filtro');
        var valor  = el.value;
        filtros[nombre] = valor;
        if (!valor) todosCompletos = false;
    });

    if (!todosCompletos) {
        mostrarAlerta('Completa todos los filtros requeridos', 'warning');
        return;
    }

    $.ajax({
        url: API_PLANTILLAS + '?action=obtener_datos_filtrados'
           + '&cod='     + encodeURIComponent(plantillaActual.cod_plantilla)
           + '&filtros=' + encodeURIComponent(JSON.stringify(filtros)),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                datosFormulario = response.data;
                var contenido = reemplazarVariables(plantillaActual.contenido, response.data);

                var ed = tinymce.get('documento-editor');
                if (ed) ed.setContent(contenido);
                document.getElementById('editorSection').style.display = 'block';
                mostrarAlerta('Documento cargado correctamente', 'success');
            } else {
                mostrarAlerta('Error: ' + response.error, 'danger');
            }
        }
    });
}

// ============================================================
// REEMPLAZAR VARIABLES EN CONTENIDO (cliente-side)
// Soporta [[var]], {%%var%%}, {{var}}, -var-
// ============================================================
function reemplazarVariables(contenido, datos) {
    if (!datos || typeof datos !== 'object') return contenido;

    // Caso: array de registros (tabla repetible)
    if (Array.isArray(datos) && datos.length > 0) {
        var rowRegex = /<tr[^>]*>[\s\S]*?\[\[[\s\S]*?\]\][\s\S]*?<\/tr>/gi;
        var rowMatch = contenido.match(rowRegex);
        if (rowMatch && rowMatch.length > 0) {
            var rowTemplate = rowMatch[0];
            var allRows = '';
            datos.forEach(function(record) {
                var row = rowTemplate;
                for (var key in record) {
                    if (record.hasOwnProperty(key)) {
                        var val = record[key] !== null ? String(record[key]) : '';
                        row = row.split('[[' + key + ']]').join(val);
                    }
                }
                allRows += row;
            });
            contenido = contenido.replace(rowRegex, allRows);
        }
        return contenido;
    }

    // Caso: objeto único
    for (var key in datos) {
        if (datos.hasOwnProperty(key)) {
            var val = datos[key] !== null ? String(datos[key]) : '';
            contenido = contenido.split('[[' + key + ']]').join(val);
            contenido = contenido.split('{%%' + key + '%%}').join(val);
            contenido = contenido.split('{{' + key + '}}').join(val);
            contenido = contenido.split('-' + key + '-').join(val);
        }
    }
    return contenido;
}

// ============================================================
// GENERAR DOCUMENTO (re-aplica filtros desde el editor)
// ============================================================
function generarDocumento() {
    aplicarFiltro();
}

// ============================================================
// DESCARGAR PDF (imprime en ventana nueva)
// ============================================================
function descargarPDF() {
    if (!plantillaActual) {
        mostrarAlerta('Selecciona una plantilla primero', 'warning');
        return;
    }
    var ed = tinymce.get('documento-editor');
    var contenido = ed ? ed.getContent() : '';
    if (!contenido || contenido === '<p></p>') {
        mostrarAlerta('Genera el documento primero', 'warning');
        return;
    }
    var nombre = (plantillaActual ? plantillaActual.nombre : 'documento').replace(/\s+/g, '_');
    var ventana = window.open('', '_blank', 'height=700,width=900');
    ventana.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + nombre + '</title>');
    ventana.document.write('<style>@page{margin:15mm} body{font-family:Arial,sans-serif;font-size:12px;color:#333;padding:0;margin:0} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:4px 8px}</style>');
    ventana.document.write('</head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    setTimeout(function() { ventana.print(); }, 400);
}

// ============================================================
// IMPRIMIR DOCUMENTO
// ============================================================
function imprimirDocumento() {
    descargarPDF();
}

// ============================================================
// GUARDAR DOCUMENTO EN BD
// ============================================================
function guardarDocumento() {
    if (!plantillaActual) {
        mostrarAlerta('Selecciona una plantilla primero', 'warning');
        return;
    }
    var ed = tinymce.get('documento-editor');
    var contenido_final = ed ? ed.getContent() : '';
    if (!contenido_final || contenido_final.trim() === '' || contenido_final === '<p></p>') {
        mostrarAlerta('El documento está vacío. Genera el documento con datos válidos primero', 'warning');
        return;
    }

    $.ajax({
        url: API_PLANTILLAS + '?action=guardar_documento',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            cod_plantilla:   plantillaActual.cod_plantilla,
            contenido_final: contenido_final,
            datos:           datosFormulario
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarAlerta('Documento guardado correctamente', 'success');
            } else {
                mostrarAlerta('Error al guardar: ' + response.error, 'danger');
            }
        }
    });
}

// ============================================================
// LIMPIAR
// ============================================================
function limpiar() {
    document.getElementById('selectPlantilla').value = '';
    document.getElementById('filtrosContainer').innerHTML = '';
    document.getElementById('filtroSection').style.display  = 'none';
    document.getElementById('editorSection').style.display  = 'none';
    var ed = tinymce.get('documento-editor');
    if (ed) ed.setContent('');
    datosFormulario  = {};
    plantillaActual  = null;
}

// ============================================================
// HELPERS
// ============================================================
function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#39;');
}

function mostrarAlerta(mensaje, tipo) {
    var id  = 'alert-' + Date.now();
    var div = document.createElement('div');
    div.id        = id;
    div.className = 'alert alert-' + tipo + ' alert-dismissible fade show';
    div.role      = 'alert';
    div.innerHTML = mensaje + '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
    var container = document.querySelector('.container-fluid');
    if (container) container.insertBefore(div, container.firstChild);
    setTimeout(function() {
        var el = document.getElementById(id);
        if (el) el.remove();
    }, 4000);
}

</script>
