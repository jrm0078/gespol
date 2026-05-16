<?php include("inc/seguridad.php"); ?>
<style>
.chip-color { display:inline-block; width:28px; height:18px; border-radius:4px; border:1px solid rgba(0,0,0,.15); vertical-align:middle; }
.panel-form { display:none; }
</style>

<div class="container-fluid px-1">

  <div class="card shadow-sm mb-3">
    <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between">
      <h5 class="m-0 text-white"><i class="fas fa-tags mr-2"></i>Códigos de turno</h5>
      <button id="btnAddCodigo" class="btn btn-sm btn-light">
        <i class="fas fa-plus mr-1"></i>Nuevo código
      </button>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table id="tblCodigos" class="display" style="width:100%">
          <thead><tr>
            <th style="width:40px"></th>
            <th style="width:60px">Código</th>
            <th>Descripción</th>
            <th style="width:70px;text-align:center">Color</th>
            <th style="width:90px;text-align:center">Tipo</th>
            <th style="width:70px;text-align:center">Computa</th>
            <th style="width:70px;text-align:center">Activo</th>
            <th style="width:60px;text-align:center">Orden</th>
            <th style="width:80px"></th>
          </tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ── PANEL FORMULARIO ────────────────────────────── -->
  <div id="panelFormCodigo" class="card shadow-sm mb-3 panel-form">
    <div class="card-header py-2">
      <strong id="tituloFormCodigo"><i class="fas fa-edit mr-1"></i>Código</strong>
    </div>
    <div class="card-body">
      <form id="formCodigo" autocomplete="off">
        <input type="hidden" id="codId">
        <div class="row">
          <div class="col-md-2 form-group">
            <label>Código <span class="text-danger">*</span></label>
            <input type="text" id="codCodigo" class="form-control form-control-sm" maxlength="10" placeholder="M">
          </div>
          <div class="col-md-5 form-group">
            <label>Descripción <span class="text-danger">*</span></label>
            <input type="text" id="codDesc" class="form-control form-control-sm" maxlength="150">
          </div>
          <div class="col-md-2 form-group">
            <label>Color</label>
            <input type="color" id="codColor" class="form-control form-control-sm" value="#cccccc">
          </div>
          <div class="col-md-3 form-group">
            <label>Tipo cómputo</label>
            <select id="codTipo" class="form-control form-control-sm">
              <option value="normal">Normal</option>
              <option value="reducida">Reducida</option>
              <option value="extra">Extraordinaria</option>
              <option value="ninguno">Ninguno / No computa</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2 form-group d-flex align-items-center pt-3">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="codComputa" value="1">
              <label class="custom-control-label" for="codComputa">Computa jornada</label>
            </div>
          </div>
          <div class="col-md-2 form-group d-flex align-items-center pt-3">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="codAfJornada" value="1">
              <label class="custom-control-label" for="codAfJornada">Afecta jornada</label>
            </div>
          </div>
          <div class="col-md-2 form-group d-flex align-items-center pt-3">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="codAfExtra" value="1">
              <label class="custom-control-label" for="codAfExtra">Afecta extra</label>
            </div>
          </div>
          <div class="col-md-2 form-group d-flex align-items-center pt-3">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="codReqObs" value="1">
              <label class="custom-control-label" for="codReqObs">Req. observación</label>
            </div>
          </div>
          <div class="col-md-2 form-group">
            <label>Orden</label>
            <input type="number" id="codOrden" class="form-control form-control-sm" value="0" min="0">
          </div>
          <div class="col-md-2 form-group">
            <label>Estado</label>
            <select id="codActivo" class="form-control form-control-sm">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>
        <div class="mt-1">
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>Guardar</button>
          <button type="button" id="btnCancelCodigo" class="btn btn-secondary btn-sm ml-2">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

</div>

<script src="js/cons_turnos_codigos.js"></script>
