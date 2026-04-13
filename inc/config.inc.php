<?php

define ('cdi_servername', 'localhost');
define ('cdi_username','gespol');
define ('cdi_password','');
define ('cdi_dbname','gespol');


function getConnection($database=""){
	
	if ($database==""){
		$dbh = new PDO("mysql:host=".cdi_servername.";port=3306;dbname=".cdi_dbname,cdi_username, cdi_password);
	} else {
		$dbh = new PDO("mysql:host=".cdi_servername.";port=3306;dbname=".$database,cdi_username, cdi_password);
	}
	
	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->exec("SET NAMES 'utf8mb4'"); //utf8mb4 al leer la base de datos
	return $dbh;
}


function getConnection_mysqli($database=""){
	//si pasamos el database conecta a ese si no al definido en este fichero como cdi_dbname
	
	if ($database==""){
		$conn = mysqli_connect(cdi_servername, cdi_username, cdi_password, cdi_dbname, 3306) or die("Connection failed: " . mysqli_connect_error());	
	} else {
		$conn = mysqli_connect(cdi_servername, cdi_username, cdi_password, $database, 3306) or die("Connection failed: " . mysqli_connect_error());	
	}
	
	

	/* cambiar el conjunto de caracteres a utf8 */
	if (!mysqli_set_charset($conn, "utf8mb4")) {
		printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", mysqli_error($conn));
		exit();
	} else {
	//    printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($conn));
	}



	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	return $conn;
}

 
?>
