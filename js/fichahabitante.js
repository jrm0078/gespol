"use strict";

$(".preloader2").fadeIn();

var pag_id1 = window.localStorage.getItem('pag_id1');
var lmodo   = 'alta';

document.getElementById("btnActualizarHabitante").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarHabitante").addEventListener('click',  Eliminar,   false);
document.getElementById("btnAtrasHabitante").addEventListener('click', function() {
    CargarPagina('conshabitantes.php', 'Habitantes', 'fas fa-users');
}, false);

// Calcular letra DNI al cambiar el número
document.getElementById("txtDniNum").addEventListener('input', function() {
    var num = parseInt(this.value, 10);
    if (!isNaN(num) && this.value.length >= 7) {
        var letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        document.getElementById("txtDniLetra").value = letras[num % 23];
    } else {
        document.getElementById("txtDniLetra").value = '';
    }
});

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    CargaDatos();
}

function CargaDatos() {
    if (!pag_id1 || pag_id1 === "0" || pag_id1 === "") {
        lmodo = "alta";
        document.getElementById("tituloFichaHabitante").innerHTML = "<i class='fas fa-user-plus mr-2'></i>Nuevo Habitante";
        document.getElementById("btnActualizarHabitante").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
        document.getElementById("btnEliminarHabitante").style.display = "none";
        return;
    }

    $.ajax({
        type: "POST", data: { id: pag_id1 },
        url: "inc/func_ajax.php/CargaHabitante",
        crossDomain: true, cache: false, dataType: "json",
        success: function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                lmodo = "edicion";
                // Separar número y letra del DNI almacenado
                var dniCompleto = r.dni || '';
                var dniNum = dniCompleto.replace(/[^0-9]/g, '');
                var dniLetra = dniCompleto.replace(/[^a-zA-Z]/g, '').toUpperCase();

                document.getElementById("txtIdHabitante").value    = r.idhabitante;
                document.getElementById("txtDniNum").value         = dniNum;
                document.getElementById("txtDniLetra").value       = dniLetra;
                document.getElementById("txtApel").value           = r.apel        || '';
                document.getElementById("txtNom").value            = r.nom         || '';
                document.getElementById("cmbSexo").value           = r.sexo        || '';
                document.getElementById("txtLugnac").value         = r.lugnac      || '';
                document.getElementById("txtProvnac").value        = r.provnac     || '';
                document.getElementById("txtFecnac").value         = r.fecnac      ? r.fecnac.substring(0,10) : '';
                document.getElementById("txtPadre").value          = r.padre       || '';
                document.getElementById("txtMadre").value          = r.madre       || '';
                document.getElementById("txtCalle").value          = r.calle       || '';
                document.getElementById("txtPob").value            = r.pob         || '';
                document.getElementById("txtProv").value           = r.prov        || '';
                document.getElementById("txtCPostal").value        = r.CPostal     || '';
                document.getElementById("txtPais").value           = r.Pais        || '';
                document.getElementById("txtTf").value             = r.tf          || '';
                document.getElementById("txtTft").value            = r.tft         || '';
                document.getElementById("txtEmail").value          = r.email       || '';
                document.getElementById("txtObservaciones").value  = r.Observaciones || '';

                document.getElementById("tituloFichaHabitante").innerHTML = "<i class='fas fa-user-edit mr-2'></i>Habitante #" + r.idhabitante;
                document.getElementById("btnActualizarHabitante").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarHabitante").style.display = "inline-block";
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaHabitante").innerHTML = "<i class='fas fa-user-plus mr-2'></i>Nuevo Habitante";
                document.getElementById("btnActualizarHabitante").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarHabitante").style.display = "none";
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    var dniNum   = document.getElementById("txtDniNum").value.trim();
    var dniLetra = document.getElementById("txtDniLetra").value.trim();
    var dni      = dniNum + dniLetra;

    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ActualizaHabitante",
        data: {
            lmodo:         lmodo,
            id:            document.getElementById("txtIdHabitante").value,
            dni:           dni,
            apel:          document.getElementById("txtApel").value,
            nom:           document.getElementById("txtNom").value,
            sexo:          document.getElementById("cmbSexo").value,
            lugnac:        document.getElementById("txtLugnac").value,
            provnac:       document.getElementById("txtProvnac").value,
            fecnac:        document.getElementById("txtFecnac").value,
            padre:         document.getElementById("txtPadre").value,
            madre:         document.getElementById("txtMadre").value,
            calle:         document.getElementById("txtCalle").value,
            pob:           document.getElementById("txtPob").value,
            prov:          document.getElementById("txtProv").value,
            CPostal:       document.getElementById("txtCPostal").value,
            Pais:          document.getElementById("txtPais").value,
            tf:            document.getElementById("txtTf").value,
            tft:           document.getElementById("txtTft").value,
            email:         document.getElementById("txtEmail").value,
            Observaciones: document.getElementById("txtObservaciones").value
        },
        dataType: "json", crossDomain: true, cache: false,
        success: function(result) {
            if (result.validacion === "ok") {
                mostrarToast('success', lmodo === "alta" ? "Habitante creado" : "Habitante actualizado");
                if (lmodo === "alta" && result.id) {
                    window.localStorage.setItem('pag_id1', result.id);
                    pag_id1 = result.id;
                    lmodo = "edicion";
                    CargaDatos();
                }
            } else if (result.validacion === "warning") {
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
        title: '¿Eliminar habitante?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type: "POST", url: "inc/func_ajax.php/EliminarHabitante",
                data: { id: document.getElementById("txtIdHabitante").value },
                dataType: "json", crossDomain: true, cache: false,
                success: function(result) {
                    if (result.validacion === "ok") {
                        mostrarToast('success', 'Habitante eliminado');
                        CargarPagina('conshabitantes.php', 'Habitantes', 'fas fa-users');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
