	"use strict";

	//logo animado de espera activado
	$(".preloader2").fadeIn();

	//EVENTOS
	document.getElementById("btnActualizar").addEventListener('click', Actualizar, false);
	document.getElementById("btnEliminar").addEventListener('click', Eliminar, false);
	document.getElementById("btnAtras").addEventListener('click', function() {
		CargarPagina('consusuarios.php','Usuarios','far fa-user');
	}, false);		
		
	$('#txtid').on("change", function(e) { txtid_changue(); });		
	
	
	//FIN EVENTOS

	//VARIABLES PÚBLICAS DE LA PÁGINA
	var pag_id1=window.localStorage.getItem('pag_id1');	//id
	var lmodo;

	var Cargando=false;
	//FIN VARIABLES PÚBLICAS DE LA PÁGINA

	Cargar();		
	
	//logo animado de espera quitado
	$(".preloader2").fadeOut();	



function Cargar(){
	
	Cargando=true;

	document.getElementById("txtid").value=pag_id1;

	//CARGA DE COMBOS
	CmbIniciar($('#cmbrol'));
	
	CmbCargaValor($('#cmbrol'),'Superadmin','Superadmin');
	CmbCargaValor($('#cmbrol'),'Admin','Admin');
	CmbCargaValor($('#cmbrol'),'Usuario','Usuario');	
	
	
	Cargando=false;
	
	CargaDatos();
	
}


function CargaDatos(){

	if (Cargando){
		return false;
	}

	//FIN CARGA DE COMBOS

	$.ajax({
		type: "POST",
		data: {
				id: document.getElementById("txtid").value
			  },
		url: "inc/func_ajax.php/CargaUsuario",				
		crossDomain: true,
		cache: false,
		async: false,
		dataType: "json",
		success: function (resultado) {
			//MODO EDICION
			var result=resultado[0];
			if (result!==undefined) { 
				lmodo="edicion";
				document.getElementById("txtid").value = result.id;	
				document.getElementById("txtnombre").value = result.nombre;
				document.getElementById("txtemail").value = result.email;
				document.getElementById("txtcontrasenia").value = "";
				CmbSeleccionaValor($("#cmbrol"),result.rol);

				document.getElementById("btnActualizar").innerHTML = "<i class='fa fa-check'></i> Actualizar";
				document.getElementById("btnEliminar").style.visibility = "visible";
				

			}
			else {
			//SI NO HAY ID ES MODO ALTA
				lmodo="alta";

				document.getElementById("txtnombre").value = "";
				document.getElementById("txtemail").value = "";
				document.getElementById("txtcontrasenia").value = "";
				CmbSeleccionaValor($("#cmbrol"),"Usuario");
				
				document.getElementById("btnActualizar").innerHTML = "<i class='fa fa-check'></i> Crear";	
				document.getElementById("btnEliminar").style.visibility = "hidden";		
			}
		},
		error: function (resultado, desc, err) {
			//ERROR SISTEMA NO ESPERADO
			Swal.fire({
			  type: 'error',
			  title: 'ops!',
			  html: 'Ocurrió un problema inesperado:' + " " + resultado.statusText + "  " + desc + "  " + err
			});	
		}
	});	
	
}




function txtid_changue(){
	CargaDatos();
}



function Actualizar() {
	//agregando evento Ajax INSERT O UPDATE
	$.ajax({
		type: "POST",                 
		url: "inc/func_ajax.php/ActualizaUsuario",
		data: { lmodo : lmodo,
				id : document.getElementById("txtid").value,
				nombre : document.getElementById("txtnombre").value,
				email : document.getElementById("txtemail").value,
				contrasenia : document.getElementById("txtcontrasenia").value,
				rol : document.getElementById("cmbrol").value,
				},
		dataType: "json",
		crossDomain: true,
		cache: false,			
		async: false,		
		success: function (result) {
			if (result.validacion == "ok") {
				
				var mensaje = lmodo == "alta" ? "Usuario Creado" : "Usuario Actualizado";
				Swal.fire({
				  type: 'success',
				  title: 'OK',
				  html: mensaje + '.<br>',
				  didClose: function() {
					// Volver a la lista de usuarios después del cierre del alert
					CargarPagina('consusuarios.php','Usuarios','far fa-user');
				  }
				});

			} else if (result.validacion=="warning") {
				Swal.fire({
				  type: 'warning',
				  title: 'Datos incorrectos',
				  html: result.mensaje
				});
				
			} else {
				Swal.fire({
				  type: 'error',
				  title: 'ops!',
				  html: 'Ha ocurrido un error al actualizar la solución. ' +  result.error
				});
			}
		},
		error: function (result,desc,err) {
			//ERROR SISTEMA NO ESPERADO
			Swal.fire({
			  type: 'error',
			  title: 'ops!',
			  html: "Ocurrió un problema inesperado:" + " " + result.statusText + "  " + desc + "  " + err
			});	
		}
	});		
}

function Eliminar() {
	
	Swal.fire({
	  title: '¿Estás seguro?',
	  html: "El usuario actual va a ser eliminado",
	  type: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  cancelButtonText: 'No, cancelar',
	  confirmButtonText: 'Sí, Eliminar!'
	}).then((contestacion) => {
	  if (contestacion.value) {

		$.ajax({
			type: "POST",    			
			url: "inc/func_ajax.php/EliminarUsuario",	
			data: {
				id : document.getElementById("txtid").value
				},
			dataType: "json",
			crossDomain: true,
			cache: false,
			async: false,
			cache: false,
			success: function (result) {
				if (result.validacion == "ok") {
					Swal.fire({
					  type: 'success',
					  title: 'OK',
					  html: 'Usuario Eliminado.'
					});	
					
					CargaDatos();
					
				} else if (result.validacion=="warning") {
					Swal.fire({
					  type: 'warning',
					  title: 'Datos incorrectos',
					  html: result.mensaje
					});
					
				} else {
					Swal.fire({
					  type: 'error',
					  title: 'ops!',
					  html: 'Ha ocurrido un error. ' +  result.error
					});
				}
			},
			error: function (result,desc,err) {
				//ERROR SISTEMA NO ESPERADO
				Swal.fire({
				  type: 'error',
				  title: 'ops!',
				  html: 'Ocurrió un problema inesperado:' + " " + result.statusText + "  " + desc + "  " + err
				});	
			}
		});			

	  }
	});	
}	

