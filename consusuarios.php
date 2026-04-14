<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
    #zero_config tbody tr:hover { background-color: rgba(0,132,217,0.04) !important; }
    .dataTables_filter input:focus {
        border-color: #0084D9 !important;
        box-shadow: 0 0 0 0.2rem rgba(0,132,217,0.2) !important;
    }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header card-header-blue d-flex justify-content-between align-items-center py-2">
            <h5 class="m-0 text-white">
                <i class="fas fa-users mr-2"></i>Usuarios
            </h5>
            <button type="button" id="btnCrear" class="btn btn-sm btn-header-white">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </button>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="zero_config" class='display' style='width:100%'>
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Cod. Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="js/consusuarios.js"></script>