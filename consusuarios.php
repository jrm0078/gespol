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
        <div class="card-header card-header-blue py-2">
            <h5 class="m-0 text-white">
                <i class="fas fa-users mr-2"></i>Usuarios
            </h5>
        </div>
        <div class="card-body p-2">
            <!-- TOOLBAR ERP -->
            <div class="tabla-toolbar">
                <div class="btn-group btn-group-sm" role="group">
                    <button id="btnTbAddUsuario" class="btn btn-toolbar-action" title="Nuevo usuario">
                        <i class="fas fa-plus"></i><span class="d-none d-sm-inline ml-1">A&ntilde;adir</span>
                    </button>
                    <button id="btnTbEditUsuario" class="btn btn-toolbar-action" title="Editar usuario seleccionado" disabled>
                        <i class="fas fa-edit"></i><span class="d-none d-sm-inline ml-1">Editar</span>
                    </button>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-toolbar-action dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Exportar">
                        <i class="fas fa-file-export"></i><span class="d-none d-sm-inline ml-1">Exportar</span>
                    </button>
                    <div class="dropdown-menu shadow-sm">
                        <a class="dropdown-item" href="#" data-exp="excel"><i class="fas fa-file-excel mr-2 text-success"></i>Excel</a>
                        <a class="dropdown-item" href="#" data-exp="csv"><i class="fas fa-file-alt mr-2 text-info"></i>CSV</a>
                        <a class="dropdown-item" href="#" data-exp="pdf"><i class="fas fa-file-pdf mr-2 text-danger"></i>PDF</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-exp="print"><i class="fas fa-print mr-2 text-secondary"></i>Imprimir</a>
                        <a class="dropdown-item" href="#" data-exp="copy"><i class="fas fa-copy mr-2 text-muted"></i>Copiar</a>
                    </div>
                </div>
            </div>
            <!-- FIN TOOLBAR -->
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

<!-- CONTEXT MENU USUARIOS -->
<div id="ctxMenuUsuarios" class="ctx-menu">
    <div class="ctx-menu-item" data-ctx-action="add"><i class="fas fa-plus"></i> A&ntilde;adir</div>
    <div class="ctx-menu-item" data-ctx-action="edit"><i class="fas fa-edit"></i> Editar</div>
    <div class="ctx-menu-separator"></div>
    <div class="ctx-menu-label">Exportar</div>
    <div class="ctx-menu-item" data-ctx-action="excel"><i class="fas fa-file-excel text-success"></i> Excel</div>
    <div class="ctx-menu-item" data-ctx-action="csv"><i class="fas fa-file-alt text-info"></i> CSV</div>
    <div class="ctx-menu-item" data-ctx-action="pdf"><i class="fas fa-file-pdf text-danger"></i> PDF</div>
    <div class="ctx-menu-item" data-ctx-action="print"><i class="fas fa-print text-secondary"></i> Imprimir</div>
    <div class="ctx-menu-item" data-ctx-action="copy"><i class="fas fa-copy text-muted"></i> Copiar</div>
</div>

<script src="js/consusuarios.js"></script>