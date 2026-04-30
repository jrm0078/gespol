"use strict";

$(".preloader2").fadeIn();

var pag_id1 = window.localStorage.getItem('pag_id1');
var lmodo   = 'alta';
var _habitanteVinculado = false;

document.getElementById("btnActualizarVehiculo").addEventListener('click', Actualizar, false);
document.getElementById("btnEliminarVehiculo").addEventListener('click',   Eliminar,   false);
document.getElementById("btnAtrasVehiculo").addEventListener('click', function() {
    CargarPagina('consvehiculos.php', 'Vehículos', 'fas fa-car');
}, false);

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    CmbIniciar($('#cmbHabitante'));

    $('#cmbHabitante').on('change', function() {
        var idHab = $(this).val();
        if (idHab && parseInt(idHab) > 0) {
            BuscarHabitante(parseInt(idHab));
        } else {
            DesvinculaTitular();
        }
    });

    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ComboHabitantes",
        crossDomain: true, cache: false, dataType: "json",
        success: function(data) {
            $.each(data, function(i, v) {
                var texto = (v.apel || '') + ' ' + (v.nom || '') + (v.dni ? ' (' + v.dni + ')' : '');
                CmbCargaValor($('#cmbHabitante'), v.idhabitante, texto.trim());
            });
            CargaDatos();
        },
        error: function() { CargaDatos(); }
    });
}

function BuscarHabitante(idHab) {
    $.ajax({
        type: "POST", data: { id: idHab },
        url: "inc/func_ajax.php/CargaHabitante",
        crossDomain: true, cache: false, dataType: "json",
        success: function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                _habitanteVinculado = true;
                document.getElementById("txtDnitit").value     = r.dni    || '';
                document.getElementById("txtApetit").value     = r.apel   || '';
                document.getElementById("txtNomtit").value     = r.nom    || '';
                document.getElementById("txtDomtit").value     = r.calle  || '';
                document.getElementById("txtPobtit").value     = r.pob    || '';
                document.getElementById("txtProvtit").value    = r.prov   || '';
                document.getElementById("txtTft").value        = r.tft    || '';
                document.getElementById("txtEmail").value      = r.email  || '';
                document.getElementById("txtCPostalVeh").value = r.CPostal|| '';
                setCamposReadOnly(true);
                mostrarToast('info', 'Titular: ' + (r.apel || '') + ' ' + (r.nom || ''));
            } else {
                mostrarToast('warning', 'Habitante no encontrado');
                CmbSeleccionaValor($('#cmbHabitante'), '');
                DesvinculaTitular();
            }
        },
        error: function(r) { mostrarToast('error', 'Error al buscar habitante: ' + r.statusText); }
    });
}

function DesvinculaTitular() {
    _habitanteVinculado = false;
    setCamposReadOnly(false);
    ['txtDnitit','txtApetit','txtNomtit','txtDomtit','txtPobtit','txtProvtit','txtTft','txtEmail','txtCPostalVeh'].forEach(function(id) {
        document.getElementById(id).value = '';
    });
}

function setCamposReadOnly(readonly) {
    ['txtDnitit','txtApetit','txtNomtit','txtDomtit','txtPobtit','txtProvtit','txtTft','txtEmail','txtCPostalVeh'].forEach(function(id) {
        var el = document.getElementById(id);
        el.readOnly = readonly;
        if (readonly) el.classList.add('titular-linked');
        else el.classList.remove('titular-linked');
    });
}

function CargaDatos() {
    if (!pag_id1 || pag_id1 === "0" || pag_id1 === "") {
        lmodo = "alta";
        document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Nuevo Vehículo";
        document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
        document.getElementById("btnEliminarVehiculo").style.display = "none";
        document.getElementById("txtIdVehiculo").readOnly = false;
        return;
    }

    $.ajax({
        type: "POST", data: { id: pag_id1 },
        url: "inc/func_ajax.php/CargaVehiculo",
        crossDomain: true, cache: false, dataType: "json",
        success: function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtIdVehiculo").value       = r.idVehiculo      || '';
                document.getElementById("txtMatricula").value        = r.Matricula        || '';
                document.getElementById("txtMarcaModelo").value      = r.marca_modelo     || '';
                document.getElementById("txtClase").value            = r.clase            || '';
                document.getElementById("txtColor").value            = r.color            || '';
                document.getElementById("txtFecmat").value           = r.fecmat           ? r.fecmat.substring(0,10) : '';
                document.getElementById("txtBast").value             = r.bast             || '';
                document.getElementById("txtCia").value              = r.cia              || '';
                document.getElementById("txtPoliza").value           = r.poliza           || '';
                document.getElementById("txtValidezPoliza").value    = r.ValidezPoliza    || '';
                document.getElementById("txtFechaExpPoliza").value   = r.FechaExpPoliza   ? r.FechaExpPoliza.substring(0,10) : '';
                document.getElementById("txtObservaciones").value    = r.Observaciones    || '';
                document.getElementById("txtDnitit").value           = r.dnitit           || '';
                document.getElementById("txtApetit").value           = r.apetit           || '';
                document.getElementById("txtNomtit").value           = r.nomtit           || '';
                document.getElementById("txtDomtit").value           = r.domtit           || '';
                document.getElementById("txtPobtit").value           = r.pobtit           || '';
                document.getElementById("txtProvtit").value          = r.provtit          || '';
                document.getElementById("txtTft").value              = r.tft              || '';
                document.getElementById("txtEmail").value            = r.email            || '';
                document.getElementById("txtCPostalVeh").value       = r.CPostalVeh       || '';

                if (r.idhabitante && parseInt(r.idhabitante) > 0) {
                    _habitanteVinculado = true;
                    CmbSeleccionaValor($('#cmbHabitante'), r.idhabitante);
                    setCamposReadOnly(true);
                }

                document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Vehículo: " + (r.Matricula || r.idVehiculo);
                document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarVehiculo").style.display = "inline-block";
                document.getElementById("txtIdVehiculo").readOnly = true;
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Nuevo Vehículo";
                document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarVehiculo").style.display = "none";
                document.getElementById("txtIdVehiculo").readOnly = false;
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    var idVehiculo = document.getElementById("txtIdVehiculo").value.trim();
    if (!idVehiculo) { mostrarToast('warning', 'El Id Vehículo es obligatorio'); return; }

    var idHab    = $('#cmbHabitante').val();
    var idHabVal = (idHab && parseInt(idHab) > 0) ? idHab : '';

    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ActualizaVehiculo",
        data: {
            lmodo:          lmodo,
            idVehiculo:     idVehiculo,
            Matricula:      document.getElementById("txtMatricula").value,
            marca_modelo:   document.getElementById("txtMarcaModelo").value,
            clase:          document.getElementById("txtClase").value,
            color:          document.getElementById("txtColor").value,
            fecmat:         document.getElementById("txtFecmat").value,
            bast:           document.getElementById("txtBast").value,
            cia:            document.getElementById("txtCia").value,
            poliza:         document.getElementById("txtPoliza").value,
            ValidezPoliza:  document.getElementById("txtValidezPoliza").value,
            FechaExpPoliza: document.getElementById("txtFechaExpPoliza").value,
            idhabitante:    idHabVal,
            dnitit:         document.getElementById("txtDnitit").value,
            apetit:         document.getElementById("txtApetit").value,
            nomtit:         document.getElementById("txtNomtit").value,
            domtit:         document.getElementById("txtDomtit").value,
            pobtit:         document.getElementById("txtPobtit").value,
            provtit:        document.getElementById("txtProvtit").value,
            tft:            document.getElementById("txtTft").value,
            email:          document.getElementById("txtEmail").value,
            CPostalVeh:     document.getElementById("txtCPostalVeh").value,
            Observaciones:  document.getElementById("txtObservaciones").value
        },
        dataType: "json", crossDomain: true, cache: false,
        success: function(result) {
            if (result.validacion === "ok") {
                mostrarToast('success', lmodo === "alta" ? "Vehículo creado" : "Vehículo actualizado");
                if (lmodo === "alta") {
                    window.localStorage.setItem('pag_id1', idVehiculo);
                    pag_id1 = idVehiculo;
                    lmodo = "edicion";
                    document.getElementById("txtIdVehiculo").readOnly = true;
                    document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                    document.getElementById("btnEliminarVehiculo").style.display = "inline-block";
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
        title: '¿Eliminar vehículo?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type: "POST", url: "inc/func_ajax.php/EliminarVehiculo",
                data: { id: document.getElementById("txtIdVehiculo").value },
                dataType: "json", crossDomain: true, cache: false,
                success: function(result) {
                    if (result.validacion === "ok") {
                        mostrarToast('success', 'Vehículo eliminado');
                        CargarPagina('consvehiculos.php', 'Vehículos', 'fas fa-car');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}


// Buscar habitante al perder el foco del campo idHabitante
document.getElementById("txtIdHabitanteFk").addEventListener('change', function() {
    if (this.value && parseInt(this.value) > 0) {
        BuscarHabitante();
    } else {
        DesvinculaTitular();
    }
});

Cargar();
$(".preloader2").fadeOut();

function Cargar() {
    CargaDatos();
}

function BuscarHabitante() {
    var idHab = parseInt(document.getElementById("txtIdHabitanteFk").value);
    if (isNaN(idHab) || idHab <= 0) {
        DesvinculaTitular();
        return;
    }
    $.ajax({
        type: "POST", data: { id: idHab },
        url: "inc/func_ajax.php/CargaHabitante",
        crossDomain: true, cache: false, dataType: "json",
        success: function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                _habitanteVinculado = true;
                document.getElementById("txtDnitit").value   = r.dni  || '';
                document.getElementById("txtApetit").value  = r.apel  || '';
                document.getElementById("txtNomtit").value  = r.nom   || '';
                document.getElementById("txtDomtit").value  = r.calle || '';
                document.getElementById("txtPobtit").value  = r.pob   || '';
                document.getElementById("txtProvtit").value = r.prov  || '';
                document.getElementById("txtTft").value     = r.tft   || '';
                document.getElementById("txtEmail").value   = r.email || '';
                document.getElementById("txtCPostalVeh").value = r.CPostal || '';
                // Marcar campos como solo lectura (vinculado)
                var campos = ['txtDnitit','txtApetit','txtNomtit','txtDomtit','txtPobtit','txtProvtit','txtTft','txtEmail','txtCPostalVeh'];
                campos.forEach(function(id) {
                    document.getElementById(id).readOnly = true;
                    document.getElementById(id).classList.add('titular-linked');
                });
                mostrarToast('info', 'Titular vinculado: ' + (r.apel || '') + ' ' + (r.nom || ''));
            } else {
                mostrarToast('warning', 'No se encontró el habitante con id ' + idHab);
                DesvinculaTitular();
            }
        },
        error: function(r) { mostrarToast('error', 'Error al buscar habitante: ' + r.statusText); }
    });
}

function DesvinculaTitular() {
    _habitanteVinculado = false;
    var campos = ['txtDnitit','txtApetit','txtNomtit','txtDomtit','txtPobtit','txtProvtit','txtTft','txtEmail','txtCPostalVeh'];
    campos.forEach(function(id) {
        document.getElementById(id).readOnly = false;
        document.getElementById(id).classList.remove('titular-linked');
    });
}

function CargaDatos() {
    if (!pag_id1 || pag_id1 === "0" || pag_id1 === "") {
        lmodo = "alta";
        document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Nuevo Vehículo";
        document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
        document.getElementById("btnEliminarVehiculo").style.display = "none";
        document.getElementById("txtIdVehiculo").readOnly = false;
        return;
    }

    $.ajax({
        type: "POST", data: { id: pag_id1 },
        url: "inc/func_ajax.php/CargaVehiculo",
        crossDomain: true, cache: false, dataType: "json",
        success: function(resultado) {
            var r = resultado[0];
            if (r !== undefined) {
                lmodo = "edicion";
                document.getElementById("txtIdVehiculo").value       = r.idVehiculo      || '';
                document.getElementById("txtMatricula").value        = r.Matricula        || '';
                document.getElementById("txtMarcaModelo").value      = r.marca_modelo     || '';
                document.getElementById("txtClase").value            = r.clase            || '';
                document.getElementById("txtColor").value            = r.color            || '';
                document.getElementById("txtFecmat").value           = r.fecmat           ? r.fecmat.substring(0,10) : '';
                document.getElementById("txtBast").value             = r.bast             || '';
                document.getElementById("txtCia").value              = r.cia              || '';
                document.getElementById("txtPoliza").value           = r.poliza           || '';
                document.getElementById("txtValidezPoliza").value    = r.ValidezPoliza    || '';
                document.getElementById("txtFechaExpPoliza").value   = r.FechaExpPoliza   ? r.FechaExpPoliza.substring(0,10) : '';
                document.getElementById("txtObservaciones").value    = r.Observaciones    || '';

                if (r.idhabitante && parseInt(r.idhabitante) > 0) {
                    document.getElementById("txtIdHabitanteFk").value = r.idhabitante;
                    BuscarHabitante();
                } else {
                    document.getElementById("txtIdHabitanteFk").value = '';
                    document.getElementById("txtDnitit").value   = r.dnitit  || '';
                    document.getElementById("txtApetit").value   = r.apetit  || '';
                    document.getElementById("txtNomtit").value   = r.nomtit  || '';
                    document.getElementById("txtDomtit").value   = r.domtit  || '';
                    document.getElementById("txtPobtit").value   = r.pobtit  || '';
                    document.getElementById("txtProvtit").value  = r.provtit || '';
                    document.getElementById("txtTft").value      = r.tft     || '';
                    document.getElementById("txtEmail").value    = r.email   || '';
                    document.getElementById("txtCPostalVeh").value = r.CPostalVeh || '';
                }

                document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Vehículo: " + (r.Matricula || r.idVehiculo);
                document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                document.getElementById("btnEliminarVehiculo").style.display = "inline-block";
                document.getElementById("txtIdVehiculo").readOnly = true;
            } else {
                lmodo = "alta";
                document.getElementById("tituloFichaVehiculo").innerHTML = "<i class='fas fa-car mr-2'></i>Nuevo Vehículo";
                document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";
                document.getElementById("btnEliminarVehiculo").style.display = "none";
                document.getElementById("txtIdVehiculo").readOnly = false;
            }
        },
        error: function(r) { mostrarToast('error', 'Error al cargar: ' + r.statusText); }
    });
}

function Actualizar() {
    var idVehiculo = document.getElementById("txtIdVehiculo").value.trim();
    if (!idVehiculo) { mostrarToast('warning', 'El Id Vehículo es obligatorio'); return; }

    var idHab = document.getElementById("txtIdHabitanteFk").value;
    var idHabVal = (idHab && parseInt(idHab) > 0) ? idHab : '';

    $.ajax({
        type: "POST", url: "inc/func_ajax.php/ActualizaVehiculo",
        data: {
            lmodo:          lmodo,
            idVehiculo:     idVehiculo,
            Matricula:      document.getElementById("txtMatricula").value,
            marca_modelo:   document.getElementById("txtMarcaModelo").value,
            clase:          document.getElementById("txtClase").value,
            color:          document.getElementById("txtColor").value,
            fecmat:         document.getElementById("txtFecmat").value,
            bast:           document.getElementById("txtBast").value,
            cia:            document.getElementById("txtCia").value,
            poliza:         document.getElementById("txtPoliza").value,
            ValidezPoliza:  document.getElementById("txtValidezPoliza").value,
            FechaExpPoliza: document.getElementById("txtFechaExpPoliza").value,
            idhabitante:    idHabVal,
            dnitit:         _habitanteVinculado ? '' : document.getElementById("txtDnitit").value,
            apetit:         _habitanteVinculado ? '' : document.getElementById("txtApetit").value,
            nomtit:         _habitanteVinculado ? '' : document.getElementById("txtNomtit").value,
            domtit:         _habitanteVinculado ? '' : document.getElementById("txtDomtit").value,
            pobtit:         _habitanteVinculado ? '' : document.getElementById("txtPobtit").value,
            provtit:        _habitanteVinculado ? '' : document.getElementById("txtProvtit").value,
            tft:            _habitanteVinculado ? '' : document.getElementById("txtTft").value,
            email:          _habitanteVinculado ? '' : document.getElementById("txtEmail").value,
            CPostalVeh:     _habitanteVinculado ? '' : document.getElementById("txtCPostalVeh").value,
            Observaciones:  document.getElementById("txtObservaciones").value
        },
        dataType: "json", crossDomain: true, cache: false,
        success: function(result) {
            if (result.validacion === "ok") {
                mostrarToast('success', lmodo === "alta" ? "Vehículo creado" : "Vehículo actualizado");
                if (lmodo === "alta") {
                    window.localStorage.setItem('pag_id1', idVehiculo);
                    pag_id1 = idVehiculo;
                    lmodo = "edicion";
                    document.getElementById("txtIdVehiculo").readOnly = true;
                    document.getElementById("btnActualizarVehiculo").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
                    document.getElementById("btnEliminarVehiculo").style.display = "inline-block";
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
        title: '¿Eliminar vehículo?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
        cancelButtonText: 'Cancelar', confirmButtonText: 'Sí, eliminar'
    }).then(function(c) {
        if (c.value) {
            $.ajax({
                type: "POST", url: "inc/func_ajax.php/EliminarVehiculo",
                data: { id: document.getElementById("txtIdVehiculo").value },
                dataType: "json", crossDomain: true, cache: false,
                success: function(result) {
                    if (result.validacion === "ok") {
                        mostrarToast('success', 'Vehículo eliminado');
                        CargarPagina('consvehiculos.php', 'Vehículos', 'fas fa-car');
                    } else {
                        mostrarToast('warning', result.mensaje || result.error);
                    }
                },
                error: function(r) { mostrarToast('error', 'Error inesperado: ' + r.statusText); }
            });
        }
    });
}
