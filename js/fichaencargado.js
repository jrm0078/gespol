"use strict";

$(".preloader2").fadeIn();

document.getElementById("btnActualizarEncargado").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarEncargado").addEventListener('click', Eliminar, false);
document.getElementById("btnAtrasEncargado").addEventListener('click', function() {
    CargarPagina('consencargados.php', 'Encargados', 'fas fa-user-tie');
}, false);

var pag_id1 = window.localStorage.getItem('pag_id1');
var lmodo;

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    // Cargar select2 de agentes
    CmbIniciar($('#cmbAgenteEncargado'));
    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ComboAgentes",
        crossDomain: true, cache: false, async: false, dataType: "json",
        success: function(data) {
            $.each(data, function(i, v) {
                CmbCargaValor($('#cmbAgenteEncargado'), v.numagente, v.nombre);
            });
        }
    });
    CargaDatos();
}

function CargaDatos() {
    $.ajax({
        type: "POST", data: { id: pag_id1 || 0 },
        url: "inc/func_ajax.php/CargaEncargado",
        crossDomain: true, cache: false, async: false, dataType: "json",
        success: function(resultado) {
            var result = resultado[0];
            if (result !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtNumEncargado").value    = result.numencargado;
                document.getElementById("txtNumEncargado").disabled = true;
                document.getElementById("txtNombreEncargado").value = result.encargado;
                document.getElementById("cmbCargoEncargado").value  = result.cargo;
                document.getElementById("txtEstadoEncargado").value = result.estado;
                CmbSeleccionaValor($('#cmbAgenteEncargado'), result.numagente);
                document.getElementById("tituloFichaEncargado").innerHTML = "<i class='fas fa-user-edit mr-2'></i>" + result.encargado;
                document.getElementById("btnActualizarEncargado").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarEncargado").style.display = "inline-block";
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaEncargado").innerHTML = "<i class='fas fa-user-plus mr-2'></i>Nuevo Encargado";
                document.getElementById("btnActualizarEncargado").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarEncargado").style.display = "none";
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    var id        = document.getElementById("txtNumEncargado").value.trim();
    var encargado = document.getElementById("txtNombreEncargado").value.trim();
    var w = "";
    if (id === "" || isNaN(parseInt(id, 10)) || parseInt(id, 10) <= 0) w += "Indica un número de encargado válido.<br>";
    if (encargado === "") w += "El nombre es obligatorio.<br>";
    if (w !== "") { mostrarToast('warning', w.replace(/<br>/g, '\n')); return; }

    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ActualizaEncargado",
        data: {
            lmodo:     lmodo,
            id:        document.getElementById("txtNumEncargado").value,
            encargado: document.getElementById("txtNombreEncargado").value,
            cargo:     document.getElementById("cmbCargoEncargado").value,
            estado:    document.getElementById("txtEstadoEncargado").value,
            numagente: document.getElementById("cmbAgenteEncargado").value
        },
        dataType: "json", crossDomain: true, cache: false, async: false,
        success: function(result) {
            if (result.validacion == "ok") {
                mostrarToast('success', lmodo == "alta" ? "Encargado creado" : "Encargado actualizado");
                _recargarTab('consencargados.php', 'Encargados', 'fas fa-user-tie');
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
        title: '¿Eliminar encargado?', text: 'Esta acción no se puede deshacer.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type: "POST", url: "inc/func_ajax.php/EliminarEncargado",
                data: { id: document.getElementById("txtNumEncargado").value },
                dataType: "json", crossDomain: true, cache: false, async: false,
                success: function(result) {
                    if (result.validacion == "ok") {
                        mostrarToast('success', 'Encargado eliminado');
                        _recargarTab('consencargados.php', 'Encargados', 'fas fa-user-tie');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
