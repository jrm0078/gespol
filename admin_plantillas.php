<?php include("inc/seguridad.php"); ?>
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header bg-primary border-left-primary py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-cogs"></i> Administración de Plantillas</h6>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-8">
                    <h4>Gestionar Plantillas</h4>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn btn-success btn-sm" onclick="abrirFormularioPlantilla()">
                        <i class="fas fa-plus"></i> Crear Plantilla
                    </button>
                </div>
            </div>

            <div id="alertaContainer"></div>

            <!-- TABLA PLANTILLAS -->
            <div id="tablaPantillas" style="display:block;">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTabla">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FORMULARIO CREAR/EDITAR -->
            <div id="formularioSection" style="display:none;" class="mt-4">

                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="m-0" id="tituloFormulario">Nueva Plantilla</h5>
                    </div>
                    <div class="card-body">

                        <form id="formPlantilla">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Código Plantilla *</label>
                                        <input type="text" class="form-control" id="cod_plantilla" 
                                               placeholder="ej: presupuesto_1" maxlength="50" required>
                                        <small class="text-muted">Sin espacios. Ej: presupuesto_1</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre" 
                                               placeholder="Nombre de la plantilla" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Descripción</label>
                                <textarea class="form-control" id="descripcion" rows="2" 
                                          placeholder="Descripción breve de la plantilla"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tipo Documento</label>
                                <input type="text" class="form-control" id="tipo_documento" 
                                       placeholder="ej: PDF, Contrato, etc.">
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Contenido HTML *</label>
                                <small class="text-muted d-block mb-2">
                                    Usa <code>{%%nombre_variable%%}</code> para variables. Ej: Su cliente es {%%cliente_nombre%%}
                                </small>
                                <textarea class="form-control" id="contenido" rows="10" 
                                          placeholder="Contenido HTML con variables entre {%%...%%}" required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">SQL Consulta</label>
                                <small class="text-muted d-block mb-2">
                                    SQL que proporciona datos. Usa <code>[[parametro]]</code> para filtros.
                                </small>
                                <textarea class="form-control" id="sql_consulta" rows="4" 
                                          placeholder="SELECT * FROM tabla WHERE id = [[id]]"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <input type="checkbox" id="estado" checked> Activa
                                </label>
                            </div>

                            <div class="form-group text-right">
                                <button type="button" class="btn btn-secondary mr-2" onclick="cerrarFormularioPlantilla()">
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="guardarPlantilla()">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<script>

let plantillaEnEdicion = null;
let plantillas = [];

// Cargar tabla de plantillas
function cargarTablasPlantillas() {
    $.ajax({
        url: 'inc/plantillas/ajax_plantillas.php/CargaTablasPlantillas',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.validacion === 'ok') {
                plantillas = response.data;
                refrescarTabla();
            }
        }
    });
}

// Refrescar tabla
function refrescarTabla() {
    const tbody = document.getElementById('cuerpoTabla');
    tbody.innerHTML = '';

    plantillas.forEach(plantilla => {
        const estado = plantilla[4] == 1 ? '<span class="badge badge-success">Activa</span>' : 
                       '<span class="badge badge-danger">Inactiva</span>';
        
        const html = `
            <tr>
                <td><code>${plantilla[0]}</code></td>
                <td>${plantilla[1]}</td>
                <td><small>${plantilla[2] || '-'}</small></td>
                <td><small>${plantilla[3] || '-'}</small></td>
                <td>${estado}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editarPlantilla('${plantilla[0]}')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarPlantilla('${plantilla[0]}', '${plantilla[1]}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += html;
    });
}

// Abrir formulario para crear
function abrirFormularioPlantilla() {
    plantillaEnEdicion = null;
    document.getElementById('tituloFormulario').innerHTML = 'Nueva Plantilla';
    document.getElementById('cod_plantilla').disabled = false;
    document.getElementById('cod_plantilla').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('descripcion').value = '';
    document.getElementById('tipo_documento').value = '';
    document.getElementById('contenido').value = '';
    document.getElementById('sql_consulta').value = '';
    document.getElementById('estado').checked = true;
    
    document.getElementById('tablaPantillas').style.display = 'none';
    document.getElementById('formularioSection').style.display = 'block';
}

// Editar plantilla
function editarPlantilla(cod) {
    $.ajax({
        url: 'inc/plantillas/ajax_plantillas.php/ObtenerPlantilla?cod=' + cod,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.validacion === 'ok') {
                const p = response.data;
                plantillaEnEdicion = p.cod_plantilla;
                
                document.getElementById('tituloFormulario').innerHTML = 'Editar: ' + p.nombre;
                document.getElementById('cod_plantilla').disabled = true;
                document.getElementById('cod_plantilla').value = p.cod_plantilla;
                document.getElementById('nombre').value = p.nombre;
                document.getElementById('descripcion').value = p.descripcion || '';
                document.getElementById('tipo_documento').value = p.tipo_documento || '';
                document.getElementById('contenido').value = p.contenido;
                document.getElementById('sql_consulta').value = p.sql_consulta || '';
                document.getElementById('estado').checked = p.estado == 1;
                
                document.getElementById('tablaPantillas').style.display = 'none';
                document.getElementById('formularioSection').style.display = 'block';
            }
        }
    });
}

// Cerrar formulario
function cerrarFormularioPlantilla() {
    document.getElementById('formularioSection').style.display = 'none';
    document.getElementById('tablaPantillas').style.display = 'block';
}

// Guardar plantilla
function guardarPlantilla() {
    const cod = document.getElementById('cod_plantilla').value;
    const nombre = document.getElementById('nombre').value;
    const descripcion = document.getElementById('descripcion').value;
    const tipo = document.getElementById('tipo_documento').value;
    const contenido = document.getElementById('contenido').value;
    const sql = document.getElementById('sql_consulta').value;
    const estado = document.getElementById('estado').checked ? 1 : 0;
    
    if (!cod) {
        mostrarAlerta('Error', 'Código requerido', 'danger');
        return;
    }
    if (!nombre) {
        mostrarAlerta('Error', 'Nombre requerido', 'danger');
        return;
    }
    if (!contenido) {
        mostrarAlerta('Error', 'Contenido requerido', 'danger');
        return;
    }
    
    const action = plantillaEnEdicion ? 'ActualizarPlantilla' : 'CrearPlantilla';
    const url = 'inc/plantillas/ajax_plantillas.php/' + action;
    
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            cod_plantilla: cod,
            nombre: nombre,
            descripcion: descripcion,
            tipo_documento: tipo,
            contenido: contenido,
            sql_consulta: sql,
            estado: estado
        },
        dataType: 'json',
        success: function(response) {
            if (response.validacion === 'ok') {
                mostrarAlerta('Éxito', response.mensaje, 'success');
                cerrarFormularioPlantilla();
                cargarTablasPlantillas();
            } else {
                mostrarAlerta('Error', response.error, 'danger');
            }
        }
    });
}

// Eliminar plantilla
function eliminarPlantilla(cod, nombre) {
    if (confirm('¿Seguro que deseas eliminar la plantilla: ' + nombre + '?')) {
        $.ajax({
            url: 'inc/plantillas/ajax_plantillas.php/EliminarPlantilla',
            type: 'POST',
            data: {
                cod_plantilla: cod
            },
            dataType: 'json',
            success: function(response) {
                if (response.validacion === 'ok') {
                    mostrarAlerta('Éxito', response.mensaje, 'success');
                    cargarTablasPlantillas();
                } else {
                    mostrarAlerta('Error', response.error, 'danger');
                }
            }
        });
    }
}

// Mostrar alerta
function mostrarAlerta(titulo, mensaje, tipo) {
    const alertaHTML = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <strong>${titulo}:</strong> ${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    document.getElementById('alertaContainer').innerHTML = alertaHTML;
    
    setTimeout(() => {
        document.getElementById('alertaContainer').innerHTML = '';
    }, 5000);
}

// Inicializar
cargarTablasPlantillas();

</script>
