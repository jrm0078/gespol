<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left:0.5rem; padding-right:0.5rem; }
    .form-control:focus { border-color:#0084D9!important; box-shadow:0 0 0 0.2rem rgba(0,132,217,0.25)!important; }
    .field-label { font-weight:600; font-size:0.875rem; color:#495057; margin-bottom:4px; }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-blue py-2">
            <h5 class="m-0 text-white" id="tituloFichaAgente"><i class="fas fa-user-shield mr-2"></i>Agente</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtNumAgente">Nº Agente (indicativo local) *</label>
                        <input type="number" class="form-control" id="txtNumAgente" placeholder="Número indicativo local">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtNombreAgente">Nombre y apellidos *</label>
                        <input type="text" class="form-control" id="txtNombreAgente" placeholder="Nombre completo" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtIndicativo">Indicativo (nº andaluz)</label>
                        <input type="number" class="form-control" id="txtIndicativo" placeholder="Número indicativo andaluz">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="cmbActivoAgente">Estado</label>
                        <select id="cmbActivoAgente" class="form-control custom-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizarAgente" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Guardar
            </button>
            <button type="button" id="btnEliminarAgente" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtrasAgente" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="js/fichaagente.js"></script>
