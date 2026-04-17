"use strict";

var API_REPO = 'inc/repositorio/ajax_repositorio.php';
var _tablRepo = null;

$(".preloader2").fadeIn();
Cargar();
$(".preloader2").fadeOut();

// ─────────────────────────────────────────────
// INIT
// ─────────────────────────────────────────────
function Cargar() {
    cargarDirectorios();
    CargaTabla();
}

// ─────────────────────────────────────────────
// TABLA DATATABLES
// ─────────────────────────────────────────────
function CargaTabla() {
    if ($.fn.DataTable.isDataTable('#tblRepositorio')) {
        $('#tblRepositorio').DataTable().destroy();
    }

    // Fila de filtros por columna
    if ($('#tblRepositorio thead tr').length < 2) {
        $('#tblRepositorio thead tr').clone(true).appendTo('#tblRepositorio thead');
    }
    $('#tblRepositorio thead tr:eq(1) th').each(function (i) {
        if (i === 0 || i === 1) { $(this).html(''); return; } // oculto y preview
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function () {
            if (_tablRepo && _tablRepo.column(i).search() !== this.value) {
                _tablRepo.column(i).search(this.value).draw();
            }
        });
    });

    _tablRepo = $('#tblRepositorio').DataTable({
        "sDom": '<f>t<pl>',
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false,
        "pageLength": 50,
        "orderCellsTop": true,
        "fixedHeader": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "language": {
            "emptytable": "No hay ficheros en el repositorio",
            "lengthMenu": "Mostrar _MENU_ registros",
            "search": "Buscar:",
            "zeroRecords": "No se han encontrado resultados",
            "paginate": { "previous": "Anterior", "next": "Siguiente" }
        },
        "columnDefs": [
            { "targets": 0, "visible": false, "searchable": false },
            { "targets": 1, "orderable": false, "searchable": false,
              "render": function (data, type, row) {
                  return renderPreview(row);
              }
            },
            { "targets": 5, "render": function(data) { return '<small class="text-muted">' + data + '</small>'; } },
            { "targets": 6, "render": function(data) { return formatBytes(data); } },
            { "targets": 3, "render": function(data) {
                return data ? '<span class="dir-badge"><i class="fas fa-folder mr-1"></i>' + escHtml(data) + '</span>' : '<span class="text-muted">—</span>';
            }},
        ],
        "processing": true,
        "serverSide": true,
        "order": [[7, "desc"]],
        "ajax": {
            type: "POST",
            url: API_REPO + '?action=cargar_tabla',
            dataType: 'json'
        }
    });

    initTablaToolbar({
        tableId:   '#tblRepositorio',
        ctxMenuId: '#ctxMenuRepositorio',
        btnAdd:    '#btnTbAddRepo',
        btnEdit:   '#btnTbEditRepo',
        getDt:     function () { return _tablRepo; },
        onAdd:     function ()    { abrirFormNuevo(); },
        onEdit:    function (tr)  { abrirFormEditar(tr); },
    });

    // Botones extra
    $('#btnTbDelRepo').on('click', function () {
        var tr = _tablRepo.row({ selected: true }).node();
        if (tr) confirmarEliminar(tr);
    });
    $('#btnTbCopyUrl').on('click', function () {
        var tr = _tablRepo.row({ selected: true }).node();
        if (tr) copiarUrlDeFila(tr);
    });

    // Selección → activar botones extra
    $('#tblRepositorio').on('click', 'tbody tr', function () {
        var sel = _tablRepo.row(this).node() === _tablRepo.row({ selected: true }).node();
        $('#btnTbDelRepo, #btnTbCopyUrl').prop('disabled', false);
    });
    $('#tblRepositorio').on('deselect', function () {
        $('#btnTbDelRepo, #btnTbCopyUrl').prop('disabled', true);
    });

    // Context menu extra
    $('#ctxMenuRepositorio').on('click', '[data-ctx-action="copyurl"]', function () {
        var tr = _tablRepo.row({ selected: true }).node();
        if (tr) copiarUrlDeFila(tr);
    });
    $('#ctxMenuRepositorio').on('click', '[data-ctx-action="delete"]', function () {
        var tr = _tablRepo.row({ selected: true }).node();
        if (tr) confirmarEliminar(tr);
    });
}

// ─────────────────────────────────────────────
// CARGAR DIRECTORIOS EN SELECT
// ─────────────────────────────────────────────
function cargarDirectorios(valorActual) {
    $.get(API_REPO + '?action=listar_directorios', function (res) {
        if (!res.ok) return;
        var sel = $('#repo_directorio_select');
        sel.empty().append('<option value="">-- Raíz --</option>');
        res.directorios.forEach(function (d) {
            if (d === '') return;
            sel.append('<option value="' + escHtml(d) + '">' + escHtml(d) + '</option>');
        });
        if (valorActual) sel.val(valorActual);
        sincronizarDirInput();
    });
}

function sincronizarDirInput() {
    $('#repo_directorio').val($('#repo_directorio_select').val());
}

function toggleNuevaCarepeta() {
    var row = $('#nuevaCarpetaRow');
    row.toggle();
    if (row.is(':visible')) $('#repo_nueva_carpeta').focus();
}

function usarNuevaCarpeta() {
    var val = $('#repo_nueva_carpeta').val().trim().replace(/[^a-zA-Z0-9_\-\/]/g, '');
    if (!val) { Swal.fire('Atención', 'Introduce un nombre válido para la carpeta.', 'warning'); return; }
    // Añadir al select si no existe
    if ($('#repo_directorio_select option[value="' + val + '"]').length === 0) {
        $('#repo_directorio_select').append('<option value="' + escHtml(val) + '">' + escHtml(val) + '</option>');
    }
    $('#repo_directorio_select').val(val);
    sincronizarDirInput();
    toggleNuevaCarepeta();
    $('#repo_nueva_carpeta').val('');
}

// ─────────────────────────────────────────────
// FORMULARIO: ABRIR NUEVO
// ─────────────────────────────────────────────
function abrirFormNuevo() {
    $('#repo_id').val('');
    $('#repo_descripcion').val('');
    $('#repo_directorio_select').val('');
    sincronizarDirInput();
    $('#repo_fichero').val('');
    $('#lblFicheroNombre').text('Seleccionar fichero...');
    $('#ficheroActualSection').hide();
    $('#ficheroRequerido').show();
    $('#tituloFormRepo').html('<i class="fas fa-plus-circle mr-1"></i> Nuevo Fichero');
    $('#alertaRepo').html('');
    $('#tablaRepositorioSection').hide();
    $('#formularioRepositorioSection').show();
    cargarDirectorios();
}

// ─────────────────────────────────────────────
// FORMULARIO: ABRIR EDITAR
// ─────────────────────────────────────────────
function abrirFormEditar(tr) {
    var row  = _tablRepo.row(tr);
    var data = row.data();
    var id   = data[0];

    $.post(API_REPO + '?action=obtener', { id: id }, function (res) {
        if (!res.ok) { Swal.fire('Error', res.error, 'error'); return; }
        var d = res.data;

        $('#repo_id').val(d.id);
        $('#repo_descripcion').val(d.descripcion);
        $('#repo_fichero').val('');
        $('#lblFicheroNombre').text('Seleccionar fichero nuevo (opcional)...');
        $('#ficheroRequerido').hide();

        // Mostrar fichero actual
        var url = d.url;
        var esImagen = /\.(jpg|jpeg|png|gif|webp|svg|bmp)$/i.test(d.nombre_fichero);
        if (esImagen) {
            $('#previewActual').attr('src', url).show();
        } else {
            $('#previewActual').hide();
        }
        $('#linkActual').attr('href', url);
        $('#nombreActual').text(d.nombre_original);
        $('#ficheroActualSection').show();

        $('#tituloFormRepo').html('<i class="fas fa-edit mr-1"></i> Editar Fichero');
        $('#alertaRepo').html('');
        cargarDirectorios(d.directorio);
        $('#tablaRepositorioSection').hide();
        $('#formularioRepositorioSection').show();
    });
}

function cancelarFormRepo() {
    $('#formularioRepositorioSection').hide();
    $('#tablaRepositorioSection').show();
}

// ─────────────────────────────────────────────
// GUARDAR (crear o editar)
// ─────────────────────────────────────────────
function guardarRepo() {
    var descripcion = $.trim($('#repo_descripcion').val());
    var id          = $('#repo_id').val();
    var esNuevo     = (id === '' || id === '0');

    if (!descripcion) {
        $('#alertaRepo').html('<div class="alert alert-warning py-2">La descripción es obligatoria.</div>');
        return;
    }
    if (esNuevo && (!$('#repo_fichero')[0].files.length || $('#repo_fichero')[0].files[0].size === 0)) {
        $('#alertaRepo').html('<div class="alert alert-warning py-2">Debes seleccionar un fichero.</div>');
        return;
    }

    var formData = new FormData();
    formData.append('descripcion', descripcion);
    formData.append('directorio',  $('#repo_directorio').val());
    if ($('#repo_fichero')[0].files.length) {
        formData.append('fichero', $('#repo_fichero')[0].files[0]);
    }

    var url = API_REPO + '?action=' + (esNuevo ? 'crear' : 'editar');
    if (!esNuevo) formData.append('id', id);

    $.ajax({
        url: url, type: 'POST',
        data: formData, processData: false, contentType: false,
        success: function (res) {
            if (res.ok) {
                cancelarFormRepo();
                _tablRepo.ajax.reload(null, false);
                cargarDirectorios();
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: esNuevo ? 'Fichero subido correctamente' : 'Fichero actualizado',
                    showConfirmButton: false, timer: 2000 });
            } else {
                $('#alertaRepo').html('<div class="alert alert-danger py-2">' + escHtml(res.error) + '</div>');
            }
        },
        error: function () {
            $('#alertaRepo').html('<div class="alert alert-danger py-2">Error de comunicación con el servidor.</div>');
        }
    });
}

// ─────────────────────────────────────────────
// ELIMINAR
// ─────────────────────────────────────────────
function confirmarEliminar(tr) {
    var data = _tablRepo.row(tr).data();
    var desc = data[2] || 'este fichero';
    Swal.fire({
        title: '¿Eliminar fichero?',
        html: 'Se eliminará <strong>' + escHtml(desc) + '</strong> del servidor.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post(API_REPO + '?action=eliminar', { id: data[0] }, function (res) {
            if (res.ok) {
                _tablRepo.ajax.reload(null, false);
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: 'Fichero eliminado', showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire('Error', res.error, 'error');
            }
        });
    });
}

// ─────────────────────────────────────────────
// COPIAR URL
// ─────────────────────────────────────────────
function copiarUrlDeFila(tr) {
    var data = _tablRepo.row(tr).data();
    var id   = data[0];
    $.post(API_REPO + '?action=obtener', { id: id }, function (res) {
        if (!res.ok) { Swal.fire('Error', res.error, 'error'); return; }
        var url = window.location.origin + window.location.pathname.replace(/\/[^/]*$/, '/') + res.data.url;
        navigator.clipboard.writeText(url).then(function () {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                title: 'URL copiada al portapapeles', showConfirmButton: false, timer: 2000 });
        }).catch(function () {
            Swal.fire('URL del fichero', url, 'info');
        });
    });
}

// ─────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────
function renderPreview(row) {
    var nombre = row[4] || '';
    // directorio está en row[3], nombre_fichero en row[4]
    // No tenemos la URL completa aquí, solo mostramos icono por tipo
    var esImagen = /\.(jpg|jpeg|png|gif|webp|svg|bmp|ico)$/i.test(nombre);
    if (esImagen) {
        return '<div class="repo-icon"><i class="fas fa-image text-primary"></i></div>';
    }
    var iconos = {
        pdf: 'fas fa-file-pdf text-danger',
        doc: 'fas fa-file-word text-primary', docx: 'fas fa-file-word text-primary',
        xls: 'fas fa-file-excel text-success', xlsx: 'fas fa-file-excel text-success',
        ppt: 'fas fa-file-powerpoint text-warning', pptx: 'fas fa-file-powerpoint text-warning',
        zip: 'fas fa-file-archive text-secondary', rar: 'fas fa-file-archive text-secondary',
        mp4: 'fas fa-file-video text-info', mp3: 'fas fa-file-audio text-info',
        txt: 'fas fa-file-alt text-muted', csv: 'fas fa-file-csv text-success',
    };
    var ext = nombre.split('.').pop().toLowerCase();
    var cls = iconos[ext] || 'fas fa-file text-muted';
    return '<div class="repo-icon"><i class="' + cls + '"></i></div>';
}

function formatBytes(bytes) {
    bytes = parseInt(bytes);
    if (isNaN(bytes)) return '—';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Preview al seleccionar fichero
$('#repo_fichero').on('change', function () {
    var f = this.files[0];
    $('#lblFicheroNombre').text(f ? f.name : 'Seleccionar fichero...');
});
