<?php
@session_start();
	
	//Validamos si existe realmente una sesión activa o no 
	if(!isset($_SESSION["validacion"]) || $_SESSION["validacion"] != "ok")
	{ 
		//Si no hay sesión activa, lo direccionamos al login.php (inicio de sesión) 
		echo('<script>' .
				' window.location="/backend_final/login.php"; ' .
			'</script>'); 
		exit(); 
	}	
	else{
		///COMPROBACIONES, POR EJEMPLO EL TOKEN PARA MÁS SEGURIDAD
	}

?>