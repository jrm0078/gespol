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
    #txtDniLetra { background: #f8f9fa; font-weight: 700; color: #495057; }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-blue py-2">
            <h5 class="m-0 text-white" id="tituloFichaHabitante">
                <i class="fas fa-user mr-2"></i>Habitante
            </h5>
        </div>

        <div class="card-body">

            <!-- IDENTIFICACIÓN -->
            <div class="section-title">Identificación</div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Nº (Automático)</label>
                        <input id="txtIdHabitante" type="text" class="form-control" placeholder="(Auto)" disabled>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Número DNI</label>
                        <input id="txtDniNum" type="text" class="form-control" placeholder="12345678" maxlength="8">
                    </div>
                </div>
                <div class="col-4 col-md-1">
                    <div class="form-group">
                        <label class="field-label">Letra</label>
                        <input id="txtDniLetra" type="text" class="form-control text-center" placeholder="-" maxlength="1" readonly>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Apellidos</label>
                        <input id="txtApel" type="text" class="form-control" placeholder="Apellidos" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Nombre</label>
                        <input id="txtNom" type="text" class="form-control" placeholder="Nombre" maxlength="100">
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="form-group">
                        <label class="field-label">Sexo</label>
                        <select id="cmbSexo" class="form-control custom-select">
                            <option value="">-</option>
                            <option value="VARON">Varón</option>
                            <option value="MUJER">Mujer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- NACIMIENTO -->
            <div class="section-title">Nacimiento</div>
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Lugar de nacimiento</label>
                        <input id="txtLugnac" type="text" class="form-control" placeholder="Localidad" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Provincia nacimiento</label>
                        <input id="txtProvnac" type="text" class="form-control" placeholder="Provincia" maxlength="100">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Fecha nacimiento</label>
                        <input id="txtFecnac" type="date" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Padre</label>
                        <input id="txtPadre" type="text" class="form-control" placeholder="Nombre padre" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Madre</label>
                        <input id="txtMadre" type="text" class="form-control" placeholder="Nombre madre" maxlength="100">
                    </div>
                </div>
            </div>

            <!-- DOMICILIO -->
            <div class="section-title">Domicilio</div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="field-label">Calle</label>
                        <input id="txtCalle" type="text" class="form-control" placeholder="Dirección" maxlength="150">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label class="field-label">Población</label>
                        <input id="txtPob" type="text" class="form-control" placeholder="Población" maxlength="100">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Provincia</label>
                        <input id="txtProv" type="text" class="form-control" placeholder="Provincia" maxlength="100">
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="form-group">
                        <label class="field-label">C. Postal</label>
                        <input id="txtCPostal" type="text" class="form-control" placeholder="00000" maxlength="10">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">País origen</label>
                        <input id="txtPais" type="text" class="form-control" placeholder="País" maxlength="100">
                    </div>
                </div>
            </div>

            <!-- CONTACTO -->
            <div class="section-title">Contacto</div>
            <div class="row">
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Teléfono 1</label>
                        <input id="txtTf" type="text" class="form-control" placeholder="Teléfono" maxlength="20">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-group">
                        <label class="field-label">Teléfono 2</label>
                        <input id="txtTft" type="text" class="form-control" placeholder="Teléfono 2" maxlength="20">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="field-label">Email</label>
                        <input id="txtEmail" type="email" class="form-control" placeholder="correo@ejemplo.com" maxlength="150">
                    </div>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <div class="section-title">Observaciones</div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <textarea id="txtObservaciones" class="form-control" rows="3" placeholder="Anotaciones sobre la persona..."></textarea>
                    </div>
                </div>
            </div>

        </div><!-- card-body -->

        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizarHabitante" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Crear
            </button>
            <button type="button" id="btnEliminarHabitante" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtrasHabitante" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="js/fichahabitante.js"></script>
