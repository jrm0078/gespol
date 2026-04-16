	"use strict";

	//logo animado de espera activado
	$(".preloader2").fadeIn();

	//EVENTOS
	// (gestionados por initTablaToolbar após CargaTabla)
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
		"sDom": '<f>t<pl>',
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
		"columnDefs": [ {
			"targets": 0,
			"visible": false,
			"searchable": false
		} ],
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"buttons": [
			{ extend: 'excel',  text: 'Excel'    },
			{ extend: 'csv',    text: 'CSV'      },
			{ extend: 'pdf',    text: 'PDF'      },
			{ extend: 'print',  text: 'Imprimir' },
			{ extend: 'copy',   text: 'Copiar'   }
		],
		"ajax": {
			type: "POST",
			data: {},
			url: "inc/func_ajax.php/CargatablaUsuarios",
			dataType: 'JSON'	
		}
	});	

	// Inicializar toolbar ERP
	initTablaToolbar({
		tableId:   '#zero_config',
		ctxMenuId: '#ctxMenuUsuarios',
		btnAdd:    '#btnTbAddUsuario',
		btnEdit:   '#btnTbEditUsuario',
		getDt:     function () { return $('#zero_config').DataTable(); },
		onAdd:     function () { CargarPagina('fichausuario.php', 'Usuario', 'far fa-user'); },
		onEdit:    function (tr) {
			var data = $('#zero_config').DataTable().row(tr).data();
			if (data) CargarPagina('fichausuario.php', 'Usuario', 'far fa-user', data[0], '', '', '', '');
		}
	});

	// Ocultar el input de búsqueda de la columna oculta (col 0)
	if ($('#zero_config thead tr').length > 1) {
		$('#zero_config thead tr')[1].cells[0].innerHTML = '';
	}
	
}






	
