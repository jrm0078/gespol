<?php include("inc/seguridad.php"); ?>
<style>
.badge-activo   { background:#28a745; color:#fff; padding:2px 8px; border-radius:10px; font-size:.75rem; }
.badge-inactivo { background:#dc3545; color:#fff; padding:2px 8px; border-radius:10px; font-size:.75rem; }
.panel-form { display:none; }
.agente-chip { display:inline-flex; align-items:center; background:#e9ecef; border-radius:20px;
               padding:3px 10px; margin:3px; font-size:.82rem; }
.agente-chip .btn-rm { background:none; border:none; color:#dc3545; padding:0 0 0 6px; cursor:pointer; font-size:.9rem; line-height:1; }
</style>

<div class="container-fluid px-1">

  <!-- ── CARD PRINCIPAL ─────────────────────────────── -->
  <div class="card shadow-sm mb-3">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between">
      <h5 class="m-0 text-white"><i class="fas fa-users-cog mr-2"></i>Equipos</h5>
      <button id="btnAddEquipo" class="btn btn-sm btn-light">
        <i class="fas fa-plus mr-1"></i>Nuevo equipo
      </button>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table id="tblEquipos" class="display" style="width:100%">
          <thead><tr>
            <th style="width:50px"></th>
            <th>Código</th>
            <th>Nombre</th>
            <th style="width:90px;text-align:center">Agentes</th>
            <th style="width:70px;text-align:center">Orden</th>
            <th style="width:80px;text-align:center">Activo</th>
            <th style="width:90px"></th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ── PANEL FORMULARIO EQUIPO ────────────────────── -->
  <div id="panelFormEquipo" class="card shadow-sm mb-3 panel-form">
    <div class="card-header py-2">
      <strong id="tituloFormEquipo"><i class="fas fa-edit mr-1"></i>Equipo</strong>
    </div>
    <div class="card-body">
      <form id="formEquipo" autocomplete="off">
        <input type="hidden" id="eqId">
        <div class="row">
          <div class="col-md-2 form-group">
            <label>Código <span class="text-danger">*</span></label>
            <input type="text" id="eqCodigo" class="form-control form-control-sm" maxlength="20" placeholder="EQ1">
          </div>
          <div class="col-md-5 form-group">
            <label>Nombre <span class="text-danger">*</span></label>
            <input type="text" id="eqNombre" class="form-control form-control-sm" maxlength="100">
          </div>
          <div class="col-md-2 form-group">
            <label>Orden</label>
            <input type="number" id="eqOrden" class="form-control form-control-sm" value="0" min="0">
          </div>
          <div class="col-md-3 form-group">
            <label>Estado</label>
            <select id="eqActivo" class="form-control form-control-sm">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>

        <!-- Agentes del equipo -->
        <hr class="my-2">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <strong>Agentes en este equipo</strong>
          <button type="button" id="btnAddAgenteEquipo" class="btn btn-sm btn-outline-primary" style="display:none">
            <i class="fas fa-user-plus mr-1"></i>Añadir agente
          </button>
        </div>
        <div id="zonaAgentesEquipo" class="mb-2">
          <em class="text-muted small">Guarda el equipo primero para asignar agentes.</em>
        </div>
        <div id="rowAddAgente" class="input-group input-group-sm" style="display:none;max-width:500px">
          <select id="cmbAgenteLibre" class="form-control form-control-sm"></select>
          <div class="input-group-append">
            <button type="button" id="btnConfirmAddAgente" class="btn btn-primary btn-sm">Asignar</button>
            <button type="button" id="btnCancelAddAgente"  class="btn btn-secondary btn-sm">Cancelar</button>
          </div>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Guardar</button>
          <button type="button" id="btnCancelEquipo" class="btn btn-secondary btn-sm ml-2">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

</div>

<script src="js/cons_turnos_equipos.js"></script>
