<?php include("inc/seguridad.php"); ?>

<style>
    .card-header:not(.card-header-blue) {
        border-bottom: 3px solid #0084D9 !important;
        background: linear-gradient(to right, rgba(0,132,217,0.05), transparent) !important;
    }
    .form-control:focus {
        border-color: #0084D9 !important;
        box-shadow: 0 0 0 0.2rem rgba(0,132,217,0.25) !important;
    }
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .field-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
        margin-bottom: 4px;
    }
    /* Select2 */
    .select2-container--default .select2-selection--single {
        height: 38px; border: 1px solid #ced4da;
        border-radius: 4px; padding: 5px 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px; color: #495057; padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #0084D9;
        box-shadow: 0 0 0 0.2rem rgba(0,132,217,0.25);
        outline: none;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0084D9;
    }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-blue d-flex justify-content-between align-items-center py-2">
            <h5 class="m-0 text-white" id="tituloFichaUsuario">
                <i class="fas fa-user mr-2"></i>Usuario
            </h5>
            <button type="button" id="btnAtras" class="btn btn-sm btn-header-white">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="txtid">Código interno</label>
                        <input id="txtid" type="number" class="form-control" placeholder="(Automático)" disabled>
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtnombre">Nombre *</label>
                        <input type="text" class="form-control" id="txtnombre" placeholder="Nombre completo" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtemail">Email *</label>
                        <input type="email" class="form-control" id="txtemail" placeholder="correo@ejemplo.com" maxlength="150">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="field-label" for="cmbrol">Rol</label>
                        <select id="cmbrol" class="select2 form-control custom-select">
                            <option disabled>Seleccionar Rol</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="field-label" for="txtcontrasenia">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="txtcontrasenia"
                                   placeholder="Dejar vacío para no cambiar" maxlength="255">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="btnMostrarPass" tabindex="-1">
                                    <i class="fa fa-eye" id="iconoPass"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted" id="helpContrasenia">
                            Dejar vacío para mantener la contraseña actual.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizar" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Actualizar
            </button>
            <button type="button" id="btnEliminar" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtras" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="js/fichausuario.js"></script>