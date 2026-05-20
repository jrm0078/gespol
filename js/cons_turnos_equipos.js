/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX = 'inc/turnos/ajax_turnos.php';
    var dtEquipos;
    var currentId = 0;

    // ── INIT TABLA ──────────────────────────────────────────
    function initTabla() {
        $.post(AJAX + '?action=listar_equipos', {}, function (r) {
            if (dtEquipos) { dtEquipos.clear().destroy(); }
            dtEquipos = $('#tblEquipos').DataTable({
                data: r.data || [],
                language: { url: 'libs/datatables/Spanish.json' },
                pageLength: 25,
                columns: [
                    {
                        data: null, orderable: false, searchable: false, width: '40px',
                        render: function (d) {
                            return '<button class="btn btn-sm btn-outline-secondary btn-edit-equipo" data-id="' + d.id + '" title="Editar">' +
                                '<i class="fas fa-pencil-alt"></i></button>';
                        }
                    },
                    { data: 'codigo' },
                    { data: 'nombre' },
                    { data: 'num_agentes', className: 'text-center' },
                    { data: 'orden', className: 'text-center' },
                    {
                        data: 'activo', className: 'text-center',
                        render: function (v) {
                            return v == 1
                                ? '<span class="badge-activo">Activo</span>'
                                : '<span class="badge-inactivo">Inactivo</span>';
                        }
                    },
                    {
                        data: null, orderable: false, searchable: false,
                        render: function (d) {
                            return '<button class="btn btn-sm btn-outline-danger btn-del-equipo" data-id="' + d.id + '" title="Eliminar">' +
                                '<i class="fas fa-trash-alt"></i></button>';
                        }
                    }
                ]
            });
        });
    }

    initTabla();

    // ── NUEVO EQUIPO ────────────────────────────────────────
    $('#btnAddEquipo').on('click', function () {
        abrirForm(0);
    });

    // ── EDITAR ──────────────────────────────────────────────
    $('#tblEquipos tbody').on('click', '.btn-edit-equipo', function () {
        var id = $(this).data('id');
        $.post(AJAX + '?action=obtener_equipo', { id: id }, function (r) {
            var d = r.data && r.data[0] ? r.data[0] : null;
            if (!d) return;
            abrirForm(id, d);
        });
    });

    // ── ELIMINAR ────────────────────────────────────────────
    $('#tblEquipos tbody').on('click', '.btn-del-equipo', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar equipo?',
            text: 'Se eliminarán también las asignaciones de agentes.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (res) {
            if (!res.value) return;
            $.post(AJAX + '?action=eliminar_equipo', { id: id }, function (r) {
                mostrarRespuesta(r);
                if (r.validacion === 'ok') initTabla();
            });
        });
    });

    // ── CANCEL ──────────────────────────────────────────────
    $('#btnCancelEquipo').on('click', function () {
        cerrarForm();
    });

    // ── GUARDAR ─────────────────────────────────────────────
    $('#formEquipo').on('submit', function (e) {
        e.preventDefault();
        var payload = {
            id: $('#eqId').val(),
            codigo: $.trim($('#eqCodigo').val()),
            nombre: $.trim($('#eqNombre').val()),
            orden: $('#eqOrden').val(),
            activo: $('#eqActivo').val()
        };
        $.post(AJAX + '?action=guardar_equipo', payload, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') {
                currentId = r.id;
                $('#eqId').val(r.id);
                $('#tituloFormEquipo').html('<i class="fas fa-edit mr-1"></i>Equipo #' + r.id);
                $('#btnAddAgenteEquipo').show();
                cargarAgentesEquipo(r.id);
                initTabla();
            }
        });
    });

    // ── HELPERS FORMULARIO ───────────────────────────────────
    function abrirForm(id, d) {
        currentId = id;
        $('#eqId').val(id);
        if (d) {
            $('#eqCodigo').val(d.codigo);
            $('#eqNombre').val(d.nombre);
            $('#eqOrden').val(d.orden);
            $('#eqActivo').val(d.activo);
        } else {
            $('#formEquipo')[0].reset();
        }
        if (id > 0) {
            $('#tituloFormEquipo').html('<i class="fas fa-edit mr-1"></i>Editar equipo #' + id);
            $('#btnAddAgenteEquipo').show();
            cargarAgentesEquipo(id);
        } else {
            $('#tituloFormEquipo').html('<i class="fas fa-plus mr-1"></i>Nuevo equipo');
            $('#btnAddAgenteEquipo').hide();
            $('#zonaAgentesEquipo').html('<em class="text-muted small">Guarda el equipo primero para asignar agentes.</em>');
        }
        $('#panelFormEquipo').slideDown(200);
        $('html,body').animate({ scrollTop: $('#panelFormEquipo').offset().top - 60 }, 300);
    }

    function cerrarForm() {
        $('#panelFormEquipo').slideUp(200);
        $('#rowAddAgente').hide();
        currentId = 0;
    }

    // ── AGENTES DEL EQUIPO ───────────────────────────────────
    function cargarAgentesEquipo(id_equipo) {
        $.post(AJAX + '?action=listar_agentes_equipo', { id_equipo: id_equipo }, function (r) {
            var html = '';
            if (!r.data || r.data.length === 0) {
                html = '<em class="text-muted small">Sin agentes asignados.</em>';
            } else {
                r.data.forEach(function (a) {
                    html += '<span class="agente-chip">' +
                        '<strong>' + a.numagente + '</strong>' +
                        (a.indicativo ? '&nbsp;' + a.indicativo : '') +
                        '&nbsp;' + a.nombre +
                        '<button class="btn-rm btn-quitar-agente" data-id="' + a.id + '" title="Quitar">&times;</button>' +
                        '</span>';
                });
            }
            $('#zonaAgentesEquipo').html(html);
        });
    }

    // Añadir agente: mostrar combo
    $('#btnAddAgenteEquipo').on('click', function () {
        cargarComboAgentesLibres();
        $('#rowAddAgente').show();
    });

    function cargarComboAgentesLibres() {
        $.post(AJAX + '?action=combo_agentes', { id_equipo: currentId }, function (r) {
            var opts = '<option value="">-- Selecciona agente --</option>';
            (r.data || []).forEach(function (a) { opts += '<option value="' + a.id + '">' + a.texto + '</option>'; });
            $('#cmbAgenteLibre').html(opts);
        });
    }

    $('#btnCancelAddAgente').on('click', function () { $('#rowAddAgente').hide(); });

    $('#btnConfirmAddAgente').on('click', function () {
        var numagente = $('#cmbAgenteLibre').val();
        if (!numagente) { mostrarAviso('Selecciona un agente'); return; }
        $.post(AJAX + '?action=asignar_agente', { id_equipo: currentId, numagente: numagente, fecha_desde: '2026-01-01' }, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') {
                $('#rowAddAgente').hide();
                cargarAgentesEquipo(currentId);
                initTabla();
            }
        });
    });

    // Quitar agente
    $('#zonaAgentesEquipo').on('click', '.btn-quitar-agente', function () {
        var id = $(this).data('id');
        $.post(AJAX + '?action=quitar_agente', { id: id }, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') { cargarAgentesEquipo(currentId); initTabla(); }
        });
    });

    // ── HELPERS UI ────────────────────────────────────────────
    function mostrarRespuesta(r) {
        var icon = r.validacion === 'ok' ? 'success' : (r.validacion === 'warning' ? 'warning' : 'error');
        var msg  = r.mensaje || r.error || '';
        if (msg) Swal.fire({ type: icon, title: msg, timer: 2000, showConfirmButton: false });
    }

    function mostrarAviso(msg) {
        Swal.fire({ type: 'warning', title: msg, timer: 2000, showConfirmButton: false });
    }
});
