"use strict";

//logo animado de espera activado
$(".preloader").fadeIn();

//EVENTOS

	
//FIN EVENTOS

//VARIABLES PÚBLICAS DE LA PÁGINA


//FIN VARIABLES PÚBLICAS DE LA PÁGINA


setInterval(Notificar, 10000);


// Página de inicio comentada (conspedidos.php no existe)
// CargarPagina("conspedidos.php","Pedidos","far fa-sticky-note");




//logo animado de espera quitado
$(".preloader").fadeOut();




function Notificar(){
	// DESHABILITADO: NuevosPedidos y MarcaNotificacion fueron removidos
	// Se puede reactivar cuando se implementen estas funciones
	/*
	
	$.ajax({
		type: "POST",
		data: {
				
			  },
		url: "inc/func_ajax.php/NuevosPedidos",				
		crossDomain: true,
		cache: false,
		async: false,
		dataType: "json",
		success: function (resultado) {
			if (resultado.length>0){
				$("#sonidonotificacion")[0].play();
				document.getElementById("notificacion2").style.display="";
				document.getElementById("notificacion").style.display="none";
				
				$.ajax({
					type: "POST",
					data: {
							
						  },
					url: "inc/func_ajax.php/MarcaNotificacion",				
					crossDomain: true,
					cache: false,
					async: false,
					dataType: "json",
					success: function (resultado) {

					},
					error: function (resultado, desc, err) {					
					}
				});					
				
			} else {
			}
		},
		error: function (resultado, desc, err) {
			
			$("#alerta")[0].play();
			
			//ERROR SISTEMA NO ESPERADO
			Swal.fire({
			  type: 'error',
			  title: 'ALERTA!',
			  html: "SE HA PERDIDO LA CONEXIÓN AL SERVIDOR" 
			});	
		}
	});	
	*/
}	
	
		
