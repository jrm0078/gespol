/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX = 'inc/turnos/ajax_turnos.php';
    var dtFestivos, dtReducciones;
    var diasMes = [
        'Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
    ];

    function getEjercicio() { return $('#selEjercicio').val(); }

    // ── CARGA COMPLETA ───────────────────────────────────────
    function cargarTodo() {
        var ej = getEjercicio();
        $.post(AJAX + '?action=listar_festivos',   { ejercicio: ej }, function (r) {
            renderCalendario(ej, r.data || []);
            renderTablaFestivos(r.data || []);
        });
        $.post(AJAX + '?action=listar_reducciones', { ejercicio: ej }, function (r) {
            renderTablaReducciones(r.data || []);
        });
    }

    cargarTodo();
    $('#selEjercicio').on('change', cargarTodo);

    // ── CALENDARIO VISUAL ────────────────────────────────────
    function renderCalendario(ejercicio, festivos) {
        // Índice de festivos: { 'YYYY-MM-DD': tipo }
        var idx = {};
        festivos.forEach(function (f) { idx[f.fecha] = f.festivo_tipo; });

        var html = '';
        for (var m = 0; m < 12; m++) {
            var fecha1 = new Date(ejercicio, m, 1);
            var diasTotal = new Date(ejercicio, m + 1, 0).getDate();
            var primerDia = (fecha1.getDay() + 6) % 7; // 0=Lun

            html += '<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 cal-mes">';
            html += '<div class="mes-titulo">' + diasMes[m] + ' ' + ejercicio + '</div>';
            html += '<table><thead><tr>';
            ['L','M','X','J','V','S','D'].forEach(function (d) { html += '<th>' + d + '</th>'; });
            html += '</tr></thead><tbody><tr>';

            for (var i = 0; i < primerDia; i++) html += '<td class="otro-mes"></td>';

            for (var dia = 1; dia <= diasTotal; dia++) {
                var col = (primerDia + dia - 1) % 7;
                var fechaStr = ejercicio + '-' + pad2(m + 1) + '-' + pad2(dia);
                var cls = '';
                if (idx[fechaStr] === 'nacional') cls = 'festivo-nacional';
                else if (idx[fechaStr] === 'local') cls = 'festivo-local';
                else if (idx[fechaStr] === 'convenio') cls = 'festivo-convenio';
                else if (col === 5 || col === 6) cls = 'table-secondary'; // fin de semana

                var title = idx[fechaStr] ? ' title="' + (idx[fechaStr]) + '"' : '';
                html += '<td class="' + cls + '"' + title + '>' + dia + '</td>';

                if (col === 6 && dia < diasTotal) html += '</tr><tr>';
            }

            // Cerrar fila
            var ultimoCol = (primerDia + diasTotal - 1) % 7;
            for (var j = ultimoCol + 1; j < 7; j++) html += '<td class="otro-mes"></td>';
            html += '</tr></tbody></table></div>';
        }
        $('#calendarioAnual').html(html);
    }

    function pad2(n) { return n < 10 ? '0' + n : '' + n; }

    // ── TABLA FESTIVOS ───────────────────────────────────────
    function renderTablaFestivos(data) {
        if (dtFestivos) { dtFestivos.clear().destroy(); }
        dtFestivos = $('#tblFestivos').DataTable({
            data: data,
            language: { url: 'libs/datatables/Spanish.json' },
            pageLength: 25,
            order: [[0,'asc']],
            columns: [
                { data: 'fecha' },
                {
                    data: 'festivo_tipo',
                    render: function (v) {
                        var map = { nacional:'<span class="badge" style="background:#dc3545;color:#fff">Nacional</span>',
                                    local:'<span class="badge" style="background:#fd7e14;color:#fff">Local</span>',
                                    convenio:'<span class="badge" style="background:#6f42c1;color:#fff">Convenio</span>' };
                        return map[v] || v;
                    }
                },
                { data: 'festivo_desc' },
                {
                    data: null, orderable: false,
                    render: function (d) {
                        return '<button class="btn btn-xs btn-outline-secondary btn-edit-fes mr-1" data-id="' + d.id + '">' +
                               '<i class="fas fa-pencil-alt"></i></button>' +
                               '<button class="btn btn-xs btn-outline-danger btn-del-fes" data-id="' + d.id + '">' +
                               '<i class="fas fa-trash-alt"></i></button>';
                    }
                }
            ]
        });
    }

    // ── TABLA REDUCCIONES ────────────────────────────────────
    function renderTablaReducciones(data) {
        if (dtReducciones) { dtReducciones.clear().destroy(); }
        dtReducciones = $('#tblReducciones').DataTable({
            data: data,
            language: { url: 'libs/datatables/Spanish.json' },
            pageLength: 10,
            order: [[1,'asc']],
            columns: [
                { data: 'descripcion' },
                { data: 'fecha_desde' },
                { data: 'fecha_hasta' },
                { data: 'reduccion_minutos', className: 'text-center' },
                {
                    data: 'aplica_sabado', className: 'text-center',
                    render: function (v) { return v==1 ? '<i class="fas fa-check text-success"></i>' : ''; }
                },
                {
                    data: 'aplica_domingo', className: 'text-center',
                    render: function (v) { return v==1 ? '<i class="fas fa-check text-success"></i>' : ''; }
                },
                {
                    data: null, orderable: false,
                    render: function (d) {
                        return '<button class="btn btn-xs btn-outline-secondary btn-edit-red mr-1" data-id="' + d.id + '">' +
                               '<i class="fas fa-pencil-alt"></i></button>' +
                               '<button class="btn btn-xs btn-outline-danger btn-del-red" data-id="' + d.id + '">' +
                               '<i class="fas fa-trash-alt"></i></button>';
                    }
                }
            ]
        });
    }

    // ── FESTIVOS: CRUD ───────────────────────────────────────
    $('#btnAddFestivo').on('click', function () { abrirFormFestivo(0); });

    $('#tblFestivos tbody').on('click', '.btn-edit-fes', function () {
        var id  = $(this).data('id');
        var row = dtFestivos.rows().data().toArray().find(function (r) { return r.id == id; });
        if (row) abrirFormFestivo(id, row);
    });

    $('#tblFestivos tbody').on('click', '.btn-del-fes', function () {
        var id = $(this).data('id');
        Swal.fire({ title:'¿Eliminar festivo?', icon:'warning', showCancelButton:true,
                    confirmButtonText:'Sí', cancelButtonText:'No', confirmButtonColor:'#dc3545' })
            .then(function (res) {
                if (!res.isConfirmed) return;
                $.post(AJAX + '?action=eliminar_festivo', { id: id }, function (r) {
                    mostrarRespuesta(r);
                    if (r.validacion === 'ok') cargarTodo();
                });
            });
    });

    $('#btnCancelFestivo').on('click', function () { $('#panelFormFestivo').slideUp(200); });

    $('#formFestivo').on('submit', function (e) {
        e.preventDefault();
        var payload = {
            id: $('#fesId').val(),
            ejercicio: getEjercicio(),
            fecha: $('#fesFecha').val(),
            festivo_tipo: $('#fesTipo').val(),
            festivo_desc: $.trim($('#fesDesc').val())
        };
        $.post(AJAX + '?action=guardar_festivo', payload, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') { $('#panelFormFestivo').slideUp(200); cargarTodo(); }
        });
    });

    function abrirFormFestivo(id, d) {
        $('#fesId').val(id);
        if (d) { $('#fesFecha').val(d.fecha); $('#fesTipo').val(d.festivo_tipo); $('#fesDesc').val(d.festivo_desc); }
        else   { $('#formFestivo')[0].reset(); }
        $('#panelFormFestivo').slideDown(200);
        $('html,body').animate({ scrollTop: $('#panelFormFestivo').offset().top - 60 }, 300);
    }

    // ── REDUCCIONES: CRUD ────────────────────────────────────
    $('#btnAddReduccion').on('click', function () { abrirFormReduccion(0); });

    $('#tblReducciones tbody').on('click', '.btn-edit-red', function () {
        var id  = $(this).data('id');
        var row = dtReducciones.rows().data().toArray().find(function (r) { return r.id == id; });
        if (row) abrirFormReduccion(id, row);
    });

    $('#tblReducciones tbody').on('click', '.btn-del-red', function () {
        var id = $(this).data('id');
        Swal.fire({ title:'¿Eliminar reducción?', icon:'warning', showCancelButton:true,
                    confirmButtonText:'Sí', cancelButtonText:'No', confirmButtonColor:'#dc3545' })
            .then(function (res) {
                if (!res.isConfirmed) return;
                $.post(AJAX + '?action=eliminar_reduccion', { id: id }, function (r) {
                    mostrarRespuesta(r);
                    if (r.validacion === 'ok') cargarTodo();
                });
            });
    });

    $('#btnCancelReduccion').on('click', function () { $('#panelFormReduccion').slideUp(200); });

    $('#formReduccion').on('submit', function (e) {
        e.preventDefault();
        var payload = {
            id: $('#redId').val(),
            ejercicio: getEjercicio(),
            descripcion: $.trim($('#redDesc').val()),
            fecha_desde: $('#redDesde').val(),
            fecha_hasta: $('#redHasta').val(),
            reduccion_minutos: $('#redMin').val(),
            aplica_sabado:  $('#redSab').is(':checked') ? 1 : 0,
            aplica_domingo: $('#redDom').is(':checked') ? 1 : 0
        };
        $.post(AJAX + '?action=guardar_reduccion', payload, function (r) {
            mostrarRespuesta(r);
            if (r.validacion === 'ok') { $('#panelFormReduccion').slideUp(200); cargarTodo(); }
        });
    });

    function abrirFormReduccion(id, d) {
        $('#redId').val(id);
        if (d) {
            $('#redDesc').val(d.descripcion);
            $('#redDesde').val(d.fecha_desde);
            $('#redHasta').val(d.fecha_hasta);
            $('#redMin').val(d.reduccion_minutos);
            $('#redSab').prop('checked', d.aplica_sabado == 1);
            $('#redDom').prop('checked', d.aplica_domingo == 1);
        } else {
            $('#formReduccion')[0].reset();
        }
        $('#panelFormReduccion').slideDown(200);
        $('html,body').animate({ scrollTop: $('#panelFormReduccion').offset().top - 60 }, 300);
    }

    function mostrarRespuesta(r) {
        var icon = r.validacion === 'ok' ? 'success' : (r.validacion === 'warning' ? 'warning' : 'error');
        var msg  = r.mensaje || r.error || '';
        if (msg) Swal.fire({ icon: icon, title: msg, timer: 2000, showConfirmButton: false });
    }
});
