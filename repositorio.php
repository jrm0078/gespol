<?php include("inc/seguridad.php"); ?>

<style>
    .container-fluid { padding-left: 0.5rem; padding-right: 0.5rem; }
    #tblRepositorio tbody tr:hover { background-color: rgba(0,132,217,0.04) !important; }
    .dataTables_filter input:focus {
        border-color: #0084D9 !important;
        box-shadow: 0 0 0 0.2rem rgba(0,132,217,0.2) !important;
    }
    .repo-thumb {
        width: 36px; height: 36px; object-fit: cover;
        border-radius: 4px; border: 1px solid #dee2e6;
        cursor: pointer;
    }
    .repo-icon {
        width: 36px; height: 36px; display: flex; align-items: center;
        justify-content: center; font-size: 1.3rem; color: #6c757d;
    }
    .dir-badge {
        background: #e8f4fd; color: #0084D9;
        border-radius: 4px; padding: 1px 6px; font-size: 0.78rem;
        display: inline-block;
    }
    #formularioRepositorioSection { display: none; }
</style>

<div class="preloader2">
    <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
</div>

<div class="container-fluid">

    <!-- ══════════════════════════════════════════════
         TABLA PRINCIPAL
    ══════════════════════════════════════════════ -->
    <div id="tablaRepositorioSection">
        <div class="card shadow-sm">
            <div class="card-header card-header-blue py-2">
                <h5 class="m-0 text-white">
                    <i class="fas fa-archive mr-2"></i>Repositorio de Ficheros
                </h5>
            </div>
            <div class="card-body p-2">
                <!-- TOOLBAR -->
                <div class="tabla-toolbar">
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btnTbAddRepo" class="btn btn-toolbar-action" title="Subir nuevo fichero">
                            <i class="fas fa-plus"></i><span class="d-none d-sm-inline ml-1">Añadir</span>
                        </button>
                        <button id="btnTbEditRepo" class="btn btn-toolbar-action" title="Editar seleccionado" disabled>
                            <i class="fas fa-edit"></i><span class="d-none d-sm-inline ml-1">Editar</span>
                        </button>
                        <button id="btnTbDelRepo" class="btn btn-toolbar-action text-danger" title="Eliminar seleccionado" disabled>
                            <i class="fas fa-trash"></i><span class="d-none d-sm-inline ml-1">Eliminar</span>
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm ml-1" role="group">
                        <button id="btnTbCopyUrl" class="btn btn-toolbar-action" title="Copiar URL del fichero seleccionado" disabled>
                            <i class="fas fa-link"></i><span class="d-none d-sm-inline ml-1">Copiar URL</span>
                        </button>
                    </div>
                </div>
                <!-- FIN TOOLBAR -->
                <div class="table-responsive">
                    <table id="tblRepositorio" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th><!-- id oculto -->
                                <th style="width:50px;"></th><!-- preview -->
                                <th>Descripción</th>
                                <th>Directorio</th>
                                <th>Fichero</th>
                                <th>Tipo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════
         FORMULARIO CREAR / EDITAR
    ══════════════════════════════════════════════ -->
    <div id="formularioRepositorioSection" class="mt-2">
        <div class="card shadow-sm">
            <div class="card-header card-header-blue py-2 d-flex align-items-center justify-content-between">
                <h5 class="m-0 text-white" id="tituloFormRepo">
                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Fichero
                </h5>
                <button type="button" class="btn btn-sm btn-header-white" onclick="cancelarFormRepo()">
                    <i class="fas fa-arrow-left mr-1"></i>Volver
                </button>
            </div>
            <div class="card-body">
                <div id="alertaRepo"></div>
                <input type="hidden" id="repo_id">

                <div class="row">
                    <!-- Descripción -->
                    <div class="col-12 col-md-8">
                        <div class="form-group">
                            <label class="font-weight-bold">Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="repo_descripcion"
                                   placeholder="Ej: Logo empresa, Firma director..." maxlength="255">
                        </div>
                    </div>
                    <!-- Directorio -->
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Directorio / Carpeta</label>
                            <div class="input-group">
                                <select class="form-control" id="repo_directorio_select" onchange="sincronizarDirInput()">
                                    <option value="">-- Raíz --</option>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                            title="Crear nueva carpeta" onclick="toggleNuevaCarepeta()">
                                        <i class="fas fa-folder-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="nuevaCarpetaRow" style="display:none;" class="mt-1">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="repo_nueva_carpeta"
                                           placeholder="nueva/carpeta">
                                    <div class="input-group-append">
                                        <button class="btn btn-success btn-sm" type="button" onclick="usarNuevaCarpeta()">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-secondary btn-sm" type="button" onclick="toggleNuevaCarepeta()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Solo letras, números, guiones y / para subcarpetas</small>
                            </div>
                            <input type="hidden" id="repo_directorio">
                        </div>
                    </div>
                </div>

                <!-- Fichero -->
                <div class="form-group">
                    <label class="font-weight-bold" id="lblFichero">
                        Fichero <span class="text-danger" id="ficheroRequerido">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="repo_fichero" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.mp4,.mp3,.svg">
                        <label class="custom-file-label" for="repo_fichero" id="lblFicheroNombre">Seleccionar fichero...</label>
                    </div>
                    <small class="text-muted">
                        Permitidos: imágenes, PDF, Office, ZIP, MP4/MP3, SVG, TXT/CSV. Máx. 20 MB.
                        <strong class="text-danger">No se permiten PHP, JS ni ejecutables.</strong>
                    </small>
                </div>

                <!-- Preview fichero actual (en modo editar) -->
                <div id="ficheroActualSection" style="display:none;" class="mb-3">
                    <label class="font-weight-bold text-muted">Fichero actual:</label>
                    <div class="d-flex align-items-center gap-2" style="gap:10px;">
                        <img id="previewActual" src="" alt="" class="repo-thumb" style="display:none;">
                        <a id="linkActual" href="#" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-external-link-alt mr-1"></i><span id="nombreActual"></span>
                        </a>
                        <small class="text-muted">(Sube un nuevo fichero para reemplazarlo)</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex" style="gap:8px;">
                <button class="btn btn-primary btn-sm" onclick="guardarRepo()">
                    <i class="fas fa-save mr-1"></i>Guardar
                </button>
                <button class="btn btn-secondary btn-sm" onclick="cancelarFormRepo()">
                    <i class="fas fa-times mr-1"></i>Cancelar
                </button>
            </div>
        </div>
    </div>

</div><!-- /container-fluid -->

<!-- CONTEXT MENU -->
<div id="ctxMenuRepositorio" class="ctx-menu">
    <div class="ctx-menu-item" data-ctx-action="add"><i class="fas fa-plus"></i> Añadir</div>
    <div class="ctx-menu-item" data-ctx-action="edit"><i class="fas fa-edit"></i> Editar</div>
    <div class="ctx-menu-item" data-ctx-action="copyurl"><i class="fas fa-link"></i> Copiar URL</div>
    <div class="ctx-menu-separator"></div>
    <div class="ctx-menu-item text-danger" data-ctx-action="delete"><i class="fas fa-trash"></i> Eliminar</div>
</div>

<script src="js/repositorio.js"></script>
