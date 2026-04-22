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

// AGENTES
$app->post('/CargatablaAgentes',  'CargatablaAgentes');
$app->post('/CargaAgente',        'CargaAgente');
$app->post('/ActualizaAgente',    'ActualizaAgente');
$app->post('/EliminarAgente',     'EliminarAgente');

// ENCARGADOS
$app->post('/CargatablaEncargados', 'CargatablaEncargados');
$app->post('/CargaEncargado',       'CargaEncargado');
$app->post('/ActualizaEncargado',   'ActualizaEncargado');
$app->post('/EliminarEncargado',    'EliminarEncargado');

// SERVICIOS
$app->post('/CargatablaServicios',  'CargatablaServicios');
$app->post('/CargaServicio',        'CargaServicio');
$app->post('/ActualizaServicio',    'ActualizaServicio');
$app->post('/EliminarServicio',     'EliminarServicio');

// INCIDENCIAS POL
$app->post('/CargatablaIncidencias',    'CargatablaIncidencias');
$app->post('/CargatablaIncidenciasXServicio', 'CargatablaIncidenciasXServicio');
$app->post('/CargaIncidencia',          'CargaIncidencia');
$app->post('/ActualizaIncidencia',      'ActualizaIncidencia');
$app->post('/EliminarIncidencia',       'EliminarIncidencia');

// COMBOS AUXILIARES
$app->post('/ComboAgentes',    'ComboAgentes');
$app->post('/ComboEncargados', 'ComboEncargados');

$app->run();





////////////////////////////////////////////////////////////////////////////////////////////////////
//CARGA TABLA (DATATABLES) DE USUARIOS
function CargatablaUsuarios() {
 	
	$table = "usuario";	 
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
	
	$id = intval($_POST["id"]);

	$query = "SELECT id,nombre,email,rol FROM usuario WHERE id = " . $id;
	
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


	$tabla = "usuario";
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

	$id = intval($_POST["id"]);

	$mensajewarning="";

	if ($id === 1) {
		$mensajewarning = $mensajewarning . "No se puede eliminar el usuario Administrador principal<br>";
	}

	if ($mensajewarning!=""){
		echo '{"validacion":"warning","mensaje":"' . $mensajewarning . '"}';
		exit();
	}

	$tabla = "usuario";
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

////////////////////////////////////////////////////////////////////////////////////////////////////
// AGENTES
////////////////////////////////////////////////////////////////////////////////////////////////////
function CargatablaAgentes() {
    $table      = "agentes";
    $primaryKey = "numagente";
    $campos     = "numagente,numagente,nombre,indicativo,activo";
    $tiposcampo = "numero,numero,texto,numero,numero";
    echo CargaTablaPHP($table, $campos, $tiposcampo, $primaryKey, "", "", "");
}

function CargaAgente() {
    $id = intval($_POST["id"]);
    echo select("SELECT numagente, nombre, indicativo, activo FROM agentes WHERE numagente = $id");
}

function ActualizaAgente() {
    $lmodo     = $_POST["lmodo"];
    $id        = intval($_POST["id"]);
    $nombre    = "'" . CadSql($_POST["nombre"])    . "'";
    $indicativo= intval($_POST["indicativo"]);
    $activo    = intval($_POST["activo"]);

    $w = "";
    if (trim($_POST["nombre"]) == "") $w .= "Indicar nombre del agente<br>";
    if ($_POST["id"] == "")           $w .= "Indicar número de agente<br>";
    if ($w != "") { echo '{"validacion":"warning","mensaje":"' . $w . '"}'; exit(); }

    $tabla  = "agentes";
    $campos = "numagente,nombre,indicativo,activo";
    $vals   = "$id#,#$nombre#,#$indicativo#,#$activo";
    $where  = "numagente=$id";

    if ($lmodo == "edicion") echo update($tabla, "nombre,indicativo,activo", "$nombre#,#$indicativo#,#$activo", $where);
    else                     echo insert($tabla, $campos, $vals);
}

function EliminarAgente() {
    $id = intval($_POST["id"]);
    echo delete("agentes", "numagente=$id");
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// ENCARGADOS
////////////////////////////////////////////////////////////////////////////////////////////////////
function CargatablaEncargados() {
    $table      = "encargados";
    $primaryKey = "numencargado";
    $campos     = "numencargado,numencargado,encargado,cargo,estado";
    $tiposcampo = "numero,numero,texto,texto,texto";
    echo CargaTablaPHP($table, $campos, $tiposcampo, $primaryKey, "", "", "");
}

function CargaEncargado() {
    $id = intval($_POST["id"]);
    echo select("SELECT numencargado, encargado, cargo, estado, numagente FROM encargados WHERE numencargado = $id");
}

function ActualizaEncargado() {
    $lmodo       = $_POST["lmodo"];
    $id          = intval($_POST["id"]);
    $encargado   = "'" . CadSql($_POST["encargado"]) . "'";
    $cargo       = "'" . CadSql($_POST["cargo"])      . "'";
    $estado      = "'" . CadSql($_POST["estado"])     . "'";
    $numagente   = $_POST["numagente"] != "" ? intval($_POST["numagente"]) : "NULL";

    $w = "";
    if (trim($_POST["encargado"]) == "") $w .= "Indicar nombre del encargado<br>";
    if ($_POST["id"] == "")              $w .= "Indicar número de encargado<br>";
    if ($w != "") { echo '{"validacion":"warning","mensaje":"' . $w . '"}'; exit(); }

    $tabla  = "encargados";
    $campos = "numencargado,encargado,cargo,estado,numagente";
    $vals   = "$id#,#$encargado#,#$cargo#,#$estado#,#$numagente";
    $where  = "numencargado=$id";

    if ($lmodo == "edicion") echo update($tabla, "encargado,cargo,estado,numagente", "$encargado#,#$cargo#,#$estado#,#$numagente", $where);
    else                     echo insert($tabla, $campos, $vals);
}

function EliminarEncargado() {
    $id = intval($_POST["id"]);
    echo delete("encargados", "numencargado=$id");
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// SERVICIOS
////////////////////////////////////////////////////////////////////////////////////////////////////
function CargatablaServicios() {
    $table      = "servicios";
    $primaryKey = "numservicio";
    $campos     = "numservicio,numservicio,fecha,turno,tipodia,diasemana";
    $tiposcampo = "numero,numero,fecha,texto,texto,texto";
    echo CargaTablaPHP($table, $campos, $tiposcampo, $primaryKey, "", "", "");
}

function CargaServicio() {
    $id = intval($_POST["id"]);
    echo select("SELECT * FROM servicios WHERE numservicio = $id");
}

function ActualizaServicio() {
    $lmodo = $_POST["lmodo"];
    $id    = intval($_POST["id"]);

    $campos_simples = ["fecha","fecha2","turno","tipodia","diasemana","numagenteencargado",
        "numagente","numagente1","numagente2","numagente3","numagente4","numagente5",
        "numagente6","numagente7","numagente8","numagente9","numagente10","numagente11",
        "numagente12","numagente13","numagente14","numagente15",
        "agenteextra","agenteextra1","agenteextra2","agenteextra3","agenteextra4",
        "agenteextra5","agenteextra6","agenteextra7","agenteextra8","agenteextra9",
        "horainicio","horainicio1","horainicio2","horainicio3","horainicio4",
        "horainicio5","horainicio6","horainicio7","horainicio8","horainicio9",
        "horafinal","horafinal1","horafinal2","horafinal3","horafinal4",
        "horafinal5","horafinal6","horafinal7","horafinal8","horafinal9",
        "textoservicioextra","valor"];

    // Validaciones
    $w = "";
    if (empty($_POST["fecha"]))  $w .= "La fecha es obligatoria<br>";
    if (empty($_POST["turno"]))  $w .= "El turno es obligatorio<br>";
    if ($_POST["turno"] == "noche" && empty($_POST["fecha2"])) $w .= "Turno noche requiere Fecha 2<br>";
    if ($w != "") { echo '{"validacion":"warning","mensaje":"' . $w . '"}'; exit(); }

    $cols = [];
    $vals = [];
    foreach ($campos_simples as $c) {
        $v = isset($_POST[$c]) ? trim($_POST[$c]) : "";
        if ($v === "") {
            $cols[] = $c;
            $vals[] = "NULL";
        } else {
            $cols[] = $c;
            $vals[] = "'" . CadSql($v) . "'";
        }
    }

    if ($lmodo == "edicion") {
        $sets = [];
        for ($i = 0; $i < count($cols); $i++) $sets[] = $cols[$i] . "=" . $vals[$i];
        $sql = "UPDATE servicios SET " . implode(",", $sets) . " WHERE numservicio=" . $id;
        $resultado = ejecutarSQL($sql);
        echo $resultado;
    } else {
        $camposStr = implode(",", $cols);
        $valsStr   = implode(",", $vals);
        $sql = "INSERT INTO servicios ($camposStr) VALUES ($valsStr)";
        $sql = CadCompatible($sql);
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $newId = $db->lastInsertId();
            $db = null;
            echo '{"validacion":"ok","error":"","id":' . $newId . '}';
        } catch(PDOException $e) {
            echo '{"validacion":"error","error":"' . $e->getMessage() . '"}';
        }
    }
}

function EliminarServicio() {
    $id = intval($_POST["id"]);
    echo delete("servicios", "numservicio=$id");
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// INCIDENCIAS POL
////////////////////////////////////////////////////////////////////////////////////////////////////
function CargatablaIncidencias() {
    $table      = "incidencias_pol";
    $primaryKey = "numincidencia";
    $campos     = "numincidencia,numincidencia,numservicio,destinatario,etiquetas_filtro";
    $tiposcampo = "numero,numero,numero,texto,texto";
    echo CargaTablaPHP($table, $campos, $tiposcampo, $primaryKey, "", "", "");
}

function CargatablaIncidenciasXServicio() {
    $numservicio = intval($_POST["numservicio"]);
    $table      = "incidencias_pol";
    $primaryKey = "numincidencia";
    $campos     = "numincidencia,numincidencia,destinatario,etiquetas_filtro,numagente";
    $tiposcampo = "numero,numero,texto,texto,numero";
    $extraWhere = "numservicio=$numservicio";
    echo CargaTablaPHP($table, $campos, $tiposcampo, $primaryKey, "", $extraWhere, "");
}

function CargaIncidencia() {
    $id = intval($_POST["id"]);
    echo select("SELECT * FROM incidencias_pol WHERE numincidencia = $id");
}

function ActualizaIncidencia() {
    $lmodo             = $_POST["lmodo"];
    $id                = intval($_POST["id"]);
    $numservicio       = $_POST["numservicio"] != "" ? intval($_POST["numservicio"]) : "NULL";
    $incidencias       = "'" . CadSql($_POST["incidencias"])       . "'";
    $destinatario      = "'" . CadSql($_POST["destinatario"])      . "'";
    $etiquetas_filtro  = "'" . CadSql($_POST["etiquetas_filtro"])  . "'";
    $numagente         = $_POST["numagente"]  != "" ? intval($_POST["numagente"])  : "NULL";
    $numagente1        = $_POST["numagente1"] != "" ? intval($_POST["numagente1"]) : "NULL";
    $numagente2        = $_POST["numagente2"] != "" ? intval($_POST["numagente2"]) : "NULL";
    $numagente3        = $_POST["numagente3"] != "" ? intval($_POST["numagente3"]) : "NULL";
    $historial         = "'" . CadSql($_POST["historialincidencias"]) . "'";
    $valor             = $_POST["valor"] != "" ? intval($_POST["valor"]) : "NULL";

    $tabla  = "incidencias_pol";
    $campos = "numservicio,incidencias,destinatario,etiquetas_filtro,numagente,numagente1,numagente2,numagente3,historialincidencias,valor";
    $vals   = "$numservicio#,#$incidencias#,#$destinatario#,#$etiquetas_filtro#,#$numagente#,#$numagente1#,#$numagente2#,#$numagente3#,#$historial#,#$valor";
    $where  = "numincidencia=$id";

    if ($lmodo == "edicion") {
        echo update($tabla, $campos, $vals, $where);
    } else {
        $camposArr = explode(",", $campos);
        $valsArr   = explode("#,#", $vals);
        $valsStr   = implode(",", $valsArr);
        $sql = "INSERT INTO incidencias_pol ($campos) VALUES ($valsStr)";
        $sql = CadCompatible($sql);
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $newId = $db->lastInsertId();
            $db = null;
            echo '{"validacion":"ok","error":"","id":' . $newId . '}';
        } catch(PDOException $e) {
            echo '{"validacion":"error","error":"' . $e->getMessage() . '"}';
        }
    }
}

function EliminarIncidencia() {
    $id = intval($_POST["id"]);
    echo delete("incidencias_pol", "numincidencia=$id");
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// COMBOS AUXILIARES
////////////////////////////////////////////////////////////////////////////////////////////////////
function ComboAgentes() {
    echo select("SELECT numagente, nombre FROM agentes WHERE activo=1 ORDER BY nombre ASC");
}

function ComboEncargados() {
    echo select("SELECT numencargado, encargado FROM encargados ORDER BY encargado ASC");
}
