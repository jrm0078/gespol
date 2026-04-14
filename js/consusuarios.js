	"use strict";

	//logo animado de espera activado
	$(".preloader2").fadeIn();

	//EVENTOS
	document.getElementById("btnCrear").addEventListener('click', function(){CargarPagina('fichausuario.php','Usuario','far fa-user');}, false);
	$('#zero_config').on( 'click', '#editar', function () {
		var oTable = $('#zero_config').DataTable();
		var data =  oTable.row( $(this).parents('tr') ).data();
		CargarPagina('fichausuario.php','Usuario','far fa-user',data[0],'','','','');
	});
	//FIN EVENTOS

	//VARIABLES PÚBLICAS DE LA PÁGINA

	//FIN VARIABLES PÚBLICAS DE LA PÁGINA

	Cargar();

	//logo animado de espera quitado
	$(".preloader2").fadeOut();


function Cargar(){
	CargaTabla();	
}

function CargaTabla(){
	$('#zero_config').DataTable().destroy();
	
	/****************************************
	 *       Configura y muestra OBJETO TABLA zero_config                   *
	 ****************************************/

    // FILTROS DE LA TABLA
	if ($('#zero_config thead tr').length<2){
		$('#zero_config thead tr').clone(true).appendTo( '#zero_config thead' );		
	}
    $('#zero_config thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Buscar '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );




	// tabla
	var table = $('#zero_config').DataTable(
	{
		"sDom": '<pf>t<pl>',
		"bPaginate": true,
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"searching": true,		
		"pageLength": 50,
		"orderCellsTop": true,
        "fixedHeader": true,
		"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
		"language": {
			"emptytable": "No hay datos disponibles",
			"info": "Mostrando página _PAGE_ de _PAGES_",
			"infoEmpty": "Sin resultados",
			"infoFiltered": "(Filtrando sobre _MAX_ registros)",
			"infoPostFix": "",
			"decimal": "",
			"thousands": ",",
			"lengthMenu": "Mostrar _MENU_ registros",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"sSearchPlaceholder": "",
			"url": "",
			"zeroRecords": "No se han encontrado resultados",	
		   "paginate": {
				"First": "Primero",
				"Last": "Último",
				"previous": "Anterior",
				"sNext": "Siguiente",
		   }
		},
		"columns": [
			null,
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 },
			{ "data": 4 }
		],
		"columnDefs": [ {
			"targets": 0,
			"data": null,
			"width": "0px",
			"defaultContent": "<i id='editar' title='Editar' style='cursor:pointer' class='fa fa-edit'></i>"
		} ],
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],	
		"ajax": {
			type: "POST",
			data: {},
			url: "inc/func_ajax.php/CargatablaUsuarios",
			dataType: 'JSON'	
		}
	});	

	$('#zero_config thead tr')[1].cells[0].childNodes[0].style.display = 'none';			
	
}






	
