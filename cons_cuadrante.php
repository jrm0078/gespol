<?php include("inc/seguridad.php"); ?>
<style>
/* ── Cuadrante grid ─────────────────────────────────────────────── */
#wrapCuadrante { overflow-x: auto; }

#tblCuadrante {
    border-collapse: collapse;
    min-width: 900px;
    font-size: .72rem;
    white-space: nowrap;
    table-layout: fixed;
}
#tblCuadrante th, #tblCuadrante td {
    border: 1px solid #dee2e6;
    padding: 2px 1px;
    text-align: center;
    vertical-align: middle;
}
#tblCuadrante thead th { background:#0066B3; color:#fff; position:sticky; top:0; z-index:10; }
#tblCuadrante thead tr.fila-dow  th { background:#004a8f; font-size:.65rem; }
#tblCuadrante thead tr.fila-fes  th { background:#00357a; font-size:.6rem; padding:1px; }

/* Columna agente: fija a la izquierda */
#tblCuadrante th.col-agente,
#tblCuadrante td.col-agente {
    position: sticky;
    left: 0;
    z-index: 5;
    min-width: 140px;
    max-width: 140px;
    width: 140px;
    text-align: left;
    padding-left: 6px;
    background: #fff;
    border-right: 2px solid #0066B3;
    overflow: hidden;
    text-overflow: ellipsis;
}
#tblCuadrante thead th.col-agente { background:#0066B3; z-index:15; }

/* Columna total */
#tblCuadrante td.col-total { background:#f1f3f5; font-weight:bold; min-width:36px; }
#tblCuadrante th.col-total { min-width:36px; }

/* Filas de equipo (cabecera) */
tr.fila-equipo td {
    background: #e3f0ff !important;
    font-weight: bold;
    color: #0066B3;
    font-size: .75rem;
    border-left: 3px solid #0066B3;
}

/* Celdas de día */
td.celda-dia {
    min-width: 26px;
    max-width: 26px;
    width: 26px;
    cursor: pointer;
    user-select: none;
}
td.celda-dia:hover { filter: brightness(0.88); }
td.celda-dia.sel   { outline: 2px solid #333; outline-offset: -2px; }

/* Tipos de día */
td.dia-sab  { background-color: #e9ecef; }
td.dia-dom  { background-color: #dee2e6; }
td.dia-fes-nacional  { background-color: rgba(220,53,69,.12); }
td.dia-fes-local     { background-color: rgba(253,126,20,.12); }
td.dia-fes-convenio  { background-color: rgba(111,66,193,.12); }

/* Código badge en celda */
.badge-cod {
    display: inline-block;
    padding: 1px 3px;
    border-radius: 3px;
    font-size: .7rem;
    font-weight: 600;
    color: #fff;
    min-width: 18px;
    line-height: 1.4;
    text-shadow: 0 1px 2px rgba(0,0,0,.3);
}

/* Panel lateral de herramientas */
#panelHerramientas { min-width: 200px; }

/* Leyenda */
.leyenda-item { display:inline-flex; align-items:center; gap:5px; margin:3px 8px 3px 0; font-size:.75rem; }
.leyenda-box  { width:14px; height:14px; border-radius:2px; flex-shrink:0; border:1px solid rgba(0,0,0,.1); }

/* Totales barra */
#barraTotal .total-item { display:inline-block; margin:0 8px; font-size:.8rem; }
#barraTotal .total-val  { font-weight:bold; }
</style>

<div class="container-fluid px-1 pag-cuadrante">

  <!-- ── SELECTOR EJERCICIO / MES ─────────────────────────── -->
  <div class="card shadow-sm mb-2">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between flex-wrap">
      <h5 class="m-0 text-white"><i class="fas fa-table mr-2"></i>Cuadrante mensual</h5>
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
  <div id="areaEditor" class="d-none">
    <div class="d-flex" style="gap:10px;align-items:flex-start">

      <!-- Cuadrante grid -->
      <div class="card shadow-sm flex-grow-1 mb-2" style="overflow:hidden">
        <div class="card-header py-1 d-flex align-items-center justify-content-between flex-wrap" style="gap:6px">
          <div class="d-flex align-items-center flex-wrap" style="gap:6px">
            <!-- Acciones rápidas -->
            <button class="btn btn-sm btn-outline-primary" id="btnPatron">
              <i class="fas fa-magic mr-1"></i>Patrón equipo
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="btnLimpiarSel">
              <i class="fas fa-eraser mr-1"></i>Limpiar sel.
            </button>
            <button class="btn btn-sm btn-outline-success" id="btnGuardarPendientes">
              <i class="fas fa-save mr-1"></i>Guardar <span id="cntPendientes" class="badge badge-danger d-none">0</span>
            </button>
          </div>
          <div id="barraTotal" class="text-muted small d-none">
            <span class="total-item">Jornadas: <span class="total-val" id="totJornadas">0</span></span>
            <span class="total-item">Festivos: <span class="total-val" id="totFestivos">0</span></span>
            <span class="total-item">F/S: <span class="total-val" id="totFS">0</span></span>
          </div>
        </div>
        <div class="card-body p-1">
          <div id="wrapCuadrante">
            <table id="tblCuadrante"><thead></thead><tbody></tbody></table>
          </div>
        </div>
      </div>

      <!-- Panel de herramientas (selector de código) -->
      <div id="panelHerramientas" class="card shadow-sm mb-2" style="width:175px;flex-shrink:0">
        <div class="card-header py-1"><strong style="font-size:.78rem"><i class="fas fa-tags mr-1"></i>Código activo</strong></div>
        <div class="card-body p-1">
          <div id="listaCodigos" style="max-height:400px;overflow-y:auto">
            <!-- generado por JS -->
          </div>
          <hr class="my-1">
          <div class="text-muted" style="font-size:.7rem">
            <strong>Selección actual:</strong><br>
            <span id="lblSelCodigo">Ninguno</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Leyenda -->
    <div class="card shadow-sm mb-2">
      <div class="card-body py-1 px-2">
        <span class="leyenda-item"><span class="leyenda-box" style="background:#e9ecef;border:1px solid #ccc"></span>Sábado</span>
        <span class="leyenda-item"><span class="leyenda-box" style="background:#dee2e6;border:1px solid #bbb"></span>Domingo</span>
        <span class="leyenda-item"><span class="leyenda-box" style="background:rgba(220,53,69,.25)"></span>Festivo nacional</span>
        <span class="leyenda-item"><span class="leyenda-box" style="background:rgba(253,126,20,.25)"></span>Festivo local</span>
        <span class="leyenda-item"><span class="leyenda-box" style="background:rgba(111,66,193,.25)"></span>Convenio</span>
        <span class="leyenda-item text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Excepción al equipo</span>
        <span id="leyendaCodigos" class="d-inline-flex flex-wrap"></span>
      </div>
    </div>
  </div>

  <div id="divCargando" class="text-center py-5 d-none">
    <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
    <p class="mt-2 text-muted">Cargando cuadrante…</p>
  </div>

</div>

<!-- ── MODAL EDITAR CELDA ──────────────────────────────────── -->
<div class="modal fade" id="modalCelda" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title"><i class="fas fa-edit mr-1"></i>Editar celda</h6>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body py-2">
        <p id="lblCeldaInfo" class="small text-muted mb-2"></p>
        <div class="form-group mb-2">
          <label class="small font-weight-bold">Código</label>
          <select id="celdaCodigo" class="form-control form-control-sm">
            <option value="">— vacía —</option>
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold">Horas (opcional)</label>
          <input type="number" id="celdaHoras" class="form-control form-control-sm" min="0" max="24" step="0.5" placeholder="Ej: 8">
        </div>
        <div class="form-group mb-0">
          <label class="small font-weight-bold">Observaciones</label>
          <input type="text" id="celdaObs" class="form-control form-control-sm" maxlength="200">
        </div>
      </div>
      <div class="modal-footer py-1">
        <button class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-sm btn-danger mr-auto" id="btnBorrarCelda"><i class="fas fa-trash-alt"></i></button>
        <button class="btn btn-sm btn-primary" id="btnGuardarCelda"><i class="fas fa-save mr-1"></i>OK</button>
      </div>
    </div>
  </div>
</div>

<!-- ── MODAL PATRÓN EQUIPO ────────────────────────────────── -->
<div class="modal fade" id="modalPatron" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title"><i class="fas fa-magic mr-1"></i>Aplicar patrón a equipo</h6>
        <button class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body py-2">
        <div class="form-group mb-2">
          <label class="small font-weight-bold">Equipo</label>
          <select id="patronEquipo" class="form-control form-control-sm"></select>
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold">Código a aplicar</label>
          <select id="patronCodigo" class="form-control form-control-sm">
            <option value="">— limpiar celda —</option>
          </select>
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold">Días de la semana</label>
          <div id="patronDias" class="d-flex flex-wrap" style="gap:6px">
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdL" value="0" checked><label class="custom-control-label" for="pdL">Lun</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdM" value="1" checked><label class="custom-control-label" for="pdM">Mar</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdX" value="2" checked><label class="custom-control-label" for="pdX">Mié</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdJ" value="3" checked><label class="custom-control-label" for="pdJ">Jue</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdV" value="4" checked><label class="custom-control-label" for="pdV">Vie</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdS" value="5"><label class="custom-control-label" for="pdS">Sáb</label></div>
            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="pdD" value="6"><label class="custom-control-label" for="pdD">Dom</label></div>
          </div>
        </div>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="patronSoloVacias">
          <label class="custom-control-label" for="patronSoloVacias">Aplicar solo en celdas vacías</label>
        </div>
      </div>
      <div class="modal-footer py-1">
        <button class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-sm btn-primary" id="btnAplicarPatron"><i class="fas fa-check mr-1"></i>Aplicar</button>
      </div>
    </div>
  </div>
</div>

<script src="js/cons_cuadrante.js"></script>
