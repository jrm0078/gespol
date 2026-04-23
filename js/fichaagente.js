"use strict";

$(".preloader2").fadeIn();

document.getElementById("btnActualizarAgente").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarAgente").addEventListener('click', Eliminar, false);
document.getElementById("btnAtrasAgente").addEventListener('click', function() {
    CargarPagina('consagentes.php', 'Agentes', 'fas fa-user-shield');
}, false);

var pag_id1 = window.localStorage.getItem('pag_id1');
var lmodo;

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    CargaDatos();
}

function CargaDatos() {
    $.ajax({
        type: "POST",
        data: { id: pag_id1 || 0 },
        url: "inc/func_ajax.php/CargaAgente",
        crossDomain: true, cache: false, async: false, dataType: "json",
        success: function(resultado) {
            var result = resultado[0];
            if (result !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtNumAgente").value       = result.numagente;
                document.getElementById("txtNumAgente").disabled    = true;
                document.getElementById("txtNombreAgente").value    = result.nombre;
                document.getElementById("txtIndicativo").value      = result.indicativo || "";
                document.getElementById("cmbActivoAgente").value    = result.activo;
                document.getElementById("tituloFichaAgente").innerHTML = "<i class='fas fa-user-edit mr-2'></i>" + result.nombre;
                document.getElementById("btnActualizarAgente").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarAgente").style.display = "inline-block";
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaAgente").innerHTML = "<i class='fas fa-user-plus mr-2'></i>Nuevo Agente";
                document.getElementById("btnActualizarAgente").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarAgente").style.display = "none";
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    var id     = document.getElementById("txtNumAgente").value.trim();
    var nombre = document.getElementById("txtNombreAgente").value.trim();
    var w = "";
    if (id === "" || isNaN(parseInt(id, 10)) || parseInt(id, 10) <= 0) w += "Indica un número de agente válido.<br>";
    if (nombre === "") w += "El nombre es obligatorio.<br>";
    if (w !== "") { mostrarToast('warning', w.replace(/<br>/g, '\n')); return; }

    $.ajax({
        type: "POST",
        url: "inc/func_ajax.php/ActualizaAgente",
        data: {
            lmodo:      lmodo,
            id:         document.getElementById("txtNumAgente").value,
            nombre:     document.getElementById("txtNombreAgente").value,
            indicativo: document.getElementById("txtIndicativo").value,
            activo:     document.getElementById("cmbActivoAgente").value
        },
        dataType: "json", crossDomain: true, cache: false, async: false,
        success: function(result) {
            if (result.validacion == "ok") {
                mostrarToast('success', lmodo == "alta" ? "Agente creado" : "Agente actualizado");
                _recargarTab('consagentes.php', 'Agentes', 'fas fa-user-shield');
            } else if (result.validacion == "warning") {
                mostrarToast('warning', result.mensaje);
            } else {
                mostrarToast('error', 'Error al guardar: ' + result.error);
            }
        },
        error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
    });
}

function Eliminar() {
    Swal.fire({
        title: '¿Eliminar agente?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type: "POST", url: "inc/func_ajax.php/EliminarAgente",
                data: { id: document.getElementById("txtNumAgente").value },
                dataType: "json", crossDomain: true, cache: false, async: false,
                success: function(result) {
                    if (result.validacion == "ok") {
                        mostrarToast('success', 'Agente eliminado');
                        _recargarTab('consagentes.php', 'Agentes', 'fas fa-user-shield');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
