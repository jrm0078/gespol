<?php

// ESTE PHP NO REQUIERE DE LLAMAR A seguridad.php YA QUE NO HAY SESIÓN INICIADA QUE VALIDAR
// PARA ESTAS FUNCIONES, EL USUARIO NO ESTARÁ LOGEADO

include 'config.inc.php';
include 'genericasPHP.php';

require 'libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

header("Access-Control-Allow-Origin: *");
header('Content-Type: text/html; charset=utf-8');

$app = new \Slim\Slim();

//LOGIN DE USUARIO PANEL
$app->post('/login','login');
//Cerrar sesión 
$app->get('/cerrarsesion','cerrarsesion');


//Cerrar sesión 
$app->get('/cerrarsesioncliente','cerrarsesioncliente');



$app->run();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////// FUNCIONES PARA EJECUTAR SIN HABER INICIADO UNA SESIÓN EN EL SERVIDOR ////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////


function login() {
	
	$sql = "SELECT id,nombre,email,contrasenia,rol" .
			" FROM usuario " .
			"WHERE email='" . $_POST['usuario'] . "'";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$resultado = $stmt->fetch(PDO::FETCH_OBJ);
		$db = null;
		
		
		/* verifica que el usuario y password concuerden correctamente */
	
		if(  !empty($resultado) ){

			if (!password_verify($_POST['password'],$resultado->contrasenia)){
				/* esta informacion se envia si la validacion falla */		
				echo '{"validacion":"error","error":"Contraseña incorrecta"}'; 	
			}	
			else{
				/*esta informacion se envia solo si la validacion es correcta */
				session_start(); 
				//Guardamos dos variables de sesión que nos auxiliará para saber si se está o no "logueado" un usuario 
				$_SESSION["validacion"] = "ok"; 
				$_SESSION["user_codigo"] = $resultado->id;
				$_SESSION["user_descripcion"] = $resultado->nombre;
				$_SESSION["user_email"] = $resultado->email;
				$_SESSION["user_rol"] = $resultado->rol;


				echo '{"validacion":"ok"}';
				
			}
		}else{
			/* esta informacion se envia si la validacion falla */
			echo '{"validacion":"error","error":"Cuenta o usuario inexistente."}'; 
		}
    } catch(PDOException $e) {
        echo '{"validacion":"error","error":"'. $e->getMessage() .'"}';
	}
	
}

	
function cerrarsesion() {
	//Reanudamos la sesión 
	session_start(); 
	//Literalmente la destruimos 
	$_SESSION["validacion"] = "ko"; 
	//Redireccionamos a index.php (al inicio de sesión) 
	header("Location: /backend_final/login.php");  
}

function cerrarsesioncliente() {
	//Reanudamos la sesión 
	session_start(); 
	//Literalmente la destruimos 
	$_SESSION["validacioncliente"] = "ko"; 
	//Redireccionamos a index.php (al inicio de sesión) 
	header("Location: /backend_final/index.php");	

}







?>