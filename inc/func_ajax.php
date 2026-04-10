<?php

include 'config.inc.php';
include 'genericasPHP.php';
include 'func_datosPHP.php';
include("seguridad.php");

require '../libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

header("Access-Control-Allow-Origin: *");
header('Content-Type: text/html; charset=utf-8');

$app = new \Slim\Slim();




//CARGAR TABLA USUARIOS
$app->post('/CargatablaUsuarios', 'CargatablaUsuarios');
//CARGAR DATOS DE USUARIO
$app->post('/CargaUsuario', 'CargaUsuario');
//ACTUALIZAR EL USUARIO
$app->post('/ActualizaUsuario', 'ActualizaUsuario');
//ELIMINAR EL USUARIO
$app->post('/EliminarUsuario', 'EliminarUsuario');


//SUBIR IMAGEN
$app->post('/subirimagen', 'subirimagen');


$app->run();





////////////////////////////////////////////////////////////////////////////////////////////////////
//CARGA TABLA (DATATABLES) DE USUARIOS
function CargatablaUsuarios() {
 	
	$table = "usuarios";	 
	$primaryKey = "id";
	$campos = "id,id,nombre,email,rol"; 
	$tiposcampo = "numero,numero,texto,texto,texto";
	$joinQuery = ""; 
	$database = "";
	$extraWhere = "";
	
	
	echo CargaTablaPHP($table,$campos,$tiposcampo,$primaryKey,$joinQuery,$extraWhere,$database);  

}






//DEVUELVE USUARIO PASADO POR POST
function CargaUsuario(){
	
	$where = "";
	$id = $_POST["id"];

	$where = "id = " . $id;
	
	$query = "SELECT " .
				"id,nombre,email,rol " .
			 "FROM " .
				"usuarios " .
			 "WHERE " .
				$where;
	
	echo select($query);
}


//ACTUALIZA USAARIO POR POST CON TODOS SUS PARÁMETROS DE DATOS CAMPOS
//SI HAY CUALQUIER PROBLEMA DEVUELVE CADENA DE TEXTO CON EL ERROR DE ACTUALIZACIÓN
function ActualizaUsuario(){
	
	$lmodo = $_POST["lmodo"];
	$id = CadSql($_POST["id"]);
	$nombre = "'" . CadSql($_POST["nombre"]) . "'";
	$email = "'" . CadSql($_POST["email"]) . "'";
	$contrasenia = "'" . CadSql($_POST["contrasenia"]) . "'";
	$rol = "'" . CadSql($_POST["rol"]) . "'";
	
	$mensajewarning="";


	if ($_POST["nombre"] == "") {
		$mensajewarning = $mensajewarning . "Indicar nombre<br>";
	}
	if ($_POST["email"] == "") {
		$mensajewarning = $mensajewarning . "Indicar email<br>";
	}
	

	if ($mensajewarning!=""){
		echo '{"validacion":"warning","mensaje":"'. $mensajewarning . '"}';
		exit();
	}

	//si es edición y se deja el pass a vacío se hace update del mismo pass que tenía
	if ($_POST["lmodo"]==="edicion" && $_POST["contrasenia"]===""){
		$passEncriptado="contrasenia";
	}else{
		$passEncriptado="'" . EncriptaPass($_POST["contrasenia"]) . "'";
	}


	$tabla = "usuarios";
	$campos = "nombre,email,contrasenia,rol";
	$valores = $nombre . "#,#" . $email . "#,#" . $passEncriptado . "#,#" . $rol;
	$where = "id=" . $id;


	if ($_POST["lmodo"]=="edicion" ) {
		//URL PARA UPDATE
		$resultado = update($tabla,$campos,$valores,$where);
	}
	else {
		//URL PARA INSERT
		$resultado = insert($tabla,$campos,$valores);
	}

	echo $resultado;
	
}


//ELIMINAR USUARIO POR POST CON TODOS SUS PARÁMETROS DE DATOS CAMPOS
//SI HAY CUALQUIER PROBLEMA DEVUELVE CADENA DE TEXTO CON EL ERROR DE ELIMINACIÓN
function EliminarUsuario(){

	$id = CadSql($_POST["id"]);

	$mensajewarning="";

	if ($_POST["id"] == "1") {
		$mensajewarning = $mensajewarning . "No se puede eliminar el usuario Administrador principal<br>";
	}

	if ($mensajewarning!=""){
		echo '{"validacion":"warning","mensaje":"' . $mensajewarning . '"}';
		exit();
	}

	$tabla = "usuarios";
	$where = "id=" . $id;

	echo delete($tabla,$where);
	
}



//FUNCION QUE SUBE FICHERO AL DESTINO QUE SE PASA POR POST
function subirimagen(){
	
	$nombrefichero = Replace(Replace(fechahoraactualPHP(),' ','_'),':','-') . "_" . $_POST["nombrefichero"];

	$fallo=false;

	$directorio = strtolower("../images/elementos/");
	
	
	//SI NO EXISTE EL DIRECTORIO LO CREAMOS
    try {
		if (!is_dir($directorio)){
			if(!mkdir($directorio, 0777, true)) {
				echo('Fallo al crear directorio ' . $directorio);
				exit();
			}
		}
    } catch(PDOException $e) {		
        echo('Fallo al crear directorio ' . $directorio);
		exit();
    }	
	
	//GRABAMOS IMAGEN DEL USUARIO
	$archivo = $_FILES["fichero"];
	if ($archivo!==null){
		$rutanombrearchivo = $directorio . $nombrefichero ;
		if (is_file($rutanombrearchivo)) {	  
			unlink($rutanombrearchivo); // Borrar el fichero
		}	
		if (!move_uploaded_file($archivo["tmp_name"],  $rutanombrearchivo)){
			$fallo=true;
		}
	}
	
	if ($fallo){
		echo '{"validacion":"error","error":"Error"}';
	}else{
		echo '{"validacion":"ok","error":"","ficheroguardado":"' . $rutanombrearchivo . '"}';
	}
	
}
