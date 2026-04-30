<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
    #tbl_log tbody tr:hover { background-color: rgba(0,132,217,0.04) !important; }
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
                <i class="fas fa-list-alt mr-2"></i>Log de Accesos y Cambios
            </h5>
        </div>
        <div class="card-body p-2">
            <!-- TOOLBAR -->
            <div class="tabla-toolbar">
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
                <table id="tbl_log" class='display' style='width:100%'>
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Usuario</th>
                            <th>Acci&oacute;n</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CONTEXT MENU LOG -->
<div id="ctxMenuLog" class="ctx-menu">
    <div class="ctx-menu-label">Exportar</div>
    <div class="ctx-menu-item" data-ctx-action="excel"><i class="fas fa-file-excel text-success"></i> Excel</div>
    <div class="ctx-menu-item" data-ctx-action="csv"><i class="fas fa-file-alt text-info"></i> CSV</div>
    <div class="ctx-menu-item" data-ctx-action="pdf"><i class="fas fa-file-pdf text-danger"></i> PDF</div>
    <div class="ctx-menu-item" data-ctx-action="print"><i class="fas fa-print text-secondary"></i> Imprimir</div>
    <div class="ctx-menu-item" data-ctx-action="copy"><i class="fas fa-copy text-muted"></i> Copiar</div>
</div>

<script src="js/conslog.js?v=1"></script>
