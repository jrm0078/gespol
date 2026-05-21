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

    // Helper para escopar selectores a esta página
    function $p(sel) { return $('.pag-auditoria').find(sel); }

    // ── Fechas por defecto: último mes ───────────────────────
    (function () {
        var hoy    = new Date();
        var inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        $p('#filtDesde').val(fmtDate(inicio));
        $p('#filtHasta').val(fmtDate(hoy));
        cargarAuditoria();
    })();

    // ── Botón filtrar ────────────────────────────────────────
    $(document).on('click', '.pag-auditoria #btnFiltrar', function () {
        _pagina = 1;
        cargarAuditoria();
    });

    // ── Paginación ───────────────────────────────────────────
    $(document).on('click', '.pag-auditoria #btnPagAnterior', function () {
        if (_pagina > 1) { _pagina--; cargarAuditoria(); }
    });
    $(document).on('click', '.pag-auditoria #btnPagSiguiente', function () {
        if (_pagina < _total_paginas) { _pagina++; cargarAuditoria(); }
    });

    // ─────────────────────────────────────────────────────────
    // CARGA DE DATOS
    // ─────────────────────────────────────────────────────────
    function cargarAuditoria() {
        $p('#divCargando').removeClass('d-none');
        $p('#wrapTabla').addClass('d-none');
        $p('#divPaginacion').addClass('d-none');

        $.post(AJAX + '?action=cargar_auditoria', {
            pagina:   _pagina,
            entidad:  $p('#filtEntidad').val(),
            usuario:  $p('#filtUsuario').val().trim(),
            desde:    $p('#filtDesde').val(),
            hasta:    $p('#filtHasta').val(),
            busqueda: $p('#filtBusqueda').val().trim()
        }, function (r) {
            $p('#divCargando').addClass('d-none');
            $p('#wrapTabla').removeClass('d-none');

            if (r.validacion !== 'ok') {
                mostrarToast('error', r.error || 'Error al cargar auditoría');
                return;
            }

            _total_paginas = r.total_paginas || 1;

            // Actualizar entidades en el selector (dinámico)
            if (r.entidades && r.entidades.length) {
                var $sel = $p('#filtEntidad');
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
                $p('#lblTotal').text('No hay registros con esos filtros');
            } else {
                $p('#lblTotal').text('Mostrando ' + desde + '–' + hasta + ' de ' + r.total + ' registros');
            }

            if (_total_paginas > 1) {
                $p('#divPaginacion').removeClass('d-none');
                $p('#lblPagina').text('Página ' + _pagina + ' de ' + _total_paginas);
                $p('#btnPagAnterior').prop('disabled', _pagina <= 1);
                $p('#btnPagSiguiente').prop('disabled', _pagina >= _total_paginas);
            }

            renderTabla(r.filas);

        }, 'json').fail(function () {
            $p('#divCargando').addClass('d-none');
            $p('#wrapTabla').removeClass('d-none');
            mostrarToast('error', 'Error de conexión');
        });
    }

    // ─────────────────────────────────────────────────────────
    // RENDER TABLA
    // ─────────────────────────────────────────────────────────
    function renderTabla(filas) {
        if (!filas || filas.length === 0) {
            $p('#tbodyAuditoria').html(
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

        $p('#tbodyAuditoria').html(html);
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
