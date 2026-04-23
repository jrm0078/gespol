"use strict";

// Esperar a que el DOM esté completamente disponible
document.addEventListener('DOMContentLoaded', function() {
	console.log('[Login] DOMContentLoaded fired');
	
	try {
		// Recuperar usuario guardado en localStorage
		var usuarioInput = document.getElementById('usuario');
		if (usuarioInput && localStorage.getItem('codusuario')) {
			usuarioInput.value = localStorage.getItem('codusuario');
		}
		
		// Event listeners
		var btnConectar = document.getElementById("btnconectar");
		if (btnConectar) {
			btnConectar.addEventListener('click', Conectar, false);
			console.log('[Login] Botón conectar registrado');
		} else {
			console.warn('[Login] Botón conectar no encontrado');
		}
		
		// Enter key en usuario
		if (usuarioInput) {
			usuarioInput.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') Conectar();
			});
		}
		
		// Enter key en password
		var passInput = document.getElementById('password');
		if (passInput) {
			passInput.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') Conectar();
			});
		}
		
		// Tooltips si existen
		if (typeof jQuery !== 'undefined' && jQuery('[data-toggle="tooltip"]').length) {
			jQuery('[data-toggle="tooltip"]').tooltip();
		}
		
	} catch (error) {
		console.error('[Login] Error en inicialización:', error);
	}
});


function Conectar(){
	console.log('[Login] Conectar() llamado');
	
	var usuario = document.getElementById('usuario').value;
	var password = document.getElementById('password').value;
	
	if (!usuario || !password) {
		if (typeof Swal !== 'undefined') {
			Swal.fire({
				icon: 'warning',
				title: 'Advertencia',
				html: "Ingrese usuario y contraseña"
			});
		}
		return;
	}

	if (typeof jQuery === 'undefined') {
		console.error('[Login] jQuery no está disponible');
		alert('Error: jQuery no cargó correctamente');
		return;
	}

	jQuery.ajax({
		type: "POST",
		data: { usuario: usuario, password: password },
		url: "inc/login.php",
		crossDomain: true,
		cache: false,
		dataType: "json",
		success: function (result) {
			console.log('[Login] Respuesta exitosa:', result);
			if (result.validacion == "ok") {
				localStorage.setItem('codusuario', usuario);
				window.location = "index.php";
			} else {
				console.warn('[Login] Validación no ok:', result);
				if (typeof Swal !== 'undefined') {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						html: result.error || result.mensaje || "Usuario o contraseña incorrectos"
					});
				}
			}
		},
		error: function (resultado, desc, err) {
			console.error('[Login] Error AJAX:', desc, err);
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					html: "Error al conectar con el servidor: " + desc
				});
			}
		}
	});	
}
