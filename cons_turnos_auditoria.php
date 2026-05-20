<?php include("inc/seguridad.php"); ?>
<style>
#tblAuditoria { font-size:.73rem; }
#tblAuditoria th, #tblAuditoria td { padding:3px 7px; vertical-align:middle; }
.val-celda {
    display:inline-block; max-width:200px;
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    vertical-align:middle;
}
.badge-entidad {
    font-size:.68rem; font-family:monospace; font-weight:600;
}
</style>

<div class="container-fluid px-1">

  <!-- ── CABECERA ─────────────────────────────────────────────── -->
  <div class="card shadow-sm mb-2">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between flex-wrap">
      <h5 class="m-0 text-white"><i class="fas fa-history mr-2"></i>Auditoría de cambios</h5>
      <small class="text-white opacity-75">Registro automático de todas las modificaciones del módulo de turnos</small>
    </div>
  </div>

  <!-- ── FILTROS ──────────────────────────────────────────────── -->
  <div class="card shadow-sm mb-2">
    <div class="card-body py-2">
      <div class="form-row align-items-end">
        <div class="col-md-2 form-group mb-1">
          <label class="small font-weight-bold">Desde</label>
          <input type="date" id="filtDesde" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 form-group mb-1">
          <label class="small font-weight-bold">Hasta</label>
          <input type="date" id="filtHasta" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 form-group mb-1">
          <label class="small font-weight-bold">Entidad</label>
          <select id="filtEntidad" class="form-control form-control-sm">
            <option value="">– Todas –</option>
            <option value="cuadrante_dia">cuadrante_dia</option>
            <option value="cuadrante">cuadrante</option>
            <option value="contabilidad_mes">contabilidad_mes</option>
          </select>
        </div>
        <div class="col-md-2 form-group mb-1">
          <label class="small font-weight-bold">Usuario</label>
          <input type="text" id="filtUsuario" class="form-control form-control-sm" placeholder="Nombre…">
        </div>
        <div class="col-md-2 form-group mb-1">
          <label class="small font-weight-bold">Texto libre</label>
          <input type="text" id="filtBusqueda" class="form-control form-control-sm" placeholder="Buscar en valores…">
        </div>
        <div class="col-md-2 form-group mb-1">
          <label class="small d-block">&nbsp;</label>
          <button id="btnFiltrar" class="btn btn-sm btn-primary w-100">
            <i class="fas fa-search mr-1"></i>Filtrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ── RESULTADOS ───────────────────────────────────────────── -->
  <div class="card shadow-sm mb-2">
    <div class="card-body p-0">

      <!-- Info paginación -->
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <small id="lblTotal" class="text-muted">–</small>
        <div id="divPaginacion" class="d-none">
          <button class="btn btn-xs btn-outline-secondary mr-1" id="btnPagAnterior">
            <i class="fas fa-chevron-left"></i>
          </button>
          <span id="lblPagina" class="small mx-2">–</span>
          <button class="btn btn-xs btn-outline-secondary" id="btnPagSiguiente">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <!-- Spinner -->
      <div id="divCargando" class="text-center py-4 d-none">
        <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
      </div>

      <!-- Tabla -->
      <div class="table-responsive" id="wrapTabla">
        <table class="table table-hover table-sm mb-0" id="tblAuditoria">
          <thead class="thead-light">
            <tr>
              <th style="width:130px">Fecha/Hora</th>
              <th style="width:130px">Usuario</th>
              <th style="width:110px">Entidad</th>
              <th style="width:60px">ID</th>
              <th style="width:130px">Campo</th>
              <th>Valor anterior</th>
              <th>Valor nuevo</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <tbody id="tbodyAuditoria">
            <tr><td colspan="8" class="text-center text-muted py-4">
              Usa los filtros y pulsa <strong>Filtrar</strong> para ver el registro de cambios.
            </td></tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<script src="js/cons_turnos_auditoria.js"></script>
