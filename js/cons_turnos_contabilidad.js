/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX = 'inc/turnos/ajax_turnos.php';

    // ── Estado global ──────────────────────────────────────────
    var _cuadrante    = null;  // { id, ejercicio, mes, estado, ... }
    var _filas        = [];    // array de filas de contabilidad
    var _diasTeoricos = 0;     // días laborables teóricos del mes
    var _filaMod      = null;  // fila que se está editando en el modal

    var MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    // ─────────────────────────────────────────────────────────────
    // ARRANQUE: cargar Enero del año actual automáticamente
    // ─────────────────────────────────────────────────────────────
    $('#btnCargar').on('click', cargarContabilidad);

    (function () {
        var hoy = new Date();
        $('#selEjercicio').val(hoy.getFullYear());
        cargarContabilidad();
    })();

    // ─────────────────────────────────────────────────────────────
    // CARGA DE DATOS
    // ─────────────────────────────────────────────────────────────
    function cargarContabilidad() {
        var ej  = parseInt($('#selEjercicio').val(), 10);
        var mes = parseInt($('#selMes').val(), 10);

        $('#areaContabilidad').addClass('d-none');
        $('#divCargando').removeClass('d-none');

        $.post(AJAX + '?action=cargar_contabilidad', { ejercicio: ej, mes: mes }, function (r) {
            $('#divCargando').addClass('d-none');

            if (r.validacion === 'warning') {
                // No hay cuadrante aún para ese mes
                mostrarToast('warning', r.mensaje);
                return;
            }
            if (r.validacion !== 'ok') {
                mostrarToast('error', r.error || 'Error al cargar');
                return;
            }

            _cuadrante    = r.cuadrante;
            _filas        = r.filas || [];
            _diasTeoricos = parseInt(r.dias_teoricos, 10) || 0;

            actualizarEstadoBadge();
            actualizarBotones();
            renderTabla();

            // Último cálculo
            if (_filas.length > 0 && _filas[0].calculado_en) {
                $('#lblFechaCalculo').text(formatFechaHora(_filas[0].calculado_en));
                $('#lblUltimoCalculo').show();
            } else {
                $('#lblUltimoCalculo').hide();
            }
            $('#lblDiasTeoricos').text(_diasTeoricos || '–');

            $('#areaContabilidad').removeClass('d-none');
        }, 'json').fail(function () {
            $('#divCargando').addClass('d-none');
            mostrarToast('error', 'Error de conexión');
        });
    }

    // ─────────────────────────────────────────────────────────────
    // RENDER TABLA
    // ─────────────────────────────────────────────────────────────
    function renderTabla() {
        var tbody = '';
        var tfoot = '';

        // Totales globales
        var tot = {
            jornadas: 0, festivos: 0, fs: 0, vac: 0, bajas: 0,
            permisos: 0, form: 0, hred: 0, p01: 0, p040: 0,
            extras: 0, desc: 0, ajuste: 0
        };

        var equipoActual = null;

        _filas.forEach(function (f) {
            // ── Fila de cabecera de equipo ────────────────────
            var grupoKey = f.equipo_codigo + '|' + f.equipo_nombre;
            if (grupoKey !== equipoActual) {
                equipoActual = grupoKey;
                tbody += '<tr class="fila-equipo-contab">' +
                         '<td class="col-agente" colspan="16">' +
                         '<i class="fas fa-users mr-1"></i>' +
                         esc(f.equipo_codigo) + ' – ' + esc(f.equipo_nombre) +
                         '</td></tr>';
            }

            // ── Diferencia con días teóricos ──────────────────
            var jorn  = parseFloat(f.jornadas_mes) || 0;
            var dif   = jorn - _diasTeoricos;
            var difHtml = '';
            if (_diasTeoricos > 0) {
                var difCls = dif > 0 ? 'dif-positivo' : (dif < 0 ? 'dif-negativo' : '');
                var difStr = dif > 0 ? '+' + dif : '' + dif;
                difHtml = '<td class="col-auto ' + difCls + '">' + difStr + '</td>';
            } else {
                difHtml = '<td class="col-auto">–</td>';
            }

            // ── P01/P040 combinados ────────────────────────────
            var p01  = parseInt(f.p01_jornadas,  10) || 0;
            var p040 = parseInt(f.p040_jornadas, 10) || 0;
            var p01str = (p01 || p040) ? (p01 + '/' + p040) : '–';

            // ── Campos manuales (mostrar '–' si son 0) ────────
            var extH = parseFloat(f.extras_horas)  || 0;
            var desc = parseFloat(f.descuentos)    || 0;
            var ajus = parseFloat(f.ajuste_manual) || 0;
            var obs  = f.observaciones || '';

            // Acumular totales
            tot.jornadas += jorn;
            tot.festivos += parseInt(f.festivos_trabajados,     10) || 0;
            tot.fs       += parseInt(f.fines_semana_trabajados, 10) || 0;
            tot.vac      += parseInt(f.vacaciones_dias,         10) || 0;
            tot.bajas    += parseInt(f.bajas_dias,              10) || 0;
            tot.permisos += parseInt(f.permisos_dias,           10) || 0;
            tot.form     += parseInt(f.formacion_dias,          10) || 0;
            tot.hred     += parseFloat(f.horas_reduccion)            || 0;
            tot.p01      += p01;
            tot.p040     += p040;
            tot.extras   += extH;
            tot.desc     += desc;
            tot.ajuste   += ajus;

            // ── Fila de agente ────────────────────────────────
            tbody +=
                '<tr>' +
                // Agente
                '<td class="col-agente" title="' + esc(f.nombre_completo) + '">' +
                '<small>' + esc(f.nombre_completo) + '</small></td>' +
                // Calculados
                '<td class="col-auto">' + fmt(f.jornadas_mes) + '</td>' +
                difHtml +
                '<td class="col-auto">' + (parseInt(f.festivos_trabajados,10)||0)     + '</td>' +
                '<td class="col-auto">' + (parseInt(f.fines_semana_trabajados,10)||0) + '</td>' +
                '<td class="col-auto">' + (parseInt(f.vacaciones_dias,10)||0)         + '</td>' +
                '<td class="col-auto">' + (parseInt(f.bajas_dias,10)||0)              + '</td>' +
                '<td class="col-auto">' + (parseInt(f.permisos_dias,10)||0)           + '</td>' +
                '<td class="col-auto">' + (parseInt(f.formacion_dias,10)||0)          + '</td>' +
                '<td class="col-auto">' + fmtDec(f.horas_reduccion)                  + '</td>' +
                '<td class="col-auto">' + p01str                                      + '</td>' +
                // Manuales
                '<td class="col-manual">'          + (extH ? fmtDec(extH) : '–')         + '</td>' +
                '<td class="col-manual">'          + (desc ? fmtDec(desc) : '–')         + '</td>' +
                '<td class="col-manual">'          + (ajus ? fmtDec(ajus) : '–')         + '</td>' +
                '<td class="col-manual obs-cell">' + esc(obs)                            + '</td>' +
                // Editar
                '<td><button class="btn btn-xs btn-outline-warning btn-edit-fila" ' +
                'data-id="' + f.id + '" ' +
                'data-agente="' + esc(f.nombre_completo) + '" ' +
                'data-extras="' + extH + '" ' +
                'data-descuentos="' + desc + '" ' +
                'data-ajuste="' + ajus + '" ' +
                'data-obs="' + esc(obs) + '" ' +
                'title="Editar campos manuales"><i class="fas fa-pencil-alt"></i></button></td>' +
                '</tr>';
        });

        // ── Fila de totales ─────────────────────────────────
        if (_filas.length > 0) {
            tfoot =
                '<tr class="fila-totales">' +
                '<td class="col-agente"><strong>TOTAL</strong></td>' +
                '<td class="col-auto"><strong>' + fmtDec(tot.jornadas) + '</strong></td>' +
                '<td class="col-auto">–</td>' +
                '<td class="col-auto"><strong>' + tot.festivos + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.fs       + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.vac      + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.bajas    + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.permisos + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.form     + '</strong></td>' +
                '<td class="col-auto"><strong>' + fmtDec(tot.hred) + '</strong></td>' +
                '<td class="col-auto"><strong>' + tot.p01 + '/' + tot.p040 + '</strong></td>' +
                '<td class="col-manual"><strong>' + (tot.extras  ? fmtDec(tot.extras)  : '–') + '</strong></td>' +
                '<td class="col-manual"><strong>' + (tot.desc    ? fmtDec(tot.desc)    : '–') + '</strong></td>' +
                '<td class="col-manual"><strong>' + (tot.ajuste  ? fmtDec(tot.ajuste)  : '–') + '</strong></td>' +
                '<td class="col-manual obs-cell"></td>' +
                '<td></td>' +
                '</tr>';
        }

        $('#tbodyContabilidad').html(tbody || '<tr><td colspan="16" class="text-center text-muted py-3">Sin datos. Pulse <strong>Recalcular</strong> para generar la contabilidad del mes.</td></tr>');
        $('#tfootContabilidad').html(tfoot);
    }

    // ─────────────────────────────────────────────────────────────
    // BOTÓN EDITAR FILA (campos manuales)
    // ─────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-fila', function () {
        var $btn = $(this);
        _filaMod = {
            id:     $btn.data('id'),
            agente: $btn.data('agente')
        };
        $('#editFilaId').val(_filaMod.id);
        $('#editFilaAgente').text(_filaMod.agente);
        $('#editExtrasHoras').val($btn.data('extras') || 0);
        $('#editDescuentos').val($btn.data('descuentos') || 0);
        $('#editAjuste').val($btn.data('ajuste') || 0);
        $('#editObservaciones').val($btn.data('obs') || '');
        // ── FASE 7: Helper baja + vacaciones ─────────────────
        // Fuente: "Notas sobre nomenclaturas":
        // "En bajas que coinciden con vacaciones se calcula la equivalencia
        //  de jornada municipal (7h) y se convierte a jornada de Policía (8,20h)"
        var fila = null;
        _filas.forEach(function (f) { if (parseInt(f.id, 10) === parseInt(_filaMod.id, 10)) fila = f; });

        var $helper = $('#divHelperBaja');
        if (fila && parseInt(fila.bajas_dias, 10) > 0) {
            var diasBaja = parseInt(fila.bajas_dias, 10);
            // Jornada Ayuntamiento = 7h/día → convertir a jornada Policía (8,20h = 8h 12min)
            // equivalente_jornadas = dias_baja × 7 / 8.20
            var equiv = Math.round((diasBaja * 7 / 8.20) * 100) / 100;
            // Aplicar regla decimal P01/P040 también aquí (>0.50 → sube, ≤0.50 → baja)
            var dec   = equiv - Math.floor(equiv);
            var equiv_redondeado = dec > 0.50 ? Math.ceil(equiv) : Math.floor(equiv);

            $('#helperBajaDias').text(diasBaja);
            $('#helperEquivJorn').text(equiv + ' → ' + equiv_redondeado + ' jornadas');
            $helper.removeClass('d-none');
        } else {
            $helper.addClass('d-none');
        }
        $('#modalEditarFila').modal('show');
    });

    // ─────────────────────────────────────────────────────────────    // HELPER BAJA+VACACIONES: botón "Aplicar al ajuste manual"
    // ─────────────────────────────────────────────────────────
    $(document).on('click', '#btnAplicarHelper', function () {
        var fila = null;
        var id   = parseInt($('#editFilaId').val(), 10);
        _filas.forEach(function (f) { if (parseInt(f.id, 10) === id) fila = f; });
        if (!fila) return;

        var diasBaja = parseInt(fila.bajas_dias, 10) || 0;
        var equiv    = diasBaja * 7 / 8.20;
        var dec      = equiv - Math.floor(equiv);
        var val      = dec > 0.50 ? Math.ceil(equiv) : Math.floor(equiv);

        $('#editAjuste').val(val);
        mostrarToast('info', 'Ajuste manual sugerido: ' + val + ' jornada(s). Revísalo antes de guardar.');
    });

    // ─────────────────────────────────────────────────────────    // GUARDAR FILA (campos manuales)
    // ─────────────────────────────────────────────────────────────
    $('#btnGuardarFila').on('click', function () {
        var id = parseInt($('#editFilaId').val(), 10);
        if (!id) return;

        $.post(AJAX + '?action=guardar_fila_contabilidad', {
            id:            id,
            extras_horas:  $('#editExtrasHoras').val(),
            descuentos:    $('#editDescuentos').val(),
            ajuste_manual: $('#editAjuste').val(),
            observaciones: $('#editObservaciones').val()
        }, function (r) {
            if (r.validacion === 'ok') {
                $('#modalEditarFila').modal('hide');
                mostrarToast('success', 'Guardado correctamente');
                // Actualizar la fila en memoria y re-renderizar
                _filas.forEach(function (f) {
                    if (parseInt(f.id, 10) === id) {
                        f.extras_horas  = parseFloat($('#editExtrasHoras').val()) || 0;
                        f.descuentos    = parseFloat($('#editDescuentos').val())  || 0;
                        f.ajuste_manual = parseFloat($('#editAjuste').val())      || 0;
                        f.observaciones = $('#editObservaciones').val();
                    }
                });
                renderTabla();
            } else {
                mostrarToast('error', r.error || r.mensaje || 'Error al guardar');
            }
        }, 'json');
    });

    // ─────────────────────────────────────────────────────────────    // BOTÓN EXPORTAR EXCEL
    // ─────────────────────────────────────────────────────────
    $('#btnExportarExcel').on('click', function () {
        if (!_cuadrante) { mostrarToast('warning', 'Carga primero el mes'); return; }
        var url = 'inc/turnos/export_contabilidad.php'
                + '?ejercicio=' + _cuadrante.ejercicio
                + '&mes='       + _cuadrante.mes;
        window.open(url, '_blank');
    });

    // ─────────────────────────────────────────────────────────    // BOTÓN RECALCULAR
    // ─────────────────────────────────────────────────────────────
    $('#btnRecalcular').on('click', function () {
        if (!_cuadrante) { mostrarToast('warning', 'Carga primero el mes'); return; }

        Swal.fire({
            title: '¿Recalcular contabilidad?',
            text: 'Se recalcularán todos los campos automáticos. Los campos manuales ya guardados se conservan.',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, recalcular',
            cancelButtonText: 'Cancelar'
        }).then(function (res) {
            if (!res.value) return;

            $('#btnRecalcular').prop('disabled', true);
            $.post(AJAX + '?action=calcular_contabilidad', {
                ejercicio: _cuadrante.ejercicio,
                mes:       _cuadrante.mes
            }, function (r) {
                $('#btnRecalcular').prop('disabled', false);
                if (r.validacion === 'ok') {
                    mostrarToast('success', r.mensaje);
                    cargarContabilidad(); // Recargar para mostrar nuevos datos
                } else {
                    mostrarToast('error', r.error || r.mensaje || 'Error al recalcular');
                }
            }, 'json').fail(function () {
                $('#btnRecalcular').prop('disabled', false);
                mostrarToast('error', 'Error de conexión');
            });
        });
    });

    // ─────────────────────────────────────────────────────────────
    // BOTÓN CERRAR MES
    // ─────────────────────────────────────────────────────────────
    $('#btnCerrarMes').on('click', function () {
        if (!_cuadrante) return;
        if (_cuadrante.estado === 'cerrado' || _cuadrante.estado === 'contabilizado') {
            mostrarToast('info', 'El mes ya está ' + _cuadrante.estado);
            return;
        }

        Swal.fire({
            title: '¿Cerrar el mes?',
            text: 'Se bloqueará la edición del cuadrante. La contabilidad seguirá editable.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cerrar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e6a817'
        }).then(function (res) {
            if (!res.value) return;
            $.post(AJAX + '?action=cerrar_mes', {
                ejercicio: _cuadrante.ejercicio,
                mes:       _cuadrante.mes
            }, function (r) {
                if (r.validacion === 'ok') {
                    _cuadrante.estado = 'cerrado';
                    actualizarEstadoBadge();
                    actualizarBotones();
                    mostrarToast('success', r.mensaje);
                } else {
                    mostrarToast('error', r.error || r.mensaje || 'Error');
                }
            }, 'json');
        });
    });

    // ─────────────────────────────────────────────────────────────
    // BOTÓN REABRIR
    // ─────────────────────────────────────────────────────────────
    $('#btnReabrir').on('click', function () {
        if (!_cuadrante) return;

        Swal.fire({
            title: '¿Reabrir el mes?',
            text: 'El cuadrante volverá a estado borrador y podrá editarse.',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, reabrir',
            cancelButtonText: 'Cancelar'
        }).then(function (res) {
            if (!res.value) return;
            $.post(AJAX + '?action=reabrir_mes', {
                ejercicio: _cuadrante.ejercicio,
                mes:       _cuadrante.mes
            }, function (r) {
                if (r.validacion === 'ok') {
                    _cuadrante.estado = 'borrador';
                    actualizarEstadoBadge();
                    actualizarBotones();
                    mostrarToast('success', r.mensaje);
                } else {
                    mostrarToast('error', r.error || r.mensaje || 'Error');
                }
            }, 'json');
        });
    });

    // ─────────────────────────────────────────────────────────────
    // BOTÓN CONTABILIZAR
    // ─────────────────────────────────────────────────────────────
    $('#btnContabilizar').on('click', function () {
        if (!_cuadrante) return;

        Swal.fire({
            title: '¿Contabilizar el mes?',
            text: 'Se marcará como CONTABILIZADO. Esta acción consolida el mes. Aún podrá reabrir si es necesario.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, contabilizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then(function (res) {
            if (!res.value) return;
            $.post(AJAX + '?action=contabilizar_mes', {
                ejercicio: _cuadrante.ejercicio,
                mes:       _cuadrante.mes
            }, function (r) {
                if (r.validacion === 'ok') {
                    _cuadrante.estado = 'contabilizado';
                    actualizarEstadoBadge();
                    actualizarBotones();
                    mostrarToast('success', r.mensaje);
                } else {
                    mostrarToast('error', r.error || r.mensaje || 'Error');
                }
            }, 'json');
        });
    });

    // ─────────────────────────────────────────────────────────────
    // ESTADO Y BOTONES
    // ─────────────────────────────────────────────────────────────
    function actualizarEstadoBadge() {
        if (!_cuadrante) return;
        var cls = {
            'borrador':       'badge-secondary',
            'cerrado':        'badge-warning',
            'contabilizado':  'badge-success'
        };
        var lbl = {
            'borrador':      'Borrador',
            'cerrado':       'Cerrado',
            'contabilizado': 'Contabilizado'
        };
        var est = _cuadrante.estado || 'borrador';
        $('#estadoBadge')
            .removeClass('d-none badge-secondary badge-warning badge-success')
            .addClass(cls[est] || 'badge-secondary')
            .text(lbl[est] || est);
    }

    function actualizarBotones() {
        if (!_cuadrante) return;
        var est = _cuadrante.estado || 'borrador';

        // Recalcular: siempre disponible (aunque esté cerrado, para ver los datos)
        $('#btnRecalcular').prop('disabled', est === 'contabilizado');

        // Cerrar mes: solo si está en borrador
        $('#btnCerrarMes').toggleClass('d-none', est !== 'borrador');

        // Contabilizar: solo si está cerrado
        $('#btnContabilizar').toggleClass('d-none', est !== 'cerrado');

        // Reabrir: si está cerrado o contabilizado
        $('#btnReabrir').toggleClass('d-none', est === 'borrador');

        // Botones de edición manual: desactivar si está contabilizado
        // (se controla al abrir modal)
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    /** Formatea decimal: muestra '–' si es 0, o el número con 2 decimales si tiene parte decimal */
    function fmt(v) {
        var n = parseFloat(v) || 0;
        if (n === 0) return '–';
        return n % 1 === 0 ? String(n) : n.toFixed(2);
    }

    /** Igual que fmt pero siempre muestra el número */
    function fmtDec(v) {
        var n = parseFloat(v) || 0;
        if (n === 0) return '0';
        return n % 1 === 0 ? String(n) : n.toFixed(2);
    }

    function formatFechaHora(s) {
        if (!s) return '';
        // s viene como "YYYY-MM-DD HH:MM:SS"
        var p = s.split(' ');
        if (p.length < 2) return s;
        var d = p[0].split('-');
        return d[2] + '/' + d[1] + '/' + d[0] + ' ' + p[1].substr(0, 5);
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
        Swal.fire({ type: tipo, title: msg, timer: 2500, showConfirmButton: false });
    }
});
