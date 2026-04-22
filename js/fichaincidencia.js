"use strict";

$(".preloader2").fadeIn();

var pag_id1 = window.localStorage.getItem('pag_id1');
var lmodo   = 'alta';

document.getElementById("btnActualizarIncidencia").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarIncidencia").addEventListener('click',  Eliminar,   false);
document.getElementById("btnAtrasIncidencia").addEventListener('click', function() {
    CargarPagina('consincidencias.php', 'Incidencias', 'fas fa-exclamation-triangle');
}, false);

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    // Cargar select2 agentes
    ['cmbAgenteInc1','cmbAgenteInc2','cmbAgenteInc3','cmbAgenteInc4'].forEach(function(id) {
        CmbIniciar($('#' + id));
    });
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ComboAgentes",
        crossDomain:true, cache:false, async:false, dataType:"json",
        success:function(data) {
            ['cmbAgenteInc1','cmbAgenteInc2','cmbAgenteInc3','cmbAgenteInc4'].forEach(function(id) {
                $.each(data, function(i,v) { CmbCargaValor($('#' + id), v.numagente, v.nombre); });
            });
        }
    });
    CargaDatos();
}

function CargaDatos() {
    if (!pag_id1 || pag_id1 == "0" || pag_id1 == "") {
        lmodo = "alta";
        document.getElementById("tituloFichaIncidencia").innerHTML = "<i class='fas fa-plus mr-2'></i>Nueva Incidencia";
        document.getElementById("btnActualizarIncidencia").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
        document.getElementById("btnEliminarIncidencia").style.display = "none";
        return;
    }

    $.ajax({
        type:"POST", data:{id: pag_id1},
        url:"inc/func_ajax.php/CargaIncidencia",
        crossDomain:true, cache:false, async:false, dataType:"json",
        success:function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtNumIncidencia").value   = r.numincidencia;
                document.getElementById("cmbNumServicioInc").value  = r.numservicio     || '';
                document.getElementById("txtDestinatario").value    = r.destinatario    || '';
                document.getElementById("txtEtiquetasFiltro").value = r.etiquetas_filtro || '';
                document.getElementById("txtIncidencia").value      = r.incidencias     || '';
                document.getElementById("txtHistorial").value       = r.historialincidencias || '';
                CmbSeleccionaValor($('#cmbAgenteInc1'), r.numagente);
                CmbSeleccionaValor($('#cmbAgenteInc2'), r.numagente1);
                CmbSeleccionaValor($('#cmbAgenteInc3'), r.numagente2);
                CmbSeleccionaValor($('#cmbAgenteInc4'), r.numagente3);
                document.getElementById("tituloFichaIncidencia").innerHTML = "<i class='fas fa-exclamation-triangle mr-2'></i>Incidencia #" + r.numincidencia;
                document.getElementById("btnActualizarIncidencia").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarIncidencia").style.display = "inline-block";
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaIncidencia").innerHTML = "<i class='fas fa-plus mr-2'></i>Nueva Incidencia";
                document.getElementById("btnActualizarIncidencia").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarIncidencia").style.display = "none";
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ActualizaIncidencia",
        data:{
            lmodo:               lmodo,
            id:                  document.getElementById("txtNumIncidencia").value,
            numservicio:         document.getElementById("cmbNumServicioInc").value,
            incidencias:         document.getElementById("txtIncidencia").value,
            destinatario:        document.getElementById("txtDestinatario").value,
            etiquetas_filtro:    document.getElementById("txtEtiquetasFiltro").value,
            numagente:           document.getElementById("cmbAgenteInc1").value,
            numagente1:          document.getElementById("cmbAgenteInc2").value,
            numagente2:          document.getElementById("cmbAgenteInc3").value,
            numagente3:          document.getElementById("cmbAgenteInc4").value,
            historialincidencias:document.getElementById("txtHistorial").value,
            valor:               ""
        },
        dataType:"json", crossDomain:true, cache:false, async:false,
        success:function(result) {
            if (result.validacion == "ok") {
                mostrarToast('success', lmodo=="alta" ? "Incidencia creada" : "Incidencia actualizada");
                if (lmodo === "alta" && result.id) {
                    window.localStorage.setItem('pag_id1', result.id);
                    pag_id1 = result.id;
                    lmodo = "edicion";
                    CargaDatos();
                }
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
        title:'¿Eliminar incidencia?', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d',
        cancelButtonText:'Cancelar', confirmButtonText:'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type:"POST", url:"inc/func_ajax.php/EliminarIncidencia",
                data:{id: document.getElementById("txtNumIncidencia").value},
                dataType:"json", crossDomain:true, cache:false, async:false,
                success:function(result) {
                    if (result.validacion == "ok") {
                        mostrarToast('success', 'Incidencia eliminada');
                        _recargarTab('consincidencias.php', 'Incidencias', 'fas fa-exclamation-triangle');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
