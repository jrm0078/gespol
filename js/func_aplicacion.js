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

// Al cerrar el modal: limpiar modal y restaurar panel central tal como estaba
$('#modalPagina').one('hidden.bs.modal', function() {
$('#modalPaginaBody').find('table').each(function() {
if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
});
$('#modalPaginaBody').html('');
$('body > [data-ctx-floating]').remove();
$('#panelcentral').show();
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

// Si la pestania ya esta abierta, solo activarla
if (window._gTabs) {
var existingTab = window._gTabs.find(function(t) { return t.pagina === pagina; });
if (existingTab) {
existingTab.titulo = titulo;
existingTab.icono  = icono;
_switchToTab(pagina);
return;
}
}

// Nueva pestania: registrar + crear panel
if (!window._gTabs) window._gTabs = [];
window._gTabs.push({pagina: pagina, titulo: titulo, icono: icono});
var $newPanel = $('<div class="gtab-panel" id="' + _tabPanelId(pagina) + '"></div>');
$('#panelcentral').append($newPanel);
_switchToTab(pagina);

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
// SISTEMA DE PESTAÑAS
////////////////////////////

window._gTabs      = [];   // [{pagina, titulo, icono}]
window._gActiveTab = null;

function _tabPanelId(pagina) {
    return 'gtabp-' + pagina.replace(/[^a-zA-Z0-9]/g, '_');
}

function _renderTabBar() {
    var $bar = $('#gespol-tabbar');
    $bar.empty();
    window._gTabs.forEach(function(tab) {
        var isHome   = (tab.pagina === 'dashboard.php');
        var isActive = (tab.pagina === window._gActiveTab);
        var closeBtn = isHome ? '' : '<span class="gtab-close" data-pagina="' + tab.pagina + '" title="Cerrar">×</span>';
        var $t = $('<div class="gtab' + (isActive ? ' active' : '') + '" data-pagina="' + tab.pagina + '">' +
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

function _switchToTab(pagina) {
    $('#panelcentral > .gtab-panel').hide();
    $('#' + _tabPanelId(pagina)).show();
    window._gActiveTab = pagina;
    var tab = window._gTabs.find(function(t) { return t.pagina === pagina; });
    if (tab) {
        document.getElementById('titulopagina').innerHTML = "<i class='" + tab.icono + "'></i> " + tab.titulo;
    }
    _sidebarMarkActive(pagina);
    _renderTabBar();
}

function _closeTab(pagina) {
    var idx = window._gTabs.findIndex(function(t) { return t.pagina === pagina; });
    if (idx === -1) return;
    var $panel = $('#' + _tabPanelId(pagina));
    $panel.find('table').each(function() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
    });
    $panel.remove();
    window._gTabs.splice(idx, 1);
    if (window._gActiveTab === pagina) {
        var newIdx = Math.max(0, Math.min(idx, window._gTabs.length - 1));
        if (window._gTabs[newIdx]) {
            _switchToTab(window._gTabs[newIdx].pagina);
        }
    } else {
        _renderTabBar();
    }
}

// Eventos de la barra de pestañas
$(document).on('click', '.gtab', function(e) {
    if ($(e.target).hasClass('gtab-close') || $(e.target).closest('.gtab-close').length) return;
    _switchToTab($(this).data('pagina'));
});
$(document).on('click', '.gtab-close', function(e) {
    e.stopPropagation();
    _closeTab($(this).data('pagina'));
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













