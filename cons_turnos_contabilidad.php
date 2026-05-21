<?php include("inc/seguridad.php"); ?>
<style>
/* ── Tabla contabilidad ─────────────────────────────────────── */
#wrapContabilidad { overflow-x: auto; }

#tblContabilidad {
    border-collapse: collapse;
    min-width: 900px;
    font-size: .72rem;
    white-space: nowrap;
    table-layout: auto;
}
#tblContabilidad th, #tblContabilidad td {
    border: 1px solid #dee2e6;
    padding: 3px 5px;
    vertical-align: middle;
    text-align: center;
}

/* Cabecera sticky */
#tblContabilidad thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #0066B3;
    color: #fff;
    font-weight: 600;
}
/* Sub-cabecera grupos */
#tblContabilidad thead tr.fila-grupo-auto th { background: #004a8f; font-size:.65rem; }
#tblContabilidad thead tr.fila-grupo-man  th { background: #7a4400; font-size:.65rem; }

/* Columna agente: fija a la izquierda */
#tblContabilidad th.col-agente,
#tblContabilidad td.col-agente {
    position: sticky;
    left: 0;
    z-index: 5;
    min-width: 150px;
    max-width: 150px;
    width: 150px;
    text-align: left;
    padding-left: 8px;
    background: #fff;
    border-right: 2px solid #0066B3;
    overflow: hidden;
    text-overflow: ellipsis;
}
#tblContabilidad thead th.col-agente { background: #0066B3; z-index: 15; }

/* Filas de equipo (cabecera de grupo) */
tr.fila-equipo-contab td {
    background: #e3f0ff !important;
    font-weight: bold;
    color: #0066B3;
    font-size: .75rem;
    border-left: 3px solid #0066B3;
}

/* Columnas calculadas automáticamente */
td.col-auto {
    background: #f0f7ff;
    color: #004080;
    font-weight: 500;
}
/* Columnas manuales */
td.col-manual {
    background: #fff8f0;
    color: #7a4400;
    font-weight: 500;
    min-width: 60px;
}
td.col-manual.obs-cell {
    min-width: 120px;
    max-width: 180px;
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Diferencia de jornadas */
td.dif-positivo { color: #28a745; font-weight: bold; }
td.dif-negativo { color: #dc3545; font-weight: bold; }

/* Fila de totales */
tr.fila-totales td {
    background: #e8f5e9 !important;
    font-weight: bold;
    border-top: 2px solid #28a745;
}
tr.fila-totales td.col-agente { background: #e8f5e9 !important; }

/* Leyenda columnas */
.leyenda-col {
    display: inline-flex; align-items: center; gap: 5px;
    margin: 3px 10px 3px 0; font-size: .75rem;
}
.leyenda-col-box {
    width: 14px; height: 14px; border-radius: 2px; flex-shrink: 0;
    border: 1px solid rgba(0,0,0,.15);
}
</style>

<div class="container-fluid px-1 pag-contabilidad">

  <!-- ── SELECTOR EJERCICIO / MES ─────────────────────────────── -->
  <div class="card shadow-sm mb-2">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between flex-wrap">
      <h5 class="m-0 text-white"><i class="fas fa-calculator mr-2"></i>Contabilidad mensual</h5>
      <div class="d-flex align-items-center flex-wrap" style="gap:8px">
        <label class="text-white mb-0 small">Ejercicio:</label>
        <select id="selEjercicio" class="form-control form-control-sm" style="width:85px">
          <option value="2026">2026</option>
          <option value="2025">2025</option>
          <option value="2027">2027</option>
        </select>
        <label class="text-white mb-0 small">Mes:</label>
        <select id="selMes" class="form-control form-control-sm" style="width:130px">
          <option value="1">Enero</option>
          <option value="2">Febrero</option>
          <option value="3">Marzo</option>
          <option value="4">Abril</option>
          <option value="5">Mayo</option>
          <option value="6">Junio</option>
          <option value="7">Julio</option>
          <option value="8">Agosto</option>
          <option value="9">Septiembre</option>
          <option value="10">Octubre</option>
          <option value="11">Noviembre</option>
          <option value="12">Diciembre</option>
        </select>
        <button id="btnCargar" class="btn btn-sm" style="background:#0084D9;color:#fff;border-color:#0066B3;">
          <i class="fas fa-sync-alt mr-1"></i>Cargar
        </button>
        <span id="estadoBadge" class="badge badge-secondary d-none" style="font-size:.8rem"></span>
      </div>
    </div>
  </div>

  <!-- ── ÁREA PRINCIPAL ──────────────────────────────────────── -->
  <div id="areaContabilidad" class="d-none">

    <!-- Barra de acciones -->
    <div class="card shadow-sm mb-2">
      <div class="card-body py-2 d-flex align-items-center flex-wrap" style="gap:8px">
        <button id="btnRecalcular" class="btn btn-sm btn-primary">
          <i class="fas fa-calculator mr-1"></i>Recalcular
        </button>
        <button id="btnCerrarMes" class="btn btn-sm btn-warning">
          <i class="fas fa-lock mr-1"></i>Cerrar mes
        </button>
        <button id="btnReabrir" class="btn btn-sm btn-outline-secondary d-none">
          <i class="fas fa-lock-open mr-1"></i>Reabrir
        </button>
        <button id="btnContabilizar" class="btn btn-sm btn-success d-none">
          <i class="fas fa-check-circle mr-1"></i>Contabilizar
        </button>
        <button id="btnExportarExcel" class="btn btn-sm btn-outline-success ml-2" title="Exportar a Excel">
          <i class="fas fa-file-excel mr-1"></i>Excel
        </button>
        <div class="ml-auto d-flex align-items-center" style="gap:12px;font-size:.78rem">
          <span class="text-muted">
            Días teóricos del mes: <strong id="lblDiasTeoricos">–</strong>
          </span>
          <span class="text-muted" id="lblUltimoCalculo" style="display:none">
            Último cálculo: <strong id="lblFechaCalculo">–</strong>
          </span>
        </div>
      </div>
    </div>

    <!-- Tabla principal -->
    <div class="card shadow-sm mb-2">
      <div class="card-body p-1">
        <div id="wrapContabilidad">
          <table id="tblContabilidad">
            <thead>
              <tr>
                <th class="col-agente" rowspan="2">Agente</th>
                <!-- Calculados -->
                <th colspan="10" style="background:#004a8f;font-size:.72rem">
                  <i class="fas fa-robot mr-1"></i>Campos calculados automáticamente
                </th>
                <!-- Manuales -->
                <th colspan="4" style="background:#7a4400;font-size:.72rem">
                  <i class="fas fa-pencil-alt mr-1"></i>Campos manuales
                </th>
                <th rowspan="2" style="min-width:36px"></th>
              </tr>
              <tr>
                <!-- Calculados -->
                <th title="Jornadas trabajadas (códigos que computan normal)">Jorn.</th>
                <th title="Diferencia respecto a días teóricos del mes">Dif.</th>
                <th title="Festivos trabajados">Fest.</th>
                <th title="Fines de semana trabajados (al menos 1 día)">F/S</th>
                <th title="Días de vacaciones (código V)">Vac.</th>
                <th title="Días de baja (código B)">Baja</th>
                <th title="Días de permiso (P, Pa, Ps, Pc, Pf, Pas)">Perm.</th>
                <th title="Días de formación / escuela / jefatura (F, Fj, E, Sto, Jf)">Form.</th>
                <th title="Horas de reducción de jornada acumuladas">H.Red.</th>
                <th title="Días P01 / P040">P01/P040</th>
                <!-- Manuales -->
                <th title="Horas extraordinarias (manual)">Ext.h</th>
                <th title="Descuentos (manual)">Desc.</th>
                <th title="Ajuste manual">Ajuste</th>
                <th title="Observaciones">Obs.</th>
              </tr>
            </thead>
            <tbody id="tbodyContabilidad">
              <!-- generado por JS -->
            </tbody>
            <tfoot id="tfootContabilidad"></tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Leyenda -->
    <div class="card shadow-sm mb-2">
      <div class="card-body py-1 px-2">
        <span class="leyenda-col">
          <span class="leyenda-col-box" style="background:#f0f7ff"></span>
          Calculado automáticamente desde el cuadrante
        </span>
        <span class="leyenda-col">
          <span class="leyenda-col-box" style="background:#fff8f0"></span>
          Campo manual (editable)
        </span>
        <span class="leyenda-col text-success font-weight-bold">+N</span>
        <span class="leyenda-col" style="font-size:.72rem">Trabaja más días que los teóricos</span>
        <span class="leyenda-col text-danger font-weight-bold">–N</span>
        <span class="leyenda-col" style="font-size:.72rem">Trabaja menos días</span>
      </div>
    </div>

  </div><!-- /areaContabilidad -->

  <!-- Spinner de carga -->
  <div id="divCargando" class="text-center py-5 d-none">
    <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
    <p class="mt-2 text-muted">Cargando…</p>
  </div>

</div><!-- /container -->

<!-- ── MODAL EDITAR CAMPOS MANUALES ─────────────────────────── -->
<div class="modal fade" id="modalEditarFila" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title">
          <i class="fas fa-pencil-alt mr-1"></i>Campos manuales
        </h6>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body py-3">
        <input type="hidden" id="editFilaId">
        <p id="editFilaAgente" class="font-weight-bold text-primary mb-3"></p>

        <!-- ── FASE 7: Helper baja + vacaciones ─────────────── -->
        <!-- Visible solo cuando el agente tiene días de baja    -->
        <div id="divHelperBaja" class="alert alert-warning py-2 px-3 mb-3 d-none"
             style="border-left:4px solid #e6a817;font-size:.78rem">
          <strong><i class="fas fa-info-circle mr-1"></i>Baja durante vacaciones detectada</strong><br>
          Este agente tiene <strong id="helperBajaDias">0</strong> día(s) de baja registrados.<br>
          Según el criterio del documento de nomenclaturas: si la baja coincide con un período de
          vacaciones, se calculan las jornadas que trabajaría el personal del Ayuntamiento (7h/día)
          y se convierten a jornada de Policía (8,20h).<br>
          <span class="badge badge-secondary mt-1">
            Equivalencia estimada: <strong id="helperEquivJorn">–</strong>
          </span>
          <button type="button" class="btn btn-xs btn-warning ml-2" id="btnAplicarHelper"
                  title="Introduce el valor en el campo Ajuste manual">
            <i class="fas fa-arrow-down mr-1"></i>Aplicar al ajuste manual
          </button>
        </div>

        <div class="row">
          <div class="col-md-4 form-group">
            <label class="small font-weight-bold">
              <i class="fas fa-clock mr-1 text-warning"></i>Horas extra
            </label>
            <input type="number" id="editExtrasHoras" class="form-control form-control-sm"
                   min="0" max="999" step="0.5" placeholder="0">
            <small class="text-muted">Horas extraordinarias del mes</small>
          </div>
          <div class="col-md-4 form-group">
            <label class="small font-weight-bold">
              <i class="fas fa-minus-circle mr-1 text-danger"></i>Descuentos
            </label>
            <input type="number" id="editDescuentos" class="form-control form-control-sm"
                   min="0" max="999" step="0.5" placeholder="0">
            <small class="text-muted">Descuentos a aplicar</small>
          </div>
          <div class="col-md-4 form-group">
            <label class="small font-weight-bold">
              <i class="fas fa-balance-scale mr-1 text-info"></i>Ajuste manual
            </label>
            <input type="number" id="editAjuste" class="form-control form-control-sm"
                   min="-999" max="999" step="0.5" placeholder="0">
            <small class="text-muted">Corrección puntual</small>
          </div>
        </div>

        <div class="form-group mb-0">
          <label class="small font-weight-bold">
            <i class="fas fa-comment-alt mr-1 text-secondary"></i>Observaciones
          </label>
          <textarea id="editObservaciones" class="form-control form-control-sm"
                    rows="3" maxlength="1000"
                    placeholder="Notas sobre el mes de este agente…"></textarea>
        </div>
      </div>
      <div class="modal-footer py-1">
        <button class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-sm btn-primary" id="btnGuardarFila">
          <i class="fas fa-save mr-1"></i>Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<script src="js/cons_turnos_contabilidad.js"></script>
