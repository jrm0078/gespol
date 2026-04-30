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
    .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
    .field-label { font-weight: 600; font-size: 0.875rem; color: #495057; margin-bottom: 4px; }
    .section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.05em; color: #0084D9; border-bottom: 1px solid #dee2e6;
        padding-bottom: 4px; margin-bottom: 12px; margin-top: 8px; }
    .titular-linked { background: #f8f9fa !important; }
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
            <h5 class="m-0 text-white" id="tituloFichaVehiculo">
                <i class="fas fa-car mr-2"></i>Vehículo
            </h5>
        </div>

        <div class="card-body">

            <!-- DATOS DEL VEHÍCULO -->
            <div class="section-title">Datos del Vehículo</div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Id Vehículo</label>
                        <input id="txtIdVehiculo" type="text" class="form-control" placeholder="Código" maxlength="20">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Matrícula</label>
                        <input id="txtMatricula" type="text" class="form-control" placeholder="0000 AAA" maxlength="20">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Marca / Modelo</label>
                        <input id="txtMarcaModelo" type="text" class="form-control" placeholder="Ej: SEAT Ibiza" maxlength="100">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Clase</label>
                        <input id="txtClase" type="text" class="form-control" placeholder="Turismo, Moto..." maxlength="50">
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="form-group">
                        <label class="field-label">Color</label>
                        <input id="txtColor" type="text" class="form-control" placeholder="Color" maxlength="30">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">F. Matriculación</label>
                        <input id="txtFecmat" type="date" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="field-label">Bastidor</label>
                        <input id="txtBast" type="text" class="form-control" placeholder="Número bastidor (VIN)" maxlength="50">
                    </div>
                </div>
            </div>

            <!-- SEGURO -->
            <div class="section-title">Seguro</div>
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Compañía</label>
                        <input id="txtCia" type="text" class="form-control" placeholder="Compañía aseguradora" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Nº Póliza</label>
                        <input id="txtPoliza" type="text" class="form-control" placeholder="Número de póliza" maxlength="50">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Validez Póliza</label>
                        <input id="txtValidezPoliza" type="text" class="form-control" placeholder="Ej: ANUAL" maxlength="50">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Fecha Exp. Póliza</label>
                        <input id="txtFechaExpPoliza" type="date" class="form-control">
                    </div>
                </div>
            </div>

            <!-- TITULAR -->
            <div class="section-title">
                Titular
                <span class="ml-2 font-weight-normal text-muted" style="font-size:0.75rem;text-transform:none;">
                    — Si está vinculado a un habitante, los datos se rellenan automáticamente
                </span>
            </div>
            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label class="field-label">Habitante titular <small class="text-muted">(opcional &mdash; busca por nombre o DNI)</small></label>
                        <select id="cmbHabitante" class="form-control" style="width:100%">
                            <option value="">-- Sin vincular --</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="form-group">
                        <label class="field-label">DNI Titular</label>
                        <input id="txtDnitit" type="text" class="form-control" placeholder="DNI" maxlength="15">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Apellidos Titular</label>
                        <input id="txtApetit" type="text" class="form-control" placeholder="Apellidos" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Nombre Titular</label>
                        <input id="txtNomtit" type="text" class="form-control" placeholder="Nombre" maxlength="100">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="field-label">Domicilio Titular</label>
                        <input id="txtDomtit" type="text" class="form-control" placeholder="Dirección" maxlength="150">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Población Titular</label>
                        <input id="txtPobtit" type="text" class="form-control" placeholder="Población" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Provincia Titular</label>
                        <input id="txtProvtit" type="text" class="form-control" placeholder="Provincia" maxlength="100">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Teléfono</label>
                        <input id="txtTft" type="text" class="form-control" placeholder="Teléfono" maxlength="20">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Email</label>
                        <input id="txtEmail" type="email" class="form-control" placeholder="Email" maxlength="150">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">C. Postal Veh.</label>
                        <input id="txtCPostalVeh" type="text" class="form-control" placeholder="C.P." maxlength="10">
                    </div>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <div class="section-title">Observaciones</div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <textarea id="txtObservaciones" class="form-control" rows="3" placeholder="Observaciones..."></textarea>
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="row mt-2">
                <div class="col-12">
                    <button id="btnActualizarVehiculo" class="btn btn-primary btn-sm mr-2">
                        <i class="fa fa-check mr-1"></i> Guardar
                    </button>
                    <button id="btnEliminarVehiculo" class="btn btn-danger btn-sm mr-2" style="display:none;">
                        <i class="fa fa-trash mr-1"></i> Eliminar
                    </button>
                    <button id="btnAtrasVehiculo" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left mr-1"></i> Volver
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="js/fichavehiculo.js"></script>
