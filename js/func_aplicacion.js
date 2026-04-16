"use strict";

// Delegated handler: abrir página en modal al pinchar icono de sidebar
$(document).on('click', '.sidebar-modal-icon', function(e) {
	e.stopPropagation();
	CargarPaginaModal($(this).data('pagina'), $(this).data('titulo'), $(this).data('icono'));
});


////////////////////////////
// PAGINAS
////////////////////////////

// Carga página en ventana emergente (modal)
function CargarPaginaModal(pagina, titulo, icono, id1, id2, id3, id4, id5, id6, id7, id8, id9, id10) {

	// Guardar parámetros en localStorage
	window.localStorage.setItem('pag_id1',(id1===undefined? "" : id1));
	window.localStorage.setItem('pag_id2',(id2===undefined? "" : id2));
	window.localStorage.setItem('pag_id3',(id3===undefined? "" : id3));
	window.localStorage.setItem('pag_id4',(id4===undefined? "" : id4));
	window.localStorage.setItem('pag_id5',(id5===undefined? "" : id5));
	window.localStorage.setItem('pag_id6',(id6===undefined? "" : id6));
	window.localStorage.setItem('pag_id7',(id7===undefined? "" : id7));
	window.localStorage.setItem('pag_id8',(id8===undefined? "" : id8));
	window.localStorage.setItem('pag_id9',(id9===undefined? "" : id9));
	window.localStorage.setItem('pag_id10',(id10===undefined? "" : id10));

	// Destruir DataTables del panel central para evitar conflicto de IDs duplicados
	$('#panelcentral').find('table').each(function() {
		if ($.fn.DataTable.isDataTable(this)) {
			$(this).DataTable().destroy();
		}
	});
	$('#panelcentral').html('');

	// Al cerrar el modal: limpiar modal y recargar la página anterior en panel central
	$('#modalPagina').one('hidden.bs.modal', function() {
		$('#modalPaginaBody').find('table').each(function() {
			if ($.fn.DataTable.isDataTable(this)) {
				$(this).DataTable().destroy();
			}
		});
		$('#modalPaginaBody').html('');

		// Limpiar menús contextuales flotantes que pertenecían al modal
		$('body > [data-ctx-floating]').remove();

		// Recargar la página que estaba en el panel central
		var prevPagina = window.localStorage.getItem('pag_pagina_prev');
		var prevTitulo = window.localStorage.getItem('pag_titulo_prev');
		var prevIcono  = window.localStorage.getItem('pag_icono_prev');
		if (prevPagina) {
			CargarPagina(prevPagina, prevTitulo, prevIcono);
		}
	});

	// Título del modal
	$('#modalPaginaTitulo').html("<i class='" + icono + "'></i> " + titulo);
	$('#modalPaginaBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');

	// Mostrar modal
	$('#modalPagina').modal('show');

	// Cargar contenido
	$.ajax({
		url: pagina,
		type: 'GET',
		dataType: 'html',
		cache: false,
		success: function(html) {
			$('#modalPaginaBody').html(html);
			// Mover menús contextuales al body para que no queden cortados por overflow del modal
			$('#modalPaginaBody .ctx-menu').each(function() {
				$(this).attr('data-ctx-floating', '1').appendTo('body');
			});
		},
		error: function() {
			$('#modalPaginaBody').html('<div class="alert alert-danger">Error al cargar la página</div>');
		}
	});
}

//Carga página en panel central (o dentro del modal si está abierto)
function CargarPagina(pagina,titulo,icono,id1,id2,id3,id4,id5,id6,id7,id8,id9,id10){

	// Si el modal está abierto, cargar dentro del modal en vez del panel central
	if ($('#modalPagina').hasClass('show')) {
		$('#modalPaginaTitulo').html("<i class='" + icono + "'></i> " + titulo);
		$('#modalPaginaBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
		$.ajax({
			url: pagina,
			type: 'GET',
			dataType: 'html',
			cache: false,
			success: function(html) {
				$('#modalPaginaBody').find('table').each(function() {
					if ($.fn.DataTable.isDataTable(this)) $(this).DataTable().destroy();
				});
				$('#modalPaginaBody').html(html);
				// Mover menús contextuales al body para que no queden cortados por overflow del modal
				$('#modalPaginaBody .ctx-menu').each(function() {
					$(this).attr('data-ctx-floating', '1').appendTo('body');
				});
			},
			error: function() {
				$('#modalPaginaBody').html('<div class="alert alert-danger">Error al cargar la página</div>');
			}
		});
		return;
	}

	document.getElementById("titulopagina").innerHTML = "<i class='" + icono + "'></i> " + titulo;

	// Guardar página actual para poder restaurarla al cerrar un modal
	window.localStorage.setItem('pag_pagina_prev', pagina);
	window.localStorage.setItem('pag_titulo_prev', titulo);
	window.localStorage.setItem('pag_icono_prev',  icono);

	// Marcar ítem activo en sidebar
	document.querySelectorAll('#sidebarnav .sidebar-item').forEach(function(item) {
		item.classList.remove('active');
		var link = item.querySelector('.sidebar-link');
		if (link) link.classList.remove('active');
	});
	// Buscar el ítem cuyo listener cargará esta página
	var items = document.querySelectorAll('#sidebarnav .sidebar-item');
	items.forEach(function(item) {
		var txt = item.getAttribute('data-pagina');
		if (txt === pagina) {
			item.classList.add('active');
			var link = item.querySelector('.sidebar-link');
			if (link) link.classList.add('active');
		}
	});


	//GUARDAMOS LOS PARÁMETROS DE LLAMADA A LA PANTALLA EN COOKIES	
	window.localStorage.setItem('pag_id1',(id1===undefined? "" : id1));
	window.localStorage.setItem('pag_id2',(id2===undefined? "" : id2));
	window.localStorage.setItem('pag_id3',(id3===undefined? "" : id3));
	window.localStorage.setItem('pag_id4',(id4===undefined? "" : id4));
	window.localStorage.setItem('pag_id5',(id5===undefined? "" : id5));
	window.localStorage.setItem('pag_id6',(id6===undefined? "" : id6));
	window.localStorage.setItem('pag_id7',(id7===undefined? "" : id7));
	window.localStorage.setItem('pag_id8',(id8===undefined? "" : id8));
	window.localStorage.setItem('pag_id9',(id9===undefined? "" : id9));
	window.localStorage.setItem('pag_id10',(id10===undefined? "" : id10));			

	//se carga en el panel central la página que se ha enviado en la url
	$.ajax({
		url: pagina,
		type: 'GET',
		dataType: 'html',  // IMPORTANTE: especificar que es HTML, no script
		cache: false,
		success: function(html) {
			$("#panelcentral").html(html);
			// Re-inicializar componentes después de cargar el HTML
			if (typeof $.fn.dataTable !== 'undefined') {
				$('table').not('.dataTable').each(function() {
					if (!$.fn.DataTable.fnIsDataTable(this)) {
						// Detectamos si es una tabla de datos
					}
				});
			}
		},
		error: function() {
			$("#panelcentral").html('<div class="alert alert-danger">Error al cargar la página</div>');
		}
	});	
}	



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













