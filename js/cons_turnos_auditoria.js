/* jshint esversion:6 */
$(function () {
    'use strict';

    var AJAX    = 'inc/turnos/ajax_turnos.php';
    var _pagina = 1;
    var _total_paginas = 1;

    // ── Colores por entidad ──────────────────────────────────
    var ENTIDAD_CLASS = {
        'cuadrante_dia':    'badge-primary',
        'cuadrante':        'badge-warning',
        'contabilidad_mes': 'badge-info',
    };

    // ── Fechas por defecto: último mes ───────────────────────
    (function () {
        var hoy    = new Date();
        var inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        $('#filtDesde').val(fmtDate(inicio));
        $('#filtHasta').val(fmtDate(hoy));
    })();

    // ── Botón filtrar ────────────────────────────────────────
    $('#btnFiltrar').on('click', function () {
        _pagina = 1;
        cargarAuditoria();
    });

    // ── Paginación ───────────────────────────────────────────
    $('#btnPagAnterior').on('click', function () {
        if (_pagina > 1) { _pagina--; cargarAuditoria(); }
    });
    $('#btnPagSiguiente').on('click', function () {
        if (_pagina < _total_paginas) { _pagina++; cargarAuditoria(); }
    });

    // ─────────────────────────────────────────────────────────
    // CARGA DE DATOS
    // ─────────────────────────────────────────────────────────
    function cargarAuditoria() {
        $('#divCargando').removeClass('d-none');
        $('#wrapTabla').addClass('d-none');
        $('#divPaginacion').addClass('d-none');

        $.post(AJAX + '?action=cargar_auditoria', {
            pagina:   _pagina,
            entidad:  $('#filtEntidad').val(),
            usuario:  $('#filtUsuario').val().trim(),
            desde:    $('#filtDesde').val(),
            hasta:    $('#filtHasta').val(),
            busqueda: $('#filtBusqueda').val().trim()
        }, function (r) {
            $('#divCargando').addClass('d-none');
            $('#wrapTabla').removeClass('d-none');

            if (r.validacion !== 'ok') {
                mostrarToast('error', r.error || 'Error al cargar auditoría');
                return;
            }

            _total_paginas = r.total_paginas || 1;

            // Actualizar entidades en el selector (dinámico)
            if (r.entidades && r.entidades.length) {
                var $sel = $('#filtEntidad');
                var selVal = $sel.val();
                $sel.find('option:not(:first)').remove();
                r.entidades.forEach(function (e) {
                    $sel.append('<option value="' + esc(e) + '">' + esc(e) + '</option>');
                });
                $sel.val(selVal);
            }

            // Info de paginación
            var desde = ((_pagina - 1) * r.por_pagina) + 1;
            var hasta = Math.min(_pagina * r.por_pagina, r.total);
            if (r.total === 0) {
                $('#lblTotal').text('No hay registros con esos filtros');
            } else {
                $('#lblTotal').text('Mostrando ' + desde + '–' + hasta + ' de ' + r.total + ' registros');
            }

            if (_total_paginas > 1) {
                $('#divPaginacion').removeClass('d-none');
                $('#lblPagina').text('Página ' + _pagina + ' de ' + _total_paginas);
                $('#btnPagAnterior').prop('disabled', _pagina <= 1);
                $('#btnPagSiguiente').prop('disabled', _pagina >= _total_paginas);
            }

            renderTabla(r.filas);

        }, 'json').fail(function () {
            $('#divCargando').addClass('d-none');
            $('#wrapTabla').removeClass('d-none');
            mostrarToast('error', 'Error de conexión');
        });
    }

    // ─────────────────────────────────────────────────────────
    // RENDER TABLA
    // ─────────────────────────────────────────────────────────
    function renderTabla(filas) {
        if (!filas || filas.length === 0) {
            $('#tbodyAuditoria').html(
                '<tr><td colspan="8" class="text-center text-muted py-4">' +
                '<i class="fas fa-search mr-1"></i>Sin registros con esos filtros.</td></tr>'
            );
            return;
        }

        var html = '';
        filas.forEach(function (f) {
            var entCls = ENTIDAD_CLASS[f.entidad] || 'badge-secondary';

            html +=
                '<tr>' +
                '<td class="text-nowrap"><small>' + esc(formatFechaHora(f.fecha_hora)) + '</small></td>' +
                '<td><small class="font-weight-bold">' + esc(f.usuario || '–') + '</small></td>' +
                '<td><span class="badge badge-entidad ' + entCls + '">' + esc(f.entidad) + '</span></td>' +
                '<td class="text-center">' + f.id_entidad + '</td>' +
                '<td><code class="small">' + esc(f.campo) + '</code></td>' +
                '<td>' + renderValor(f.valor_anterior) + '</td>' +
                '<td>' + renderValor(f.valor_nuevo, true) + '</td>' +
                '<td><small class="text-muted">' + esc(f.observaciones) + '</small></td>' +
                '</tr>';
        });

        $('#tbodyAuditoria').html(html);
    }

    /** Renderiza un valor: truncado con tooltip si es largo */
    function renderValor(v, destacar) {
        if (v === null || v === undefined || v === '') return '<span class="text-muted">–</span>';
        var cls = destacar ? 'font-weight-bold text-primary' : 'text-muted';
        if (v.length > 50) {
            return '<span class="val-celda ' + cls + '" title="' + esc(v) + '">' + esc(v) + '</span>';
        }
        return '<span class="' + cls + '">' + esc(v) + '</span>';
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────
    function formatFechaHora(s) {
        if (!s) return '–';
        var p = s.split(' ');
        if (p.length < 2) return s;
        var d = p[0].split('-');
        return d[2] + '/' + d[1] + '/' + d[0] + ' ' + p[1].substr(0, 5);
    }

    function fmtDate(d) {
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + mm + '-' + dd;
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
