/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX = 'inc/turnos/ajax_turnos.php';

    // Estado global del cuadrante
    var _cuadrante = null;   // { id, ejercicio, mes, estado }
    var _equipos   = [];     // [ { id, codigo, nombre, agentes:[...] } ]
    var _festivos  = {};     // { 'YYYY-MM-DD': { tipo, desc } }
    var _codigos   = [];     // [ { codigo, descripcion, color, computa, … } ]
    var _celdas    = {};     // { 'numagente_YYYY-MM-DD': {codigo,horas,obs,es_excepcion} }
    var _pendientes= {};     // celdas modificadas pendientes de guardar
    var _codigoAct = null;   // código seleccionado en panel lateral
    var _selCeldas = {};     // celdas seleccionadas { key: {numagente,fecha,id_equipo} }

    // Para modal celda
    var _celdaActual = null; // {numagente, fecha, id_equipo, agenteNombre}

    var MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    var DIAS_SEMANA = ['L','M','X','J','V','S','D'];

    // ─────────────────────────────────────────────────────────────
    // CARGA DEL CUADRANTE
    // ─────────────────────────────────────────────────────────────

    $('#btnCargar').on('click', cargarCuadrante);

    // Cargar mes actual al arrancar
    (function () {
        var hoy = new Date();
        $('#selEjercicio').val(hoy.getFullYear());
        $('#selMes').val(hoy.getMonth() + 1);
        cargarCuadrante();
    })();

    function cargarCuadrante() {
        var ej  = parseInt($('#selEjercicio').val(), 10);
        var mes = parseInt($('#selMes').val(), 10);

        $('#areaEditor').addClass('d-none');
        $('#divCargando').removeClass('d-none');
        _pendientes = {};
        _selCeldas  = {};

        $.post(AJAX + '?action=cargar_cuadrante_mes', { ejercicio: ej, mes: mes }, function (r) {
            $('#divCargando').addClass('d-none');
            if (r.validacion !== 'ok') { mostrarToast('error', r.error || 'Error cargando cuadrante'); return; }

            _cuadrante = r.cuadrante;
            _equipos   = r.equipos   || [];
            _festivos  = r.festivos  || {};
            _codigos   = r.codigos   || [];
            _celdas    = r.celdas    || {};

            renderPanelCodigos();
            renderCuadrante(ej, mes);
            actualizarEstadoBadge();
            $('#areaEditor').removeClass('d-none');
        }, 'json').fail(function () {
            $('#divCargando').addClass('d-none');
            mostrarToast('error', 'Error de conexión');
        });
    }

    // ─────────────────────────────────────────────────────────────
    // PANEL LATERAL – CÓDIGOS
    // ─────────────────────────────────────────────────────────────

    function renderPanelCodigos() {
        var html = '';
        _codigos.forEach(function (c) {
            html += '<button class="btn btn-block btn-sm mb-1 btn-selcod text-left" data-cod="' + esc(c.codigo) + '" ' +
                    'style="background:' + c.color + ';color:#fff;padding:3px 8px;font-size:.72rem;border:none;">' +
                    '<strong>' + esc(c.codigo) + '</strong> ' + esc(c.descripcion) +
                    '</button>';
        });
        html += '<button class="btn btn-block btn-sm btn-selcod btn-outline-secondary text-left mt-1" data-cod="" ' +
                'style="font-size:.72rem"><i class="fas fa-eraser mr-1"></i>Limpiar</button>';
        $('#listaCodigos').html(html);

        // Leyenda en la parte inferior
        var ley = '';
        _codigos.forEach(function (c) {
            ley += '<span class="leyenda-item">' +
                   '<span class="leyenda-box" style="background:' + c.color + '"></span>' +
                   esc(c.codigo) + ' ' + esc(c.descripcion) +
                   '</span>';
        });
        $('#leyendaCodigos').html(ley);
    }

    $(document).on('click', '.btn-selcod', function () {
        _codigoAct = $(this).data('cod') || null;
        $('.btn-selcod').css('outline','none');
        $(this).css('outline', '2px solid #333');
        $('#lblSelCodigo').text(_codigoAct || '— vacía —');
    });

    // ─────────────────────────────────────────────────────────────
    // RENDER CUADRANTE
    // ─────────────────────────────────────────────────────────────

    function renderCuadrante(ejercicio, mes) {
        var diasMes = new Date(ejercicio, mes, 0).getDate(); // Días del mes

        // Construir cabeceras
        var thDias  = '<th class="col-agente">Agente / Equipo</th>';
        var thDows  = '<th class="col-agente"></th>';
        var thFes   = '<th class="col-agente"></th>';
        for (var d = 1; d <= diasMes; d++) {
            var fecha = pad4(ejercicio) + '-' + pad2(mes) + '-' + pad2(d);
            var dt    = new Date(ejercicio, mes - 1, d);
            var dow   = (dt.getDay() + 6) % 7; // 0=lun
            var fes   = _festivos[fecha];
            var clsH  = fes ? 'fes-' + fes.tipo : (dow === 5 ? 'sab' : dow === 6 ? 'dom' : '');
            var bgH   = fes ? (fes.tipo === 'nacional' ? '#dc3545' : fes.tipo === 'local' ? '#fd7e14' : '#6f42c1')
                            : (dow === 5 ? '#b8c0cc' : dow === 6 ? '#adb5bd' : '#0066B3');
            thDias  += '<th style="min-width:26px;max-width:26px;width:26px;background:' + bgH + '">' + d + '</th>';
            thDows  += '<th style="background:' + bgH + '">' + DIAS_SEMANA[dow] + '</th>';
            var fLabel = fes ? (fes.tipo === 'nacional' ? 'N' : fes.tipo === 'local' ? 'L' : 'C') : '';
            thFes   += '<th style="background:' + bgH + '">' + fLabel + '</th>';
        }
        thDias += '<th class="col-total">Total</th>';
        thDows += '<th class="col-total"></th>';
        thFes  += '<th class="col-total"></th>';

        var thead = '<tr class="fila-dias">'  + thDias + '</tr>' +
                    '<tr class="fila-dow">'   + thDows + '</tr>' +
                    '<tr class="fila-fes">'   + thFes  + '</tr>';

        // Construir filas
        var tbody = '';
        _equipos.forEach(function (eq) {
            // Fila cabecera equipo
            tbody += '<tr class="fila-equipo">' +
                     '<td class="col-agente" colspan="' + (diasMes + 2) + '">' +
                     '<i class="fas fa-users mr-1"></i>' + esc(eq.codigo) + ' – ' + esc(eq.nombre) +
                     ' <small class="text-muted">(' + eq.agentes.length + ' agentes)</small>' +
                     '</td></tr>';

            // Filas de cada agente
            eq.agentes.forEach(function (ag) {
                var jornadasAg = 0;
                var fds = {};
                var festTrab = 0;
                var trCeldas = '<td class="col-agente" title="' + esc(ag.nombre_completo) + '">' +
                               '<small>' + esc(ag.nombre_completo) + '</small></td>';

                for (var d2 = 1; d2 <= diasMes; d2++) {
                    var fec2 = pad4(ejercicio) + '-' + pad2(mes) + '-' + pad2(d2);
                    var dt2  = new Date(ejercicio, mes - 1, d2);
                    var dow2 = (dt2.getDay() + 6) % 7;
                    var fes2 = _festivos[fec2];
                    var key  = ag.numagente + '_' + fec2;
                    var celda = _celdas[key] || _pendientes[key] || null;
                    var cod   = celda ? celda.codigo : null;
                    var exc   = celda ? celda.es_excepcion : 0;

                    // Clases CSS de fondo del día
                    var clsDay = 'celda-dia';
                    if (fes2) clsDay += ' dia-fes-' + fes2.tipo;
                    else if (dow2 === 5) clsDay += ' dia-sab';
                    else if (dow2 === 6) clsDay += ' dia-dom';

                    // Contenido de la celda
                    var inner = '';
                    if (cod) {
                        var codigoDef = buscarCodigo(cod);
                        var color = codigoDef ? codigoDef.color : '#888';
                        inner = '<span class="badge-cod" style="background:' + color + '">' + esc(cod) + '</span>';

                        // Conteo totales
                        if (codigoDef && codigoDef.computa && codigoDef.tipo_computo === 'normal') {
                            jornadasAg++;
                            if (fes2) festTrab++;
                            if (dow2 >= 5) { fds[Math.floor((d2 + (dow2 === 6 ? 0 : 1)) / 7)] = true; }
                        }
                    }
                    if (exc) {
                        clsDay += ' text-warning';
                        inner += '<span title="Excepción" style="font-size:.55rem">⚑</span>';
                    }

                    trCeldas += '<td class="' + clsDay + '" ' +
                                'data-numagente="' + ag.numagente + '" ' +
                                'data-id-equipo="' + eq.id + '" ' +
                                'data-fecha="' + fec2 + '" ' +
                                'data-nombre="' + esc(ag.nombre_completo) + '" ' +
                                '>' + inner + '</td>';
                }
                trCeldas += '<td class="col-total">' + jornadasAg + '</td>';
                tbody += '<tr>' + trCeldas + '</tr>';
            });
        });

        $('#tblCuadrante thead').html(thead);
        $('#tblCuadrante tbody').html(tbody);
        actualizarContadorPendientes();
    }

    // ─────────────────────────────────────────────────────────────
    // INTERACCIÓN – CLIC EN CELDA
    // ─────────────────────────────────────────────────────────────

    // Clic simple: asigna el código activo directamente, o abre modal si no hay código activo
    $(document).on('click', '.celda-dia', function (e) {
        var $td       = $(this);
        var numagente = parseInt($td.data('numagente'), 10);
        var fecha     = $td.data('fecha');
        var id_equipo = parseInt($td.data('id-equipo'), 10);
        var nombre    = $td.data('nombre');

        if (_cuadrante && (_cuadrante.estado === 'cerrado' || _cuadrante.estado === 'contabilizado')) {
            mostrarToast('warning', 'El cuadrante está cerrado');
            return;
        }

        if (_codigoAct !== null) {
            // Aplicar código activo directamente
            aplicarCodigoCelda(numagente, id_equipo, fecha, _codigoAct);
            actualizarCeldaDOM($td, _codigoAct, 0);
        } else {
            // Abrir modal de edición
            _celdaActual = { numagente: numagente, id_equipo: id_equipo, fecha: fecha, nombre: nombre };
            abrirModalCelda(_celdaActual);
        }
    });

    // Doble clic: abrir modal siempre
    $(document).on('dblclick', '.celda-dia', function (e) {
        if (_cuadrante && (_cuadrante.estado === 'cerrado' || _cuadrante.estado === 'contabilizado')) return;
        var $td = $(this);
        _celdaActual = {
            numagente: parseInt($td.data('numagente'), 10),
            id_equipo: parseInt($td.data('id-equipo'), 10),
            fecha:     $td.data('fecha'),
            nombre:    $td.data('nombre')
        };
        abrirModalCelda(_celdaActual);
    });

    function aplicarCodigoCelda(numagente, id_equipo, fecha, codigo) {
        var key   = numagente + '_' + fecha;
        var exist = _celdas[key] || {};
        var nuevo = {
            numagente:    numagente,
            id_equipo:    id_equipo,
            fecha:        fecha,
            codigo:       codigo,
            horas:        exist.horas || null,
            observaciones:exist.observaciones || '',
            es_excepcion: exist.es_excepcion || 0
        };
        _celdas[key]    = nuevo;
        _pendientes[key] = nuevo;
        actualizarContadorPendientes();
    }

    function actualizarCeldaDOM($td, codigo, es_excepcion) {
        var inner = '';
        if (codigo) {
            var codigoDef = buscarCodigo(codigo);
            var color = codigoDef ? codigoDef.color : '#888';
            inner = '<span class="badge-cod" style="background:' + color + '">' + esc(codigo) + '</span>';
        }
        if (es_excepcion) inner += '<span title="Excepción" style="font-size:.55rem">⚑</span>';
        $td.html(inner);
        // Recalcular totales de la fila
        recalcularTotalFila($td.closest('tr'));
    }

    function recalcularTotalFila($tr) {
        var jornadas = 0;
        $tr.find('.celda-dia').each(function () {
            var $t   = $(this);
            var fec  = $t.data('fecha');
            var ag   = parseInt($t.data('numagente'), 10);
            var key  = ag + '_' + fec;
            var cel  = _celdas[key];
            if (cel && cel.codigo) {
                var cdef = buscarCodigo(cel.codigo);
                if (cdef && cdef.computa && cdef.tipo_computo === 'normal') jornadas++;
            }
        });
        $tr.find('.col-total').text(jornadas);
    }

    // ─────────────────────────────────────────────────────────────
    // MODAL EDITAR CELDA
    // ─────────────────────────────────────────────────────────────

    function abrirModalCelda(c) {
        var key    = c.numagente + '_' + c.fecha;
        var actual = _celdas[key] || {};

        $('#lblCeldaInfo').text(c.nombre + ' – ' + formatFecha(c.fecha));

        // Llenar select de códigos
        var opts = '<option value="">— vacía —</option>';
        _codigos.forEach(function (cd) {
            var sel = actual.codigo === cd.codigo ? ' selected' : '';
            opts += '<option value="' + esc(cd.codigo) + '"' + sel + '>' + esc(cd.codigo) + ' – ' + esc(cd.descripcion) + '</option>';
        });
        $('#celdaCodigo').html(opts);
        $('#celdaHoras').val(actual.horas || '');
        $('#celdaObs').val(actual.observaciones || '');
        $('#modalCelda').modal('show');
    }

    $('#btnGuardarCelda').on('click', function () {
        if (!_celdaActual) return;
        var c      = _celdaActual;
        var codigo = $('#celdaCodigo').val();
        var key    = c.numagente + '_' + c.fecha;
        var exist  = _celdas[key] || {};

        var nuevo = {
            numagente:     c.numagente,
            id_equipo:     c.id_equipo,
            fecha:         c.fecha,
            codigo:        codigo || null,
            horas:         $('#celdaHoras').val() !== '' ? parseFloat($('#celdaHoras').val()) : null,
            observaciones: $.trim($('#celdaObs').val()),
            es_excepcion:  exist.es_excepcion || 0
        };
        _celdas[key]    = nuevo;
        _pendientes[key] = nuevo;

        // Actualizar DOM
        var $td = $('[data-numagente="' + c.numagente + '"][data-fecha="' + c.fecha + '"]');
        actualizarCeldaDOM($td, codigo, nuevo.es_excepcion);
        actualizarContadorPendientes();
        $('#modalCelda').modal('hide');
    });

    $('#btnBorrarCelda').on('click', function () {
        if (!_celdaActual) return;
        $('#celdaCodigo').val('');
        $('#celdaHoras').val('');
        $('#celdaObs').val('');
        $('#btnGuardarCelda').click();
    });

    // ─────────────────────────────────────────────────────────────
    // GUARDAR PENDIENTES
    // ─────────────────────────────────────────────────────────────

    $('#btnGuardarPendientes').on('click', function () {
        var cells = Object.values(_pendientes);
        if (!cells.length) { mostrarToast('info', 'No hay cambios pendientes'); return; }
        if (!_cuadrante) return;

        $.post(AJAX + '?action=guardar_lote_celdas', {
            id_cuadrante: _cuadrante.id,
            cells: JSON.stringify(cells)
        }, function (r) {
            if (r.validacion === 'ok') {
                _pendientes = {};
                actualizarContadorPendientes();
                mostrarToast('success', 'Guardado correctamente');
            } else {
                mostrarToast('error', r.error || r.mensaje || 'Error al guardar');
            }
        }, 'json');
    });

    // ─────────────────────────────────────────────────────────────
    // LIMPIAR SELECCIÓN
    // ─────────────────────────────────────────────────────────────

    $('#btnLimpiarSel').on('click', function () {
        // Limpia el código activo del panel lateral
        _codigoAct = null;
        $('.btn-selcod').css('outline', 'none');
        $('#lblSelCodigo').text('Ninguno');
        mostrarToast('info', 'Código activo borrado');
    });

    // ─────────────────────────────────────────────────────────────
    // MODAL PATRÓN EQUIPO
    // ─────────────────────────────────────────────────────────────

    $('#btnPatron').on('click', function () {
        // Llenar equipo select
        var opts = '';
        _equipos.forEach(function (eq) {
            opts += '<option value="' + eq.id + '">' + esc(eq.codigo) + ' – ' + esc(eq.nombre) + '</option>';
        });
        $('#patronEquipo').html(opts);

        // Llenar código select
        var optsC = '<option value="">— limpiar —</option>';
        _codigos.forEach(function (c) {
            optsC += '<option value="' + esc(c.codigo) + '">' + esc(c.codigo) + ' – ' + esc(c.descripcion) + '</option>';
        });
        $('#patronCodigo').html(optsC);

        $('#modalPatron').modal('show');
    });

    $('#btnAplicarPatron').on('click', function () {
        if (!_cuadrante) return;
        var id_equipo = parseInt($('#patronEquipo').val(), 10);
        var codigo    = $('#patronCodigo').val();
        var diasSel   = [];
        $('#patronDias input:checked').each(function () {
            diasSel.push(parseInt($(this).val(), 10));
        });
        var soloVacias = $('#patronSoloVacias').is(':checked') ? 1 : 0;

        if (!diasSel.length) { mostrarToast('warning', 'Selecciona al menos un día de la semana'); return; }

        $('#btnAplicarPatron').prop('disabled', true);
        $.post(AJAX + '?action=aplicar_patron_equipo', {
            id_cuadrante:  _cuadrante.id,
            id_equipo:     id_equipo,
            codigo_patron: codigo,
            dias_semana:   JSON.stringify(diasSel),
            solo_vacias:   soloVacias
        }, function (r) {
            $('#btnAplicarPatron').prop('disabled', false);
            if (r.validacion === 'ok') {
                $('#modalPatron').modal('hide');
                mostrarToast('success', r.mensaje || 'Patrón aplicado');
                cargarCuadrante(); // Recargar para ver cambios
            } else {
                mostrarToast('error', r.error || r.mensaje || 'Error');
            }
        }, 'json').fail(function () {
            $('#btnAplicarPatron').prop('disabled', false);
            mostrarToast('error', 'Error de conexión');
        });
    });

    // ─────────────────────────────────────────────────────────────
    // ESTADO / BADGE
    // ─────────────────────────────────────────────────────────────

    function actualizarEstadoBadge() {
        if (!_cuadrante) return;
        var cls = { 'borrador': 'badge-secondary', 'cerrado': 'badge-warning', 'contabilizado': 'badge-success' };
        var lbl = { 'borrador': 'Borrador', 'cerrado': 'Cerrado', 'contabilizado': 'Contabilizado' };
        $('#estadoBadge').removeClass('d-none badge-secondary badge-warning badge-success')
                         .addClass(cls[_cuadrante.estado] || 'badge-secondary')
                         .text(lbl[_cuadrante.estado] || _cuadrante.estado);
    }

    function actualizarContadorPendientes() {
        var n = Object.keys(_pendientes).length;
        $('#cntPendientes').toggleClass('d-none', n === 0).text(n);
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    function buscarCodigo(cod) {
        return _codigos.find(function (c) { return c.codigo === cod; }) || null;
    }

    function pad2(n) { return n < 10 ? '0' + n : '' + n; }
    function pad4(n) { return ('000' + n).slice(-4); }

    function formatFecha(f) {
        if (!f) return '';
        var p = f.split('-');
        if (p.length !== 3) return f;
        return p[2] + '/' + p[1] + '/' + p[0];
    }

    function esc(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function mostrarToast(tipo, msg) {
        Swal.fire({ type: tipo, title: msg, timer: 2200, showConfirmButton: false });
    }
});
