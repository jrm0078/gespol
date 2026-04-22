<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left:0.5rem; padding-right:0.5rem; }
    .form-control:focus { border-color:#0084D9!important; box-shadow:0 0 0 0.2rem rgba(0,132,217,0.25)!important; }
    .field-label { font-weight:600; font-size:0.875rem; color:#495057; margin-bottom:4px; }
    .section-title { font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#5a6a7a; margin-bottom:8px; padding-bottom:4px; border-bottom:2px solid #e9ecef; }
    .select2-container--default .select2-selection--single { height:38px; border:1px solid #ced4da; border-radius:4px; padding:5px 10px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:26px; color:#495057; padding-left:0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color:#0084D9; box-shadow:0 0 0 0.2rem rgba(0,132,217,0.25); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color:#0084D9; }
    .agente-row { display:flex; align-items:center; gap:6px; margin-bottom:6px; }
    .agente-row label { min-width:90px; font-size:0.8rem; font-weight:600; color:#495057; margin:0; }
    .extra-row { background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:8px; margin-bottom:6px; }
    #tbl_incidencias_srv tbody tr:hover { background-color:rgba(0,132,217,0.04)!important; }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <!-- CABECERA -->
    <div class="card shadow-sm mb-3">
        <div class="card-header card-header-blue py-2 d-flex align-items-center">
            <h5 class="m-0 text-white flex-grow-1" id="tituloFichaServicio"><i class="fas fa-calendar-alt mr-2"></i>Servicio</h5>
        </div>
        <div class="card-body">

            <!-- SECCIÓN 1: Datos básicos -->
            <div class="section-title"><i class="fas fa-info-circle mr-1"></i>Datos del servicio</div>
            <div class="row mb-3">
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="txtNumServicio">Nº Servicio</label>
                        <input type="number" class="form-control" id="txtNumServicio" placeholder="(Automático)" disabled>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="cmbTurno">Turno *</label>
                        <select id="cmbTurno" class="form-control custom-select">
                            <option value="">-- Seleccionar --</option>
                            <option value="día">Día</option>
                            <option value="tarde">Tarde</option>
                            <option value="noche">Noche</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="txtFecha">Fecha *</label>
                        <input type="datetime-local" class="form-control" id="txtFecha">
                    </div>
                </div>
                <div class="col-6 col-md-3" id="grupoFecha2">
                    <div class="form-group mb-2">
                        <label class="field-label" for="txtFecha2">Fecha 2 <span id="fecha2obligatorio" class="text-danger">(obligatoria en noche)</span></label>
                        <input type="datetime-local" class="form-control" id="txtFecha2">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="cmbTipoDia">Tipo de día</label>
                        <select id="cmbTipoDia" class="form-control custom-select">
                            <option value="">-- Seleccionar --</option>
                            <option value="normal">Normal</option>
                            <option value="festivo">Festivo</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="cmbDiaSemana">Día de la semana</label>
                        <select id="cmbDiaSemana" class="form-control custom-select">
                            <option value="">-- Seleccionar --</option>
                            <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                            <option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="cmbEncargado">Encargado de servicio</label>
                        <select id="cmbEncargado" class="select2 form-control">
                            <option value="">-- Sin encargado --</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="form-group mb-2">
                        <label class="field-label" for="txtValor">Valor (estadísticas)</label>
                        <input type="number" class="form-control" id="txtValor" placeholder="Contador">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Agentes de servicio -->
            <div class="section-title"><i class="fas fa-users mr-1"></i>Agentes de servicio</div>
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <?php
                    $agentes = ['numagente','numagente1','numagente2','numagente3','numagente4',
                                'numagente5','numagente6','numagente7','numagente8'];
                    foreach ($agentes as $i => $ag) {
                        $label = $i === 0 ? 'Agente 1' : 'Agente ' . ($i+1);
                        echo '<div class="agente-row"><label>' . $label . '</label>';
                        echo '<select id="cmb_' . $ag . '" class="select2-agente form-control form-control-sm"><option value="">-</option></select></div>';
                    }
                    ?>
                </div>
                <div class="col-12 col-md-6">
                    <?php
                    $agentes2 = ['numagente9','numagente10','numagente11','numagente12',
                                 'numagente13','numagente14','numagente15'];
                    foreach ($agentes2 as $i => $ag) {
                        $num = $i + 10;
                        echo '<div class="agente-row"><label>Agente ' . $num . '</label>';
                        echo '<select id="cmb_' . $ag . '" class="select2-agente form-control form-control-sm"><option value="">-</option></select></div>';
                    }
                    ?>
                </div>
            </div>

            <!-- SECCIÓN 3: Servicios extraordinarios -->
            <div class="section-title">
                <i class="fas fa-star mr-1"></i>Servicios extraordinarios
                <button type="button" class="btn btn-xs btn-outline-primary ml-2" id="btnToggleExtras" style="font-size:0.7rem;padding:1px 8px;">
                    <i class="fas fa-chevron-down"></i> Mostrar/ocultar
                </button>
            </div>
            <div id="seccionExtras" style="display:none;">
                <?php
                for ($i = 0; $i <= 9; $i++) {
                    $sufijo = $i === 0 ? '' : $i;
                    echo '<div class="extra-row row align-items-center">';
                    echo '<div class="col-12 col-md-3"><label class="field-label mb-1">Agente extra ' . ($i+1) . '</label>';
                    echo '<select id="cmb_agenteextra' . $sufijo . '" class="select2-agente form-control form-control-sm"><option value="">-</option></select></div>';
                    echo '<div class="col-6 col-md-2"><label class="field-label mb-1">Hora inicio</label>';
                    echo '<input type="datetime-local" class="form-control form-control-sm" id="txtHoraInicio' . $sufijo . '"></div>';
                    echo '<div class="col-6 col-md-2"><label class="field-label mb-1">Hora final</label>';
                    echo '<input type="datetime-local" class="form-control form-control-sm" id="txtHoraFinal' . $sufijo . '"></div>';
                    echo '</div>';
                }
                ?>
                <div class="form-group mt-2">
                    <label class="field-label" for="txtTextoExtra">Notas servicios extraordinarios</label>
                    <textarea id="txtTextoExtra" class="form-control" rows="3" placeholder="Otras anotaciones sobre los servicios extraordinarios..."></textarea>
                </div>
            </div>

        </div>
        <div class="card-footer bg-white d-flex align-items-center" style="gap:8px;">
            <button type="button" id="btnActualizarServicio" class="btn btn-success">
                <i class="fa fa-check mr-1"></i> Guardar
            </button>
            <button type="button" id="btnEliminarServicio" class="btn btn-danger" style="display:none;">
                <i class="fa fa-trash mr-1"></i> Eliminar
            </button>
            <button type="button" id="btnAtrasServicio" class="btn btn-secondary ml-auto">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>

    <!-- SECCIÓN 4: Incidencias del servicio -->
    <div class="card shadow-sm" id="cardIncidenciasServicio" style="display:none;">
        <div class="card-header card-header-blue py-2 d-flex align-items-center">
            <h6 class="m-0 text-white flex-grow-1"><i class="fas fa-exclamation-triangle mr-2"></i>Incidencias del servicio</h6>
            <button type="button" id="btnNuevaIncidenciaServicio" class="btn btn-sm btn-light">
                <i class="fas fa-plus mr-1"></i>Nueva incidencia
            </button>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="tbl_incidencias_srv" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px;"></th>
                            <th>Nº</th>
                            <th>Destinatario</th>
                            <th>Etiquetas</th>
                            <th>Agente</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INCIDENCIA inline -->
<div class="modal fade" id="modalIncidencia" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header card-header-blue py-2">
                <h6 class="modal-title text-white" id="tituloModalIncidencia"><i class="fas fa-exclamation-triangle mr-2"></i>Incidencia</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txtNumIncidencia">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="field-label">Destinatario</label>
                            <input type="text" class="form-control" id="txtDestinatarioInc" maxlength="100" placeholder="Destinatario de la incidencia">
                        </div>
                        <div class="form-group">
                            <label class="field-label">Etiquetas filtro <small class="text-muted">(separadas por comas)</small></label>
                            <input type="text" class="form-control" id="txtEtiquetasInc" maxlength="100">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="field-label">Agente 1</label>
                            <select id="cmbAgenteInc1" class="select2-inc form-control"><option value="">-</option></select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Agente 2</label>
                            <select id="cmbAgenteInc2" class="select2-inc form-control"><option value="">-</option></select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Agente 3</label>
                            <select id="cmbAgenteInc3" class="select2-inc form-control"><option value="">-</option></select>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Agente 4</label>
                            <select id="cmbAgenteInc4" class="select2-inc form-control"><option value="">-</option></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="field-label">Incidencia</label>
                            <textarea id="txtIncidenciaTexto" class="form-control" rows="4" placeholder="Descripción de la incidencia..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Historial</label>
                            <textarea id="txtHistorialInc" class="form-control" rows="3" placeholder="Anotaciones historial..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnGuardarIncidencia" class="btn btn-success"><i class="fa fa-check mr-1"></i>Guardar</button>
                <button type="button" id="btnEliminarIncidencia" class="btn btn-danger" style="display:none;"><i class="fa fa-trash mr-1"></i>Eliminar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script src="js/fichaservicio.js"></script>
