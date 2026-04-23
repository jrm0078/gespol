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

	// Mostrar/ocultar contraseña
	document.getElementById("btnMostrarPass").addEventListener('click', function() {
		var input = document.getElementById("txtcontrasenia");
		var icono = document.getElementById("iconoPass");
		if (input.type === "password") {
			input.type = "text";
			icono.className = "fa fa-eye-slash";
		} else {
			input.type = "password";
			icono.className = "fa fa-eye";
		}
	}, false);
	

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

				document.getElementById("tituloFichaUsuario").innerHTML = "<i class='fas fa-user-edit mr-2'></i>Usuario";
				document.getElementById("btnActualizar").innerHTML = "<i class='fa fa-check mr-1'></i> Actualizar";
				document.getElementById("btnEliminar").style.display = "inline-block";
				document.getElementById("txtcontrasenia").placeholder = "Dejar vacío para no cambiar";

			}
			else {
			//SI NO HAY ID ES MODO ALTA
				lmodo="alta";

				document.getElementById("txtnombre").value = "";
				document.getElementById("txtemail").value = "";
				document.getElementById("txtcontrasenia").value = "";
				document.getElementById("txtcontrasenia").placeholder = "Contraseña (obligatoria)";
				document.getElementById("helpContrasenia").textContent = "Introduce una contraseña para el nuevo usuario.";
				CmbSeleccionaValor($("#cmbrol"),"Usuario");

				document.getElementById("tituloFichaUsuario").innerHTML = "<i class='fas fa-user-plus mr-2'></i>Nuevo Usuario";
				document.getElementById("btnActualizar").innerHTML = "<i class='fa fa-check mr-1'></i> Crear";	
				document.getElementById("btnEliminar").style.display = "none";		
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
		success: function (result) {
			if (result.validacion == "ok") {
				var mensaje = lmodo == "alta" ? "Usuario creado correctamente" : "Usuario actualizado correctamente";
				Swal.fire({
				  icon: 'success',
				  title: mensaje,
				  timer: 1500,
				  showConfirmButton: false
				}).then(function() {
					CargarPagina('consusuarios.php','Usuarios','far fa-user');
				});

			} else if (result.validacion=="warning") {
				Swal.fire({
				  icon: 'warning',
				  title: 'Datos incorrectos',
				  html: result.mensaje
				});
				
			} else {
				Swal.fire({
				  icon: 'error',
				  title: 'Error',
				  html: 'Ha ocurrido un error al guardar. ' + result.error
				});
			}
		},
		error: function (result,desc,err) {
			Swal.fire({
			  icon: 'error',
			  title: 'Error inesperado',
			  text: result.statusText
			});	
		}
	});		
}

function Eliminar() {
	
	Swal.fire({
	  title: '¿Eliminar usuario?',
	  text: 'Esta acción no se puede deshacer.',
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#d33',
	  cancelButtonColor: '#6c757d',
	  cancelButtonText: 'Cancelar',
	  confirmButtonText: 'Sí, eliminar'
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
			success: function (result) {
				if (result.validacion == "ok") {
					Swal.fire({
					  icon: 'success',
					  title: 'Usuario eliminado',
					  timer: 1200,
					  showConfirmButton: false
					}).then(function() {
						CargarPagina('consusuarios.php','Usuarios','far fa-user');
					});
					
				} else if (result.validacion=="warning") {
					Swal.fire({
					  icon: 'warning',
					  title: 'Aviso',
					  html: result.mensaje
					});
					
				} else {
					Swal.fire({
					  icon: 'error',
					  title: 'Error',
					  html: 'Ha ocurrido un error. ' + result.error
					});
				}
			},
			error: function (result,desc,err) {
				Swal.fire({
				  icon: 'error',
				  title: 'Error inesperado',
				  text: result.statusText
				});	
			}
		});			

	  }
	});	
}	

