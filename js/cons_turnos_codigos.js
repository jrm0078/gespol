/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX = 'inc/turnos/ajax_turnos.php';
    var dtCodigos;

    function initTabla() {
        $.post(AJAX + '?action=listar_codigos', {}, function (r) {
            if (dtCodigos) { dtCodigos.clear().destroy(); }
            dtCodigos = $('#tblCodigos').DataTable({
                data: r.data || [],
                language: { url: 'libs/datatables/Spanish.json' },
                pageLength: 50,
                columns: [
                    {
                        data: null, orderable: false, searchable: false, width: '36px',
                        render: function (d) {
                            return '<button class="btn btn-sm btn-outline-secondary btn-edit-cod" data-id="' + d.id + '">' +
                                '<i class="fas fa-pencil-alt"></i></button>';
                        }
                    },
                    { data: 'codigo' },
                    { data: 'descripcion' },
                    {
                        data: 'color', className: 'text-center',
                        render: function (v, t, d) {
                            return '<span class="chip-color" style="background:' + v + '"></span>';
                        }
                    },
                    { data: 'tipo_computo', className: 'text-center' },
                    {
                        data: 'computa', className: 'text-center',
                        render: function (v) { return v == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>'; }
                    },
                    {
                        data: 'activo', className: 'text-center',
                        render: function (v) {
                            return v == 1
                                ? '<span class="badge badge-success">Activo</span>'
                                : '<span class="badge badge-danger">Inactivo</span>';
                        }
                    },
                    { data: 'orden', className: 'text-center' },
                    {
                        data: null, orderable: false, searchable: false, width: '70px',
                        render: function (d) {
                            return '<button class="btn btn-sm btn-outline-danger btn-del-cod" data-id="' + d.id + '" title="Eliminar">' +
                                '<i class="fas fa-trash-alt"></i></button>';
                        }
                    }
                ]
            });
        });
    }

    initTabla();

    // ── NUEVO ───────────────────────────────────────────────
    $('#btnAddCodigo').on('click', function () { abrirForm(0); });

    // ── EDITAR ──────────────────────────────────────────────
    $('#tblCodigos tbody').on('click', '.btn-edit-cod', function () {
        var id = $(this).data('id');
        // buscar en la tabla
        var row = dtCodigos.rows().data().toArray().find(function (r) { return r.id == id; });
        if (row) abrirForm(id, row);
    });

    // ── ELIMINAR ────────────────────────────────────────────
    $('#tblCodigos tbody').on('click', '.btn-del-cod', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar código?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            confirmButtonColor: '#dc3545'
        }).then(function (res) {
            if (!res.isConfirmed) return;
            $.post(AJAX + '?action=eliminar_codigo', { id: id }, function (r) {
                mostrarRespuesta(r);
                if (r.validacion === 'ok') initTabla();
            });
        });
    });

    // ── CANCEL ──────────────────────────────────────────────
    $('#btnCancelCodigo').on('click', function () { cerrarForm(); });

    // ── GUARDAR ─────────────────────────────────────────────
    $('#formCodigo').on('submit', function (e) {
        e.preventDefault();
        var payload = {
            id: $('#codId').val(),
            codigo: $.trim($('#codCodigo').val()),
            descripcion: $.trim($('#codDesc').val()),
            color: $('#codColor').val(),
            tipo_computo: $('#codTipo').val(),
            computa: $('#codComputa').is(':checked') ? 1 : 0,
            afecta_jornada: $('#codAfJornada').is(':checked') ? 1 : 0,
            afecta_extra: $('#codAfExtra').is(':checked') ? 1 : 0,
            requiere_observacion: $('#codReqObs').is(':checked') ? 1 : 0,
            orden: $('#codOrden').val(),
            activo: $('#codActivo').val()
        };
        $.post(AJAX + '?action=guardar_codigo', payload, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') { cerrarForm(); initTabla(); }
        });
    });

    // ── HELPERS FORM ─────────────────────────────────────────
    function abrirForm(id, d) {
        $('#codId').val(id);
        if (d) {
            $('#codCodigo').val(d.codigo);
            $('#codDesc').val(d.descripcion);
            $('#codColor').val(d.color || '#cccccc');
            $('#codTipo').val(d.tipo_computo);
            $('#codComputa').prop('checked', d.computa == 1);
            $('#codAfJornada').prop('checked', d.afecta_jornada == 1);
            $('#codAfExtra').prop('checked', d.afecta_extra == 1);
            $('#codReqObs').prop('checked', d.requiere_observacion == 1);
            $('#codOrden').val(d.orden);
            $('#codActivo').val(d.activo);
        } else {
            $('#formCodigo')[0].reset();
            $('#codColor').val('#cccccc');
            $('#codActivo').val('1');
        }
        $('#tituloFormCodigo').html(id > 0
            ? '<i class="fas fa-edit mr-1"></i>Editar código #' + id
            : '<i class="fas fa-plus mr-1"></i>Nuevo código');
        $('#panelFormCodigo').slideDown(200);
        $('html,body').animate({ scrollTop: $('#panelFormCodigo').offset().top - 60 }, 300);
    }

    function cerrarForm() { $('#panelFormCodigo').slideUp(200); }

    function mostrarRespuesta(r) {
        var icon = r.validacion === 'ok' ? 'success' : (r.validacion === 'warning' ? 'warning' : 'error');
        var msg = r.mensaje || r.error || '';
        if (msg) Swal.fire({ icon: icon, title: msg, timer: 2000, showConfirmButton: false });
    }
});
