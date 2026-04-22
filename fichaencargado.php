<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left:0.5rem; padding-right:0.5rem; }
    .form-control:focus { border-color:#0084D9!important; box-shadow:0 0 0 0.2rem rgba(0,132,217,0.25)!important; }
    .field-label { font-weight:600; font-size:0.875rem; color:#495057; margin-bottom:4px; }
    .select2-container--default .select2-selection--single { height:38px; border:1px solid #ced4da; border-radius:4px; padding:5px 10px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:26px; color:#495057; padding-left:0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color:#0084D9; box-shadow:0 0 0 0.2rem rgba(0,132,217,0.25); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color:#0084D9; }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-blue py-2">
            <h5 class="m-0 text-white" id="tituloFichaEncargado"><i class="fas fa-user-tie mr-2"></i>Encargado</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtNumEncargado">Nº Encargado *</label>
                        <input type="number" class="form-control" id="txtNumEncargado" placeholder="Número de encargado">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtNombreEncargado">Nombre *</label>
                        <input type="text" class="form-control" id="txtNombreEncargado" placeholder="Nombre del encargado" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="cmbCargoEncargado">Cargo</label>
                        <select id="cmbCargoEncargado" class="form-control custom-select">
                            <option value="">-- Seleccionar --</option>
                            <option value="Policía">Policía</option>
                            <option value="Oficial">Oficial</option>
                            <option value="Oficial Accidental">Oficial Accidental</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtEstadoEncargado">Estado</label>
                        <input type="text" class="form-control" id="txtEstadoEncargado" placeholder="Activo como oficial o no" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="cmbAgenteEncargado">Agente asociado</label>
                        <select id="cmbAgenteEncargado" class="select2 form-control">
                            <option value="">-- Sin agente --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizarEncargado" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Guardar
            </button>
            <button type="button" id="btnEliminarEncargado" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtrasEncargado" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="js/fichaencargado.js"></script>
