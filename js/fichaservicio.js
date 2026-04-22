"use strict";

$(".preloader2").fadeIn();

// IDs de agentes de servicio
var AGENTES_SRV = ['numagente','numagente1','numagente2','numagente3','numagente4',
    'numagente5','numagente6','numagente7','numagente8','numagente9',
    'numagente10','numagente11','numagente12','numagente13','numagente14','numagente15'];

// IDs extras (0 = sin sufijo)
var EXTRAS = [0,1,2,3,4,5,6,7,8,9];

var pag_id1    = window.localStorage.getItem('pag_id1');
var lmodo      = 'alta';
var lmodoInc   = 'alta';
var _agentesData = [];

document.getElementById("btnActualizarServicio").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarServicio").addEventListener('click', Eliminar, false);
document.getElementById("btnAtrasServicio").addEventListener('click', function() {
    CargarPagina('consservicios.php', 'Servicios', 'fas fa-calendar-alt');
}, false);
document.getElementById("btnToggleExtras").addEventListener('click', function() {
    var sec = document.getElementById("seccionExtras");
    sec.style.display = sec.style.display === 'none' ? '' : 'none';
}, false);
document.getElementById("cmbTurno").addEventListener('change', actualizarFecha2, false);

// Incidencias
document.getElementById("btnNuevaIncidenciaServicio").addEventListener('click', function() { abrirModalIncidencia(null); }, false);
document.getElementById("btnGuardarIncidencia").addEventListener('click', GuardarIncidencia, false);
document.getElementById("btnEliminarIncidencia").addEventListener('click', EliminarIncidencia, false);

Cargar();
$(".preloader2").fadeOut();

function actualizarFecha2() {
    var turno = document.getElementById("cmbTurno").value;
    var lbl   = document.getElementById("fecha2obligatorio");
    if (turno === "noche") {
        lbl.style.display = "";
    } else {
        lbl.style.display = "none";
        document.getElementById("txtFecha2").value = "";
    }
}

function Cargar() {
    // Cargar select2 de encargados
    CmbIniciar($('#cmbEncargado'));
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ComboEncargados",
        crossDomain:true, cache:false, async:false, dataType:"json",
        success:function(data) { $.each(data, function(i,v) { CmbCargaValor($('#cmbEncargado'), v.numencargado, v.encargado); }); }
    });

    // Cargar agentes para todos los selects
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ComboAgentes",
        crossDomain:true, cache:false, async:false, dataType:"json",
        success:function(data) {
            _agentesData = data;
            // Agentes servicio
            AGENTES_SRV.forEach(function(ag) {
                var $s = $('#cmb_' + ag);
                $s.select2();
                $.each(data, function(i,v) { CmbCargaValor($s, v.numagente, v.nombre); });
            });
            // Extras
            EXTRAS.forEach(function(i) {
                var suf = i === 0 ? '' : i;
                var $s = $('#cmb_agenteextra' + suf);
                $s.select2({ width: '100%' });
                $.each(data, function(j,v) { CmbCargaValor($s, v.numagente, v.nombre); });
            });
            // Modal incidencias
            ['cmbAgenteInc1','cmbAgenteInc2','cmbAgenteInc3','cmbAgenteInc4'].forEach(function(id) {
                var $s = $('#' + id);
                $s.select2({ dropdownParent: $('#modalIncidencia') });
                $.each(data, function(j,v) { CmbCargaValor($s, v.numagente, v.nombre); });
            });
        }
    });

    actualizarFecha2();
    CargaDatos();
}

function CargaDatos() {
    if (!pag_id1 || pag_id1 == "0" || pag_id1 == "") {
        lmodo = "alta";
        document.getElementById("tituloFichaServicio").innerHTML = "<i class='fas fa-plus mr-2'></i>Nuevo Servicio";
        document.getElementById("btnActualizarServicio").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
        document.getElementById("btnEliminarServicio").style.display = "none";
        document.getElementById("cardIncidenciasServicio").style.display = "none";
        return;
    }

    $.ajax({
        type:"POST", data:{id: pag_id1},
        url:"inc/func_ajax.php/CargaServicio",
        crossDomain:true, cache:false, async:false, dataType:"json",
        success:function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtNumServicio").value = r.numservicio;
                document.getElementById("txtFecha").value       = r.fecha    ? r.fecha.replace(' ','T').substring(0,16)  : '';
                document.getElementById("txtFecha2").value      = r.fecha2   ? r.fecha2.replace(' ','T').substring(0,16) : '';
                document.getElementById("cmbTurno").value       = r.turno    || '';
                document.getElementById("cmbTipoDia").value     = r.tipodia  || '';
                document.getElementById("cmbDiaSemana").value   = r.diasemana || '';
                document.getElementById("txtValor").value       = r.valor    || '';
                document.getElementById("txtTextoExtra").value  = r.textoservicioextra || '';
                CmbSeleccionaValor($('#cmbEncargado'), r.numagenteencargado);
                actualizarFecha2();

                AGENTES_SRV.forEach(function(ag) { CmbSeleccionaValor($('#cmb_' + ag), r[ag]); });
                EXTRAS.forEach(function(i) {
                    var suf = i === 0 ? '' : i;
                    CmbSeleccionaValor($('#cmb_agenteextra' + suf), r['agenteextra' + suf]);
                    document.getElementById("txtHoraInicio" + suf).value = r['horainicio' + suf] ? r['horainicio' + suf].replace(' ','T').substring(0,16) : '';
                    document.getElementById("txtHoraFinal"  + suf).value = r['horafinal'  + suf] ? r['horafinal'  + suf].replace(' ','T').substring(0,16) : '';
                });

                document.getElementById("tituloFichaServicio").innerHTML = "<i class='fas fa-calendar-alt mr-2'></i>Servicio #" + r.numservicio;
                document.getElementById("btnActualizarServicio").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarServicio").style.display = "inline-block";
                document.getElementById("cardIncidenciasServicio").style.display = "";
                CargaTablaIncidencias();
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaServicio").innerHTML = "<i class='fas fa-plus mr-2'></i>Nuevo Servicio";
                document.getElementById("btnActualizarServicio").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarServicio").style.display = "none";
                document.getElementById("cardIncidenciasServicio").style.display = "none";
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function recogerDatos() {
    var d = {
        lmodo:     lmodo,
        id:        document.getElementById("txtNumServicio").value,
        fecha:     document.getElementById("txtFecha").value.replace('T',' '),
        fecha2:    document.getElementById("txtFecha2").value.replace('T',' '),
        turno:     document.getElementById("cmbTurno").value,
        tipodia:   document.getElementById("cmbTipoDia").value,
        diasemana: document.getElementById("cmbDiaSemana").value,
        numagenteencargado: document.getElementById("cmbEncargado").value,
        textoservicioextra: document.getElementById("txtTextoExtra").value,
        valor:     document.getElementById("txtValor").value
    };
    AGENTES_SRV.forEach(function(ag) { d[ag] = document.getElementById("cmb_" + ag).value; });
    EXTRAS.forEach(function(i) {
        var suf = i === 0 ? '' : i;
        d['agenteextra' + suf]  = document.getElementById("cmb_agenteextra" + suf).value;
        d['horainicio'  + suf]  = document.getElementById("txtHoraInicio" + suf).value.replace('T',' ');
        d['horafinal'   + suf]  = document.getElementById("txtHoraFinal"  + suf).value.replace('T',' ');
    });
    return d;
}

function Actualizar() {
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ActualizaServicio",
        data: recogerDatos(),
        dataType:"json", crossDomain:true, cache:false, async:false,
        success:function(result) {
            if (result.validacion == "ok") {
                // En alta, recargar con el nuevo ID para mostrar incidencias
                if (lmodo === "alta" && result.id) {
                    window.localStorage.setItem('pag_id1', result.id);
                    pag_id1 = result.id;
                    mostrarToast('success', 'Servicio creado');
                    lmodo = "edicion";
                    CargaDatos();
                } else {
                    mostrarToast('success', 'Servicio actualizado');
                }
                // Refrescar la lista en segundo plano si está abierta
                if (window._gTabs) {
                    var _consTab = window._gTabs.find(function(t) { return t.pagina === 'consservicios.php'; });
                    if (_consTab) {
                        var $p = $('#' + _tabPanelId('consservicios.php'));
                        $p.find('table').each(function() { if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy(); });
                        $p.load('consservicios.php');
                    }
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
        title:'¿Eliminar servicio?', text:'Se eliminarán también sus incidencias.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d',
        cancelButtonText:'Cancelar', confirmButtonText:'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type:"POST", url:"inc/func_ajax.php/EliminarServicio",
                data:{id: document.getElementById("txtNumServicio").value},
                dataType:"json", crossDomain:true, cache:false, async:false,
                success:function(result) {
                    if (result.validacion == "ok") {
                        mostrarToast('success', 'Servicio eliminado');
                        _recargarTab('consservicios.php', 'Servicios', 'fas fa-calendar-alt');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}

// ── INCIDENCIAS DEL SERVICIO ──────────────────────────────────────────────────

function CargaTablaIncidencias() {
    if ($.fn.DataTable.isDataTable('#tbl_incidencias_srv')) {
        $('#tbl_incidencias_srv').DataTable().destroy();
    }
    var numservicio = document.getElementById("txtNumServicio").value;

    $('#tbl_incidencias_srv').DataTable({
        "sDom": 't<pl>', "bPaginate":true, "bLengthChange":false, "bFilter":false,
        "bInfo":false, "bAutoWidth":false, "pageLength":20,
        "language": { "emptytable":"Sin incidencias", "zeroRecords":"Sin incidencias",
            "paginate":{"previous":"Anterior","sNext":"Siguiente"} },
        "columnDefs":[{"targets":0,"visible":false,"searchable":false}],
        "processing":true, "serverSide":true, "order":[[1,"asc"]],
        "ajax":{ type:"POST", data:{numservicio: numservicio},
            url:"inc/func_ajax.php/CargatablaIncidenciasXServicio", dataType:'JSON' }
    });

    // Doble click para editar
    $('#tbl_incidencias_srv tbody').off('dblclick').on('dblclick', 'tr', function() {
        var data = $('#tbl_incidencias_srv').DataTable().row(this).data();
        if (data) abrirModalIncidencia(data[0]);
    });
}

function abrirModalIncidencia(numincidencia) {
    // Limpiar
    document.getElementById("txtNumIncidencia").value    = "";
    document.getElementById("txtDestinatarioInc").value  = "";
    document.getElementById("txtEtiquetasInc").value     = "";
    document.getElementById("txtIncidenciaTexto").value  = "";
    document.getElementById("txtHistorialInc").value     = "";
    ['cmbAgenteInc1','cmbAgenteInc2','cmbAgenteInc3','cmbAgenteInc4'].forEach(function(id) {
        CmbSeleccionaValor($('#' + id), '');
    });
    document.getElementById("btnEliminarIncidencia").style.display = "none";
    document.getElementById("tituloModalIncidencia").innerHTML = "<i class='fas fa-plus mr-2'></i>Nueva Incidencia";
    lmodoInc = "alta";

    if (numincidencia) {
        $.ajax({
            type:"POST", data:{id: numincidencia},
            url:"inc/func_ajax.php/CargaIncidencia",
            crossDomain:true, cache:false, async:false, dataType:"json",
            success:function(resultado) {
                var r = resultado[0];
                if (r) {
                    lmodoInc = "edicion";
                    document.getElementById("txtNumIncidencia").value   = r.numincidencia;
                    document.getElementById("txtDestinatarioInc").value  = r.destinatario || '';
                    document.getElementById("txtEtiquetasInc").value    = r.etiquetas_filtro || '';
                    document.getElementById("txtIncidenciaTexto").value = r.incidencias || '';
                    document.getElementById("txtHistorialInc").value    = r.historialincidencias || '';
                    CmbSeleccionaValor($('#cmbAgenteInc1'), r.numagente);
                    CmbSeleccionaValor($('#cmbAgenteInc2'), r.numagente1);
                    CmbSeleccionaValor($('#cmbAgenteInc3'), r.numagente2);
                    CmbSeleccionaValor($('#cmbAgenteInc4'), r.numagente3);
                    document.getElementById("btnEliminarIncidencia").style.display = "inline-block";
                    document.getElementById("tituloModalIncidencia").innerHTML = "<i class='fas fa-edit mr-2'></i>Editar Incidencia #" + r.numincidencia;
                }
            }
        });
    }
    $('#modalIncidencia').modal('show');
}

function GuardarIncidencia() {
    var numservicio = document.getElementById("txtNumServicio").value;
    $.ajax({
        type:"POST", url:"inc/func_ajax.php/ActualizaIncidencia",
        data:{
            lmodo:               lmodoInc,
            id:                  document.getElementById("txtNumIncidencia").value,
            numservicio:         numservicio,
            incidencias:         document.getElementById("txtIncidenciaTexto").value,
            destinatario:        document.getElementById("txtDestinatarioInc").value,
            etiquetas_filtro:    document.getElementById("txtEtiquetasInc").value,
            numagente:           document.getElementById("cmbAgenteInc1").value,
            numagente1:          document.getElementById("cmbAgenteInc2").value,
            numagente2:          document.getElementById("cmbAgenteInc3").value,
            numagente3:          document.getElementById("cmbAgenteInc4").value,
            historialincidencias:document.getElementById("txtHistorialInc").value,
            valor:               ""
        },
        dataType:"json", crossDomain:true, cache:false, async:false,
        success:function(result) {
            if (result.validacion == "ok") {
                $('#modalIncidencia').modal('hide');
                CargaTablaIncidencias();
            } else if (result.validacion == "warning") {
                mostrarToast('warning', result.mensaje);
            } else {
                mostrarToast('error', 'Error al guardar: ' + result.error);
            }
        },
        error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
    });
}

function EliminarIncidencia() {
    var id = document.getElementById("txtNumIncidencia").value;
    Swal.fire({
        title:'¿Eliminar incidencia?', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d',
        cancelButtonText:'Cancelar', confirmButtonText:'Eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type:"POST", url:"inc/func_ajax.php/EliminarIncidencia",
                data:{id: id}, dataType:"json", crossDomain:true, cache:false, async:false,
                success:function(result) {
                    if (result.validacion == "ok") {
                        $('#modalIncidencia').modal('hide');
                        CargaTablaIncidencias();
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
