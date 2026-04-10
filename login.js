"use strict";


//Recuperación de cuenta y usuario grabado al entrar en sesiones anteriores con localstorage
document.getElementById('usuario').value = window.localStorage.getItem('codusuario');

document.getElementById("btnconectar").addEventListener('click', Conectar, false);

$('#cuenta').keyup(function(e){
     if(e.keyCode == 13)
     {
       Conectar();
     }
});

$('#usuario').keyup(function(e){
     if(e.keyCode == 13)
     {
       Conectar();
     }
});

$('#password').keyup(function(e){
     if(e.keyCode == 13)
     {
       Conectar();
     }
});





$('[data-toggle="tooltip"]').tooltip();


function Conectar(){

	$.ajax({
		type: "POST",
		data: { usuario : document.getElementById('usuario').value,
				password : document.getElementById('password').value
			  },
		url: "inc/ajax.php?action=login",
		crossDomain: true,
		cache: false,
		async: false,
		dataType: "json",
		success: function (result) {
			if (result.validacion == "ok") {
				
				//Se guarda valor de cuenta y usuario con localstorage para recuperarlo las siguientes veces que se abra la app
				window.localStorage.setItem('codusuario', document.getElementById('usuario').value);	
				window.location="index.php";
			}
			else {
				Swal.fire({
				  type: 'warning',
				  title: 'Fallo al conectar',
				  html: result.error
				});		
			}
		},
		error: function (result, desc, err) {
			//ERROR SISTEMA NO ESPERADO
			Swal.fire({
			  type: 'error',
			  title: 'ops!',
			  html: 'Ocurrió un problema inesperado:' + " " + result.statusText + "  " + desc + "  " + err
			});	
		}
	});	

}

