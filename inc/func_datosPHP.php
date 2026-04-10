<?php

//FUNCIÓN PARA CREAR BASE DE DATOS
function crearbdPHP($nombre){

	// Creamos conexión
	$conn = getConnection_mysqli();
	
	// Checkeamos conection
	if (mysqli_connect_errno()) {
	  return '{"validacion":"error","error":"Error de conexión -> '. mysqli_connect_error() .'"}';	  
	  exit;
	}	
	

	// Create database
	$sql = "CREATE DATABASE " . $nombre . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
	if ($conn->query($sql) === TRUE) {
		return '{"validacion":"ok","error":""}';
	} else {
		return '{"validacion":"error","error":"'. $conn->error .'"}';
	}

	$conn->close();	
}

//COMPROBAR SI EXISTE BASE DE DATOS
//DEVUELVE TEXTO CON "true" o "false", si da error devuelve JSON con error
function existebdPHP($nombre){
	// Creamos conexión
	$conn = getConnection_mysqli();

	// Checkeamos conection
	if (mysqli_connect_errno()) {
		return '{"validacion":"error","error":"Error de conexión -> '. mysqli_connect_error() .'"}';	  
		exit();
	}	
	
	// Cambiamos base de datos a la que queremos comprobar si existe
	mysqli_select_db($conn, $nombre);

	// comprobamos si la base seleccionada coincide con el nombre pasado
	if ($result = mysqli_query($conn, "select SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $nombre . "'")) {	
		$row = mysqli_fetch_row($result);
		if($row[0]==$nombre){
			$result->close();
			return "true";	 			
		}else{
			return "false";
		}
	}
}



//CARGAR TABLA (DATATABLES) LLAMANDO DESDE UN PHP, NO DESDE AJAX
////////////////////////////////////////////////////////////////////////////////////////////////////
function cargatablaPHP($table,$listacampos,$listatiposcampo,$primaryKey,$joinQuery,$extraWhere,$database) {
	 
	//CONSTRUIR ARRAY CON LOS CAMPOS SEGÚN SUS TIPOS 
	$campos=explode(",",$listacampos); 
	$tiposcampo=explode(",",$listatiposcampo);  

	$columns=array();
	//Recorremos todos los campos del array $campos obteniendo el indice $i para tomar los tiposcampo
	for($i=0; $i<count($campos); $i++)
	  {
		if (strrpos (  $campos[$i], " as ")!=false){
			$alias= substr($campos[$i],strrpos ($campos[$i], " as ")+4);
			$campos[$i]=substr($campos[$i],0,strrpos ($campos[$i], " as "));
		 }
		elseif (strrpos (  $campos[$i], " AS ")!=false) {
			$alias= substr($campos[$i],strrpos ($campos[$i], " AS ")+4);
			$campos[$i]=substr($campos[$i],0,strrpos ($campos[$i], " AS "));
			
		}
		else{
			 $alias= $campos[$i];
		}
			  
		  
		  
		//según el tipo de campo  
		//creamos subarray de cada campo
		switch ($tiposcampo[$i]) {
			case "fecha":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias,
							'formatter' => function( $d, $row ) {
								return $d===null? "" : date('d-m-Y', strtotime($d));
							})
						);
				break;
			case "fechahora":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias,
							'formatter' => function( $d, $row ) {
								return $d===null? "" : date('d-m-Y H:i:s', strtotime($d));
							})
						);
				break;
			case "texto":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias));
				break;
			case "numero":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias));
				break;
			case "decimal":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias));
				break;				
			case "icono":
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias,
							'formatter' => function( $d, $row ) {
								return $d===null? "" : "<i class='" . $d . "' style='font-size: 200%;'></i>";
							})
						);
				break;			
			default:
				array_push($columns,array( 'db' => $campos[$i], 'dt' => $i, 'field' => $campos[$i], 'as' => $alias));
				break;
		}	  
	  }

	// SQL server connection information
	$sql_details = array(
		'user' => cdi_username,
		'pass' => cdi_password,
		'db'   =>  $database==""?  cdi_dbname : $database,
		'host' => cdi_servername
	);
	require('datatables/ssp.class.php');

	return json_encode(
		SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
	);
}



/////////////////////////////////////////////////////////////////////////////////////////////////////
///FUNCIÓN QUE INSERTA UN REGISTRO SEGÚN LOS PARÁMETROS TABLA Y CAMPOS Y VALORES
///QUE SE RECOGEN POR MÉTODO POST
function insert($tabla,$campos,$valores,$database="") {
	
    $sql = "INSERT INTO " . $tabla . " (". $campos . ") VALUES 
			(" . str_replace("#,#",",",$valores) . ")";
	$sql=CadCompatible($sql);
	
    try {
        $db = getConnection($database);
        $stmt = $db->prepare($sql);
		//ejecutamos la sentencia
        $stmt->execute();
        $db = null;
		
		return '{"validacion":"ok","error":""}';
        
		
    } catch(PDOException $e) {
		if  (strpos($e->getMessage(), "[23000]: Integrity constraint violation: 1062")!=false){
			//YA EXISTE REGISTRO, NO SE PUEDE INSERTAR
			return '{"validacion":"error","error":"</br>Clave duplicada. ' . 
				'</br>Este registro ya existe, no se puede volver a crear."}';
		}else{
			return '{"validacion":"error","error":"'. $e->getMessage()  . '</br>' . $sql . '"}';
		}					
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
///FUNCIÓN QUE ACTUALIZA UN REGISTRO SEGÚN LOS PARÁMETROS TABLA Y CAMPOS Y VALORES
///QUE SE RECOGEN POR MÉTODO POST
function update($tabla,$campos,$valores,$where,$database="") {


	$campos=explode(",",$campos);	
	//LOS VALORES SE HAN DE ENVIAR CON SEPARADOR #,# EN LUGAR DE COMA NORMAL POR COMPATIBILIDAD
	$valores=explode("#,#",$valores);	

	$set="";	
	for($i=0; $i<count($campos); $i++)
	  {
		  $set=$set . $campos[$i] . "=" . $valores[$i] . ",";
	  }
	//quitamos la última coma de la cadena
	$set=substr($set,0,-1);
	
    $sql = "UPDATE " . $tabla . " SET " . $set .
			" WHERE ". $where;		
	$sql=CadCompatible($sql);
		
	try {
        $db = getConnection($database);
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
		
		return '{"validacion":"ok","error":""}';
        
		
    } catch(PDOException $e) {
		if  (strpos($e->getMessage(), "[23000]: Integrity constraint violation: 1062")!=false){
			//YA EXISTE REGISTRO, NO SE PUEDE INSERTAR
			return '{"validacion":"error","error":"</br>El registro ya existe. ' . 
				'</br>No se puede usar una clave ya existente."}';
		}
		else{
			return '{"validacion":"error","error":"'. $e->getMessage()  . '</br>' . $sql . '"}';
		}	
    }
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
///FUNCIÓN QUE ELIMINA REGISTROS SEGÚN LOS PARÁMETROS TABLA Y WHERE
///QUE SE RECOGEN POR MÉTODO POST
function delete($tabla,$where,$database="") {
	$sql = "DELETE FROM " . $tabla . " where " . $where;
	$sql=CadCompatible($sql);
	
    try {
		$db = getConnection($database);
		$stmt = $db->prepare($sql);
		$stmt->execute();

		return '{"validacion":"ok","error":""}';

	} catch(PDOException $e) {
		if  (strpos($e->getMessage(), "[23000]: Integrity constraint violation: 1451")!=false){
			//NO SE PUEDE ELIMINAR ESTE REGISTRO, PARTICIPA EN OTRAS TABLAS
			return '{"validacion":"error","error":"</br>Clave foránea. ' . 
				'</br>No se puede eliminar este registro, está participando en otras tablas."}';
		}else{
			return '{"validacion":"error","error":"'. $e->getMessage()  . '</br>' . $sql . '"}';
		}					
	}
}


//SELECT QUE DEVUELVE EL RESULTADO EN UN JSON PARA PASAR DIRÉCTAMENTE AL CLIENTE JS
function select($query,$database=""){

	$sql = CadCompatible($query);
	
    try {
		$db = getConnection($database);
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$db = null;
		
		return json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		
	} catch(PDOException $e) {
        return '{"error":"'. $e->getMessage()  . '</br>' . $sql . '"}';
	}
}


//SELECT QUE DEVUELVE EL RESULTADO EN ARRAY PARA PROCESAR EN PHP
//CASO CONTRARIO DEVUELVE UN JSON CON EL ERROR PARA PASAR A JS
function selectPHP($query,$database=""){

	$sql = CadCompatible($query);
	
    try {
		$db = getConnection($database);
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$db = null;
		
		return $resultado;
		
		
	} catch(PDOException $e) {
        return '{"error":"'. $e->getMessage()  . '</br>' . $sql . '"}';  
	}
}


//SENTENCIA INSERT O UPDATE QUE DEVUELVE 'OK' SI HA IDO BIEN, SI NO DEVUELVE ERROR EN JSON
function ejecutaqueryPHP($query,$database=""){

	$sql = CadCompatible($query);
	
	try {
        $db = getConnection($database);
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
		
		return 'OK';
        
		
    } catch(PDOException $e) {
        return '{"validacion":"error","error":"'. $e->getMessage() . '</br>' . $sql . '"}';
    }
}



//FUNCION QUE PREPARA UN TEXTO CAPTURADO POR ENTRADA DE UN USUARIO PARA SER ENVIADO LUEGO A UN INSERT, UPDATE, ETC por ajax
//CONVIRTIENDO EL CONTENIDO COMO POR EJEMPLO ' POR apostrofe$$$ PARA QUE POSTERIORMENTE
//AL PROCESARLO EN EL PHP LO VUELVA A PONER COMO COMILLA SIMPLE CON LA FUNCIÓN  CadCompatible
function CadSql($Cadena){
	$Result = $Cadena;
	
	$Result = Replace($Result,"'","apostrofe$$$"); 
	$Result = Replace($Result,"\\\\","barrainvertida$$$");
	
	return $Result;
}


////////////////////////////////////////////////////////////////////////////////
//CADCOMPATIBLE CONVIERTE VARIABLES TIPO cdivar_????? A SQL
//Ejemplo: cdivar_horaserver es CAST(NOW() AS TIME)
function CadCompatible($cadena){
	$cadenafin=$cadena;
	$cadenafin=str_replace("horaserver$$$","CAST(NOW() AS TIME)",$cadenafin);
	$cadenafin=str_replace("fechaserver$$$","CAST(NOW() AS DATE)",$cadenafin);
	$cadenafin=str_replace("apostrofe$$$","\'",$cadenafin);
	$cadenafin=str_replace("barrainvertida$$$","\\\\",$cadenafin);	
	return $cadenafin;
}

