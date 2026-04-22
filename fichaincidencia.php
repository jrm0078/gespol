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
            <h5 class="m-0 text-white" id="tituloFichaIncidencia"><i class="fas fa-exclamation-triangle mr-2"></i>Incidencia</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtNumIncidencia">Nº Incidencia</label>
                        <input type="number" class="form-control" id="txtNumIncidencia" placeholder="(Automático)" disabled>
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="cmbNumServicioInc">Nº Servicio vinculado</label>
                        <input type="number" class="form-control" id="cmbNumServicioInc" placeholder="Número de servicio">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtDestinatario">Destinatario</label>
                        <input type="text" class="form-control" id="txtDestinatario" maxlength="100" placeholder="Destinatario de la incidencia">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtEtiquetasFiltro">Etiquetas filtro <small class="text-muted">(separadas por comas)</small></label>
                        <input type="text" class="form-control" id="txtEtiquetasFiltro" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label">Agente 1</label>
                        <select id="cmbAgenteInc1" class="select2 form-control"><option value="">-</option></select>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Agente 2</label>
                        <select id="cmbAgenteInc2" class="select2 form-control"><option value="">-</option></select>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Agente 3</label>
                        <select id="cmbAgenteInc3" class="select2 form-control"><option value="">-</option></select>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Agente 4</label>
                        <select id="cmbAgenteInc4" class="select2 form-control"><option value="">-</option></select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="field-label" for="txtIncidencia">Incidencia</label>
                        <textarea id="txtIncidencia" class="form-control" rows="4" placeholder="Descripción de la incidencia..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtHistorial">Historial de incidencias</label>
                        <textarea id="txtHistorial" class="form-control" rows="3" placeholder="Anotaciones del historial..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizarIncidencia" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Guardar
            </button>
            <button type="button" id="btnEliminarIncidencia" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtrasIncidencia" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="js/fichaincidencia.js"></script>
