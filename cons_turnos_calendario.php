<?php include("inc/seguridad.php"); ?>
<style>
.cal-mes { margin-bottom: 16px; }
.cal-mes table { width:100%; border-collapse:collapse; }
.cal-mes th { background:#0084D9; color:#fff; text-align:center; font-size:.7rem; padding:3px 1px; }
.cal-mes td { text-align:center; font-size:.72rem; padding:2px 1px; border:1px solid #dee2e6; cursor:default; }
.cal-mes td.festivo-nacional { background:#dc3545; color:#fff; font-weight:bold; }
.cal-mes td.festivo-local    { background:#fd7e14; color:#fff; font-weight:bold; }
.cal-mes td.festivo-convenio { background:#6f42c1; color:#fff; font-weight:bold; }
.cal-mes td.otro-mes         { background:#f8f9fa; color:#ccc; }
.cal-mes .mes-titulo         { font-weight:bold; font-size:.8rem; text-align:center; margin-bottom:3px; background:#0066B3; color:#fff; padding:3px; border-radius:3px 3px 0 0; }
.panel-form { display:none; }
.legend-item { display:inline-flex; align-items:center; gap:6px; margin-right:12px; font-size:.78rem; }
.legend-box  { width:16px; height:16px; border-radius:3px; flex-shrink:0; }
</style>

<div class="container-fluid px-1">

  <!-- Selector ejercicio + leyenda -->
  <div class="card shadow-sm mb-3">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between">
      <h5 class="m-0 text-white"><i class="fas fa-calendar-alt mr-2"></i>Calendario Laboral</h5>
      <div class="d-flex align-items-center">
        <label class="text-white mb-0 mr-2 small">Ejercicio:</label>
        <select id="selEjercicio" class="form-control form-control-sm" style="width:90px">
          <option value="2026">2026</option>
          <option value="2025">2025</option>
          <option value="2027">2027</option>
        </select>
      </div>
    </div>
    <div class="card-body py-2">
      <div class="mb-2">
        <span class="legend-item"><span class="legend-box" style="background:#dc3545"></span>Festivo nacional</span>
        <span class="legend-item"><span class="legend-box" style="background:#fd7e14"></span>Festivo local</span>
        <span class="legend-item"><span class="legend-box" style="background:#6f42c1"></span>Día de convenio</span>
      </div>
      <div id="calendarioAnual" class="row"></div>
    </div>
  </div>

  <!-- ── FESTIVOS / CONVENIO ──────────────────────────── -->
  <div class="card shadow-sm mb-3">
    <div class="card-header py-2 d-flex align-items-center justify-content-between">
      <strong><i class="fas fa-star mr-1"></i>Festivos y días de convenio</strong>
      <button id="btnAddFestivo" class="btn btn-sm btn-primary">
        <i class="fas fa-plus mr-1"></i>Añadir
      </button>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table id="tblFestivos" class="display" style="width:100%">
          <thead><tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th style="width:80px"></th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Formulario festivo -->
      <div id="panelFormFestivo" class="card card-body mt-2 panel-form">
        <form id="formFestivo" autocomplete="off">
          <input type="hidden" id="fesId">
          <input type="hidden" id="fesEjercicio">
          <div class="row">
            <div class="col-md-3 form-group">
              <label>Fecha <span class="text-danger">*</span></label>
              <input type="date" id="fesFecha" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 form-group">
              <label>Tipo <span class="text-danger">*</span></label>
              <select id="fesTipo" class="form-control form-control-sm">
                <option value="nacional">Nacional</option>
                <option value="local">Local</option>
                <option value="convenio">Convenio empresa</option>
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label>Descripción</label>
              <input type="text" id="fesDesc" class="form-control form-control-sm" maxlength="200">
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Guardar</button>
          <button type="button" id="btnCancelFestivo" class="btn btn-secondary btn-sm ml-2">Cancelar</button>
        </form>
      </div>
    </div>
  </div>

  <!-- ── REDUCCIONES DE JORNADA ─────────────────────── -->
  <div class="card shadow-sm mb-3">
    <div class="card-header py-2 d-flex align-items-center justify-content-between">
      <strong><i class="fas fa-clock mr-1"></i>Reducciones de jornada</strong>
      <button id="btnAddReduccion" class="btn btn-sm btn-primary">
        <i class="fas fa-plus mr-1"></i>Añadir
      </button>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table id="tblReducciones" class="display" style="width:100%">
          <thead><tr>
            <th>Descripción</th>
            <th style="width:100px">Desde</th>
            <th style="width:100px">Hasta</th>
            <th style="width:80px;text-align:center">Min.</th>
            <th style="width:70px;text-align:center">Sáb.</th>
            <th style="width:70px;text-align:center">Dom.</th>
            <th style="width:80px"></th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Formulario reducción -->
      <div id="panelFormReduccion" class="card card-body mt-2 panel-form">
        <form id="formReduccion" autocomplete="off">
          <input type="hidden" id="redId">
          <input type="hidden" id="redEjercicio">
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Descripción <span class="text-danger">*</span></label>
              <input type="text" id="redDesc" class="form-control form-control-sm" maxlength="200">
            </div>
            <div class="col-md-2 form-group">
              <label>Fecha desde <span class="text-danger">*</span></label>
              <input type="date" id="redDesde" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label>Fecha hasta <span class="text-danger">*</span></label>
              <input type="date" id="redHasta" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 form-group">
              <label>Minutos reducción <span class="text-danger">*</span></label>
              <input type="number" id="redMin" class="form-control form-control-sm" min="1" max="480">
            </div>
            <div class="col-md-1 form-group d-flex align-items-center pt-3">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="redSab">
                <label class="custom-control-label" for="redSab">Sáb.</label>
              </div>
            </div>
            <div class="col-md-1 form-group d-flex align-items-center pt-3">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="redDom">
                <label class="custom-control-label" for="redDom">Dom.</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Guardar</button>
          <button type="button" id="btnCancelReduccion" class="btn btn-secondary btn-sm ml-2">Cancelar</button>
        </form>
      </div>
    </div>
  </div>

</div>

<script src="js/cons_turnos_calendario.js"></script>
