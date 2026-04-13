<?php include("inc/seguridad.php"); ?>

<style>
    /* Responsive TinyMCE */
    .tox-tinymce {
        max-width: 100% !important;
        border: 1px solid #dee2e6 !important;
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
                            <button class="btn btn-info w-100" onclick="descargarPDF()">
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

// Usar var para permitir redeclaración
var plantillaActual = null;
var datosFormulario = {};

// Inicializar TinyMCE para el editor de documentos
function inicializarTinyMCE() {
    // Destruir instancia anterior si existe
    if (tinymce.get('documento-editor')) {
        tinymce.get('documento-editor').remove();
    }
    
    // Detectar altura según pantalla
    let altura = 500;
    if (window.innerWidth < 768) {
        altura = 300;
    }
    
    tinymce.init({
        selector: '#documento-editor',
        language: 'es',
        height: altura,
        menubar: 'file edit view insert format tools',
        plugins: 'advlist autolink lists link image charmap anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen | table',
        branding: false,
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        entity_encoding: 'raw'
    });
}

// Cargar plantillas disponibles
function cargarPlantillasDisponibles() {
    $.ajax({
        url: 'inc/plantillas/ajax_plantillas.php?action=listar',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const select = document.getElementById('selectPlantilla');
                select.innerHTML = '<option value="">-- Seleccionar una plantilla --</option>';
                
                response.data.forEach(plantilla => {
                    const option = document.createElement('option');
                    option.value = plantilla.cod_plantilla;
                    option.textContent = plantilla.nombre;
                    select.appendChild(option);
                });
            }
        }
    });
}

// Cargar plantilla seleccionada
function cargarPlantilla() {
    const cod = document.getElementById('selectPlantilla').value;
    
    if (!cod) {
        document.getElementById('filtroSection').style.display = 'none';
        document.getElementById('editorSection').style.display = 'none';
        return;
    }
    
    $.ajax({
        url: 'inc/plantillas/ajax_plantillas.php?action=obtener_completa&cod=' + cod,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                plantillaActual = response.data;
                datosFormulario = {};
                
                // Mostrar editor
                document.getElementById('editorSection').style.display = 'block';
                
                // Inicializar TinyMCE y cargar contenido
                setTimeout(function() {
                    inicializarTinyMCE();
                    setTimeout(function() {
                        if (tinymce.get('documento-editor')) {
                            tinymce.get('documento-editor').setContent(plantillaActual.contenido);
                        }
                    }, 100);
                }, 100);
                
                // Mostrar filtros si existen
                if (plantillaActual.filtros && plantillaActual.filtros.length > 0) {
                    cargarFiltros(plantillaActual.filtros);
                    document.getElementById('filtroSection').style.display = 'block';
                } else {
                    document.getElementById('filtroSection').style.display = 'none';
                }
            }
        }
    });
}

// Cargar filtros dinámicos
function cargarFiltros(filtros) {
    const container = document.getElementById('filtrosContainer');
    container.innerHTML = '';
    
    filtros.forEach(filtro => {
        const col = document.createElement('div');
        col.className = 'col-12 col-md-6 mb-3';
        
        if (filtro.tipo_filtro === 'text') {
            col.innerHTML = `
                <div class="form-group">
                    <label class="font-weight-bold">${filtro.etiqueta}</label>
                    <input type="text" class="form-control" id="filtro_${filtro.nombre_filtro}" 
                           placeholder="${filtro.etiqueta}">
                </div>
            `;
        } else if (filtro.tipo_filtro === 'number') {
            col.innerHTML = `
                <div class="form-group">
                    <label class="font-weight-bold">${filtro.etiqueta}</label>
                    <input type="number" class="form-control" id="filtro_${filtro.nombre_filtro}" 
                           placeholder="${filtro.etiqueta}">
                </div>
            `;
        } else if (filtro.tipo_filtro === 'date') {
            col.innerHTML = `
                <div class="form-group">
                    <label class="font-weight-bold">${filtro.etiqueta}</label>
                    <input type="date" class="form-control" id="filtro_${filtro.nombre_filtro}">
                </div>
            `;
        }
        
        container.appendChild(col);
    });
}

// Aplicar filtro
function aplicarFiltro() {
    // Por ahora, no hacemos nada especial
    // En una versión mejorada, aquí ejecutaríamos SQL dinámico
    generarDocumento();
}

// Generar documento (reemplazar variables)
function generarDocumento() {
    if (!plantillaActual) {
        alert('Selecciona una plantilla primero');
        return;
    }
    
    // Recopilar datos del formulario
    datosFormulario = {};
    
    // Si hay filtros, recopilar sus valores
    const filtros = document.querySelectorAll('[id^="filtro_"]');
    filtros.forEach(filtro => {
        const nombre = filtro.id.replace('filtro_', '');
        datosFormulario[nombre] = filtro.value;
    });
    
    // Por ahora, solo mostramos el contenido original
    // La funcionalidad completa requiere backend
    console.log('Datos formulario:', datosFormulario);
}

// Descargar PDF
function descargarPDF() {
    const editor = tinymce.get('documento-editor');
    const contenido = editor ? editor.getContent() : '';
    
    if (!contenido || contenido === '') {
        alert('No hay contenido para descargar');
        return;
    }
    
    const ventana = window.open('', '', 'height=600,width=800');
    ventana.document.write('<html><head><title>Documento</title></head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.print();
}

// Imprimir documento
function imprimirDocumento() {
    const editor = tinymce.get('documento-editor');
    const contenido = editor ? editor.getContent() : '';
    
    if (!contenido || contenido === '') {
        alert('No hay contenido para imprimir');
        return;
    }
    
    const ventana = window.open('', '', 'height=600,width=800');
    ventana.document.write('<pre style="font-family: Arial; padding: 20px;">' + contenido + '</pre>');
    ventana.document.close();
    ventana.print();
}

// Guardar documento
function guardarDocumento() {
    if (!plantillaActual) {
        alert('Selecciona una plantilla');
        return;
    }
    
    const editor = tinymce.get('documento-editor');
    const contenido_final = editor ? editor.getContent() : '';
    
    if (!contenido_final || contenido_final === '') {
        alert('El documento no puede estar vacío');
        return;
    }
    
    // Por ahora mostramos un mensaje
    // La funcionalidad completa requiere endpoint backend adicional
    alert('Guardando documento...');
    console.log('Datos para guardar:', {
        cod_plantilla: plantillaActual.cod_plantilla,
        contenido_final: contenido_final,
        datos: datosFormulario
    });
}

// Limpiar formulario
function limpiar() {
    document.getElementById('selectPlantilla').value = '';
    
    const editor = tinymce.get('documento-editor');
    if (editor) {
        editor.setContent('');
    }
    
    document.getElementById('filtroSection').style.display = 'none';
    document.getElementById('editorSection').style.display = 'none';
    datosFormulario = {};
    plantillaActual = null;
}

// Inicializar
$(document).ready(function() {
    cargarPlantillasDisponibles();
    inicializarTinyMCE();
});
