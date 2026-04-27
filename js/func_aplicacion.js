"use strict";

// Handler para iconos de sidebar que abren en modal
$(document).on('click', '.sidebar-modal-icon', function(e) {
e.stopPropagation();
CargarPagina($(this).data('pagina'), $(this).data('titulo'), $(this).data('icono'), 'modal');
});


////////////////////////////
// PAGINAS
////////////////////////////

/**
 * Funcion unificada de carga de paginas.
 *
 * Uso:
 *   CargarPagina(pagina, titulo, icono)               -> panel central (por defecto)
 *   CargarPagina(pagina, titulo, icono, 'centro')     -> panel central (explicito)
 *   CargarPagina(pagina, titulo, icono, 'modal')      -> ventana modal
 *   CargarPagina(pagina, titulo, icono, 'tab')        -> nueva pestania del navegador
 *   CargarPagina(pagina, titulo, icono, id1, id2...)  -> panel central + parametros
 *
 * El 4o argumento se interpreta como modo ('centro'|'modal'|'tab') si coincide
 * con uno de esos valores; en otro caso se trata como id1 (panel central).
 */
var _MODOS_PAGINA = ['centro', 'modal', 'tab'];

function CargarPagina(pagina, titulo, icono, id1OrModo, id2, id3, id4, id5, id6, id7, id8, id9, id10) {

// Detectar si el 4o argumento es un modo o un dato
var modo = 'centro';
var id1  = id1OrModo;
if (_MODOS_PAGINA.indexOf(id1OrModo) !== -1) {
modo = id1OrModo;
id1  = '';
}

// Guardar IDs en localStorage
window.localStorage.setItem('pag_id1',  (id1  === undefined ? '' : id1));
window.localStorage.setItem('pag_id2',  (id2  === undefined ? '' : id2));
window.localStorage.setItem('pag_id3',  (id3  === undefined ? '' : id3));
window.localStorage.setItem('pag_id4',  (id4  === undefined ? '' : id4));
window.localStorage.setItem('pag_id5',  (id5  === undefined ? '' : id5));
window.localStorage.setItem('pag_id6',  (id6  === undefined ? '' : id6));
window.localStorage.setItem('pag_id7',  (id7  === undefined ? '' : id7));
window.localStorage.setItem('pag_id8',  (id8  === undefined ? '' : id8));
window.localStorage.setItem('pag_id9',  (id9  === undefined ? '' : id9));
window.localStorage.setItem('pag_id10', (id10 === undefined ? '' : id10));

// ---- TAB: abrir en nueva pestania ----
if (modo === 'tab') {
window.open(pagina, '_blank');
return;
}

// ---- MODAL ----
if (modo === 'modal') {

// Ocultar panel central (sin destruirlo: conserva formularios con datos sin guardar)
$('#panelcentral').hide();

// Si la misma pagina ya esta abierta como pestania, desacoplarla del DOM temporalmente
// para evitar IDs duplicados que impiden que funcionen botones y DataTables del modal
var _detachedTabEl = null;
if (window._gTabs && typeof _tabPanelId === 'function') {
    var _sameTab = window._gTabs.find(function(t) { return t.pagina === pagina; });
    if (_sameTab) {
        _detachedTabEl = $('#' + _tabPanelId(_sameTab.id)).detach();
    }
}

// Al cerrar el modal: destruir DataTables del modal, limpiar, reanotar pestania y mostrar panel
$('#modalPagina').one('hidden.bs.modal', function() {
$('#modalPaginaBody').find('table').each(function() {
if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
});
$('#modalPaginaBody').html('');
$('body > [data-ctx-floating]').remove();
// Reinsertar el panel de la pestania si fue desacoplado
if (_detachedTabEl && _detachedTabEl.length) {
    $('#panelcentral').append(_detachedTabEl);
    _detachedTabEl = null;
}
$('#panelcentral').show();
});

// Ajustar columnas de DataTables tras la animacion del modal (evita columnas con ancho 0)
$('#modalPagina').one('shown.bs.modal', function() {
if ($.fn.DataTable) {
    $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
}
});

// Titulo e indicador de carga
$('#modalPaginaTitulo').html("<i class='" + icono + "'></i> " + titulo);
$('#modalPaginaBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
$('#modalPagina').modal('show');

$.ajax({
url: pagina,
type: 'GET',
dataType: 'html',
cache: false,
success: function(html) {
$('#modalPaginaBody').html(html);
// Mover ctx-menus al body: evita que position:fixed quede cortado por el modal
$('#modalPaginaBody .ctx-menu').each(function() {
$(this).attr('data-ctx-floating', '1').appendTo('body');
});
// Si el modal ya esta visible (AJAX llego tarde), ajustar columnas ahora
if ($('#modalPagina').hasClass('show') && $.fn.DataTable) {
    setTimeout(function() {
        $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
    }, 50);
}
},
error: function() {
$('#modalPaginaBody').html('<div class="alert alert-danger">Error al cargar la pagina</div>');
}
});
return;
}

// Si el modal esta abierto, redirigir ahi en vez del panel central
// (p.ej. cuando una pagina interna llama CargarPagina sin modo al guardar o cancelar)
if ($('#modalPagina').hasClass('show')) {
$('#modalPaginaTitulo').html("<i class='" + icono + "'></i> " + titulo);
$('#modalPaginaBody').find('table').each(function() {
if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
});
$('#modalPaginaBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
$.ajax({
url: pagina,
type: 'GET',
dataType: 'html',
cache: false,
success: function(html) {
$('#modalPaginaBody').html(html);
$('#modalPaginaBody .ctx-menu').each(function() {
$(this).attr('data-ctx-floating', '1').appendTo('body');
});
},
error: function() {
$('#modalPaginaBody').html('<div class="alert alert-danger">Error al cargar la pagina</div>');
}
});
return;
}

// ---- CENTRO (por defecto) ----

// Guardar pagina actual en localStorage
window.localStorage.setItem('pag_pagina_actual', pagina);
window.localStorage.setItem('pag_titulo_actual', titulo);
window.localStorage.setItem('pag_icono_actual',  icono);
window.localStorage.setItem('pag_pagina_prev',   pagina);
window.localStorage.setItem('pag_titulo_prev',   titulo);
window.localStorage.setItem('pag_icono_prev',    icono);

// Nueva pestania: siempre nueva, aunque ya exista una con la misma pagina (punto #3)
if (!window._gTabs) window._gTabs = [];
var _newTabId = ++window._gTabIdSeq;
window._gTabs.push({id: _newTabId, pagina: pagina, titulo: titulo, icono: icono});
var $newPanel = $('<div class="gtab-panel" id="' + _tabPanelId(_newTabId) + '"></div>');
$('#panelcentral').append($newPanel);
_switchToTab(_newTabId);

// Cargar contenido en el panel de la pestania
$newPanel.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
$.ajax({
url: pagina,
type: 'GET',
dataType: 'html',
cache: false,
success: function(html) {
$newPanel.html(html);
},
error: function() {
$newPanel.html('<div class="alert alert-danger">Error al cargar la pagina</div>');
}
});
}

// Alias para compatibilidad con codigo antiguo
function CargarPaginaModal(pagina, titulo, icono) {
CargarPagina(pagina, titulo, icono, 'modal');
}

////////////////////////////
// TOAST DE NOTIFICACIONES
////////////////////////////

// Mixin SweetAlert2 no bloqueante (toast esquina superior derecha)
var _SwalToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2800,
    timerProgressBar: true,
    didOpen: function(toast) {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function mostrarToast(tipo, mensaje) {
    _SwalToast.fire({ icon: tipo, title: mensaje });
}

////////////////////////////
// RECARGA DE PESTAÑA
////////////////////////////

// Recarga el contenido de una pestaña ya abierta y la activa.
// Si la pestaña no existe, la crea mediante CargarPagina normal.
function _recargarTab(pagina, titulo, icono) {
    if (window._gTabs) {
        var existingTab = window._gTabs.find(function(t) { return t.pagina === pagina; });
        if (existingTab) {
            existingTab.titulo = titulo;
            existingTab.icono  = icono;
            var $panel = $('#' + _tabPanelId(existingTab.id));
            $panel.find('table').each(function() {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
            });
            $panel.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
            $.ajax({
                url: pagina, type: 'GET', dataType: 'html', cache: false,
                success: function(html) { $panel.html(html); },
                error:   function()     { $panel.html('<div class="alert alert-danger m-3">Error al cargar la pagina</div>'); }
            });
            _switchToTab(existingTab.id);
            return;
        }
    }
    CargarPagina(pagina, titulo, icono);
}

////////////////////////////
// SISTEMA DE PESTAÑAS
////////////////////////////

window._gTabs      = [];   // [{id, pagina, titulo, icono}]
window._gTabIdSeq  = 0;    // contador de IDs unicos de pestana
window._gActiveTab = null; // id numerico de la pestana activa

function _tabPanelId(tabId) {
    return 'gtabp-' + tabId;
}

function _renderTabBar() {
    var $bar = $('#gespol-tabbar');
    $bar.empty();
    window._gTabs.forEach(function(tab) {
        var isHome   = (tab.pagina === 'dashboard.php');
        var isActive = (tab.id === window._gActiveTab);
        var closeBtn = isHome ? '' : '<span class="gtab-close" data-tabid="' + tab.id + '" title="Cerrar">×</span>';
        var $t = $('<div class="gtab' + (isActive ? ' active' : '') + '" data-tabid="' + tab.id + '">' +
            '<i class="' + tab.icono + ' gtab-icon"></i>' +
            '<span class="gtab-title">' + $('<span>').text(tab.titulo).html() + '</span>' +
            closeBtn + '</div>');
        $bar.append($t);
    });
}

function _sidebarMarkActive(pagina) {
    document.querySelectorAll('#sidebarnav .sidebar-item').forEach(function(item) {
        item.classList.remove('active');
        var lnk = item.querySelector('.sidebar-link');
        if (lnk) lnk.classList.remove('active');
        if (item.getAttribute('data-pagina') === pagina) {
            item.classList.add('active');
            if (lnk) lnk.classList.add('active');
        }
    });
}

function _switchToTab(tabId) {
    $('#panelcentral > .gtab-panel').hide();
    $('#' + _tabPanelId(tabId)).show();
    window._gActiveTab = tabId;
    var tab = window._gTabs.find(function(t) { return t.id === tabId; });
    if (tab) {
        document.getElementById('titulopagina').innerHTML = "<i class='" + tab.icono + "'></i> " + tab.titulo;
        _sidebarMarkActive(tab.pagina);
    }
    _renderTabBar();
}

function _closeTab(tabId) {
    var idx = window._gTabs.findIndex(function(t) { return t.id === tabId; });
    if (idx === -1) return;
    var $panel = $('#' + _tabPanelId(tabId));
    $panel.find('table').each(function() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
    });
    $panel.remove();
    window._gTabs.splice(idx, 1);
    if (window._gActiveTab === tabId) {
        var newIdx = Math.max(0, Math.min(idx, window._gTabs.length - 1));
        if (window._gTabs[newIdx]) {
            _switchToTab(window._gTabs[newIdx].id);
        }
    } else {
        _renderTabBar();
    }
}

// Eventos de la barra de pestañas
$(document).on('click', '.gtab', function(e) {
    if ($(e.target).hasClass('gtab-close') || $(e.target).closest('.gtab-close').length) return;
    _switchToTab($(this).data('tabid'));
});
$(document).on('click', '.gtab-close', function(e) {
    e.stopPropagation();
    _closeTab($(this).data('tabid'));
});


////////////////////////////
// COMBOS (SELECT2)
////////////////////////////

//Iniciar Combo, para que funcione siempre hay que iniciarlo al menos una vez
function CmbIniciar(Combo){
	Combo.select2();		
}

//cargar un valor (nodo) en el combo
function CmbCargaValor(Combo,valor,texto){
	var newOption = new Option(texto,valor, false, false);
	Combo.append(newOption);		
}

//seleccionar un valor en el combo
function CmbSeleccionaValor(Combo,valor){
	Combo.val(valor);
	Combo.trigger('change'); //llama al evento change y asume y visualiza los cambios
}

//seleccionar un valor en el combo SIN LLAMAR A CHANGE
function CmbSeleccionaValorSinEventoChange(Combo,valor){
	Combo.val(valor).trigger ('change.select2');
}

//seleccionar un valor en el combo
function CmbEnabled(Combo,trueofalse){
	Combo.prop("disabled", !trueofalse);
}

//seleccionar un valor en el combo
function CmbVisible(Combo,trueofalse){
	if(trueofalse){
		Combo[0].parentElement.style.display='block';				
	} else {
		Combo[0].parentElement.style.display='none';				
	}
}




////////////////////////////
// IMG (IMAGE)
////////////////////////////

//Visualiza en un objeto imágen el seleccionado en un objeto file
function ImgVisualizaDeObjetoFile(ObjImg,ObjFile){
	  // Creamos el objeto de la clase FileReader
	  let reader = new FileReader();

	  // Leemos el archivo subido y se lo pasamos a nuestro fileReader
	  reader.readAsDataURL(ObjFile.files[0]);

	  // Le decimos que cuando este listo ejecute el código interno
	  reader.onload = function(){
		ObjImg.src = reader.result;
	  };		
}

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////


///////////////////////////////
// FILE (FICHEROS SELECCIONADOS)
//////////////////////////////////



//poner visible o invisible un file
function FleVisible(inputFile,trueofalse){
	if(trueofalse){
		inputFile.parentElement.style.display='block';				
	} else {
		inputFile.parentElement.style.display='none';				
	}
}	



/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////	













