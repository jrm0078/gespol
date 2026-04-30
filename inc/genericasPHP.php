<?php


/////////////////////////////////////////////////////////////////////////////////////////
///////////////////        FUNCIONES SIN LLAMADA DE SLIM, incluidas para llamarlas en func_aplicacion.php,func_datos.php y genericas.php      //////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////







//COMPROBAR SI EL IBAN PASADO ES CORRECTO
function fn_ValidateIBAN($iban)
{
    $iban = strtolower(str_replace(' ','',$iban));
    $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
    $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);

    if(strlen($iban) == $Countries[substr($iban,0,2)]){

        $MovedChar = substr($iban, 4).substr($iban,0,4);
        $MovedCharArray = str_split($MovedChar);
        $NewString = "";

        foreach($MovedCharArray AS $key => $value){
            if(!is_numeric($MovedCharArray[$key])){
                $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
            }
            $NewString .= $MovedCharArray[$key];
        }

        if(bcmod($NewString, '97') == 1)
        {
            return true;
        }
    }
    return false;
}

//Función que valida email, devuelve true si es correcto y false si no lo es
function ValidarEmail($email){

    $mail_correcto = 0;
    //compruebo unas cosas primeras
    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
       if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
          //miro si tiene caracter .
          if (substr_count($email,".")>= 1){
             //obtengo la terminacion del dominio
             $term_dom = substr(strrchr ($email, '.'),1);
             //compruebo que la terminación del dominio sea correcta
             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                //compruebo que lo de antes del dominio sea correcto
                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                if ($caracter_ult != "@" && $caracter_ult != "."){
                   $mail_correcto = 1;
                }
             }
          }
       }
    }
    if ($mail_correcto)
       return true;
    else
       return false; 

}



/////////////////////////////////////////////////////////////////////////////////////////////////////
///DEVUELVE LA FECHA ACTUAL FORMATO AAAA-MM-DD
function fechaactualPHP() {
	date_default_timezone_set("Europe/Madrid");
	return date("Y-m-d");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
///DEVUELVE LA FECHA-HORA ACTUAL EN FORMATO MYSQL AAAA-MM-DD HH:MM:SS
function fechahoraactualPHP() {
	date_default_timezone_set("Europe/Madrid");	
	return date("Y-m-d H:i:s");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
///DEVUELVE LA HORA ACTUAL EN FORMATO MYSQL HH:MM:SS
function horaactual() {
	date_default_timezone_set("Europe/Madrid");	
	return date("H:i:s");
}



// Función que devuelve la cadena pasada como argumento, ajustada a la
// derecha/izquierda según el parámetro y rellena con 0 o caracter indicado
function zero($pCadena, $nLon, $cLeftRight=null, $cCaracter=null){
	
	$LeftRight = "";
	$Caracter = "";
	$Cadena = CStr($pCadena);
	
	if ($cLeftRight!==null){
		$LeftRight = $cLeftRight;
	}
	
	if ($cCaracter!==null){
		$Caracter = $cCaracter;
	}
	
	$LeftRight = ($LeftRight=="" ? "L" :  $LeftRight);
	$Caracter = ($Caracter=="" ?  "0" : $Caracter);
	$Lon = $nLon;
	if (Len($Cadena) > $Lon){
		$aux = $Cadena;
	}
	else{
		if ($LeftRight == "L"){
			$aux = str_repeat($Caracter,$Lon - Len($Cadena)) . $Cadena;
		}
		else{
			$aux = $Cadena . str_repeat($Caracter,$Lon - Len($Cadena));
		}
	}
	
	return $aux;
}


//Función que devuelve el valor del parámetro Numero redondeado
//según el factor de redondeo Ej. 0,1 redondea a un decimal, 10 a la decena,..
//AoB = Alza o Baja -> "A" o "B"
function Redondea($Numero, $dec=1, $AoB=null){
	$EsNegativo = false;
	$CadNum = "";
	$CadTmp = "";
	$sufijo = "";
	$PosPunto = 0;
	$Redondea=$Numero;
	$num=$Numero;
	$dec2=1;
	
	
	try {
		if (Left(CStr($Numero), 1) === "-"){
			$num = $Numero * -1;
			$EsNegativo = true;
		} else {
			$EsNegativo = false;
		}
		
		$dec2 = $dec;
		$CadNum = CStr($num / $dec2);
						
		if (InStr(1, $CadNum, ".")) {		//SI NO ES DIVISIBLE POR EL FACTOR (RESULTADO NO ES MULTIPLO EXACTO)
			if (InStr(1, $CadNum, "E") !== 0) {     //para numeros con coma flotante
				$sufijo = Mid($CadNum, InStr(1, $CadNum, "E"));
				$CadNum = Left($CadNum, InStr(1, $CadNum, "E") - 1);
				$CadTmp = "";
				if (Left($CadNum, 1) === "+" || Left($CadNum, 1) === "-"){
					$CadTmp = Left($CadNum, 1);
					$CadNum = Mid($CadNum, 2);
				}
				if (Mid($sufijo, 2, 1) === "+") {
					$CadNum = zero($CadNum, CInt(Mid($sufijo, 3)) + Len($CadNum), "R");
				} else {
					$CadNum = zero($CadNum, CInt(Mid($sufijo, 3)) + Len($CadNum));
				}
				$PosPunto = InStr(1, $CadNum, ".");
				$CadNum = Left($CadNum, $PosPunto - CInt(Mid($sufijo, 3)) - 1) . "." .
					Mid($CadNum, $PosPunto - CInt(Mid($sufijo, 3)), $PosPunto - 2) .
					Mid($CadNum, $PosPunto + 1);
				$CadNum = $CadTmp . $CadNum;
			}
			$CadNum = Left($CadNum, InStr(1, $CadNum, ".") + 1);
			if ($AoB !== null) {   //Si se ha indicado a la alza o baja
				if ($AoB === "A") {
					$CadNum = CStr(CDbl(Left($CadNum, Len($CadNum) - 2)) + 1);
				} else {
					$CadNum = Left($CadNum, Len($CadNum) - 2);
				}
			} else {   //si NO se ha indicado alza o baja
				if (Right($CadNum, 1) >= "5") {
					$CadNum = CStr(CDbl(Left($CadNum, Len($CadNum) - 2)) + 1);
				} else {
					$CadNum = Left($CadNum, Len($CadNum) - 2);
				}
			}
			
			$Redondea = CDbl($CadNum) * $dec2 * (Iif($EsNegativo, -1, 1));			
		
		} else {
			//SI ES DIVISIBLE POR EL FACTOR (RESULTADO ES MULTIPLO EXACTO)
			$Redondea = CDbl($CadNum) * $dec2 * (Iif($EsNegativo, -1, 1));
		}

	} catch (Exception $e) {
	  $Redondea = $Numero;
	}
	return $Redondea;
}
	
//Función que devuelve la parte izquierda de la cadena en el número de caracteres indicados
function Left($cadena,$longitud){
	$resultado = IsVacio($cadena) ? "" : $cadena;
	$resultado = substr($resultado, 0, $longitud);
	return $resultado;
}

//Función que devuelve la parte derecha de la cadena en el número de caracteres indicados
function Right($cadena,$longitud){
	$resultado = IsVacio($cadena) ? "" : $cadena;
	$resultado = substr($resultado, -$longitud);
	return $resultado;
}


//Función que devuelve desde el caracter comienzo de una cadena hasta la longitud indicada
//si no se indica la longitud se devuelve cadena hasta el final.
function Mid($cadena,$comienzo,$longitud=0){
	$resultado = IsVacio($cadena) ? "" : $cadena;
	if ($longitud==0){
		$resultado = substr($resultado,$comienzo-1);
	} else {
		$resultado = substr($resultado,$comienzo-1,$longitud); 
	}
	return $resultado;
	
}

//Función que retorna retornasitrue si se cumple condicion, caso contrario retorna el valor de retornasifalse
function Iif($condicion,$retornasitrue,$retornasifalse){
	return $condicion? $retornasitrue : $retornasifalse;
}
	
//Función que devuelve true si es un número el valor pasado y false si no
function isNumeric($n) {
  return is_numeric($n);
}	

//pasando una variable número la devuelve en string
function CStr($numero){
	try 
	{
		return (string) $numero;
	} 
	catch (Exception $exception) 
	{
		return '';
	}
}

//pasando un número en variable string devuelve una variable número doble (separador decimal .) con el contenido
function CDbl($cadena){
	try 
	{
		return (float) $cadena;
	} 
	catch (Exception $exception) 
	{
		return 0;
	}	
}

//pasando un número en variable string devuelve una variable número entero con el contenido
function CInt($cadena){
	try 
	{
		return (int) $cadena;
	} 
	catch (Exception $exception) 
	{
		return 0;
	}	
}


//LONGITUD DE CADENA
function Len($cadena){
	if (IsVacio($cadena)){
		return 0;
	}else {
		return strlen($cadena);
	}	
}


//Función Replace que reemplaza en una cadena de texto un valor por otro (todas las coincidencias)
//Variable caseinsensitive si está a true (por defecto) diferencia entre mayúsculas y minúsculas, en true sí.
function Replace($Cadena,$ValorBuscar,$ValorReemplazar){
	$Result = "";

	$Result = str_replace($ValorBuscar,$ValorReemplazar,$Cadena);

	return $Result;

}


//true si cadena no está establecida, null o es vacía.
function IsVacio($cadena){
	return (!isset($cadena) || $cadena==="");
}

//Funcion que retorna el nº de carácter de la posición de la subcadena en la cadena
//Si no lo encuentra devuelve 0
function InStr($posicionini,$cadena,$subcadena){
	$resultado = strpos($cadena,$subcadena,$posicionini);
    return Iif($resultado=="",0,$resultado + 1);
}	

//Función que se le pasa un número y lo devuelve con los decimales fijados (ojo, no redondea)
function DecFixed($number, $dec_length){
    $pos=strpos($number.'', ".");
    if($pos>0){
        $int_str=substr($number,0,$pos);
        $dec_str=substr($number, $pos+1);
        if(strlen($dec_str)>$dec_length){
            return $int_str.($dec_length>0?'.':'').substr($dec_str, 0,$dec_length);
        }else{
            return $number;
        }
    }else{
        return $number;
    }
}


//Restar dos fechas para calcular los días, se devuelve el número de días
function RestaFechas($fechamax,$fechamin){
	$date1 = new DateTime($fechamin);
	$date2 = new DateTime($fechamax);
	$diff = $date1->diff($date2);
	return $diff->days;	
}


//pasamos una fecha en formato AAAA-MM-DD y devuelve la fecha como DD-MM-AAAA
//SE PUEDE INDICAR OTRO SEPARADOR 
function FechaReves($fecha,$separador="/"){
	return Right($fecha,2) . $separador . Mid($fecha,6,2) . $separador . Left($fecha,4);
}


/////////////////////////////////////////////////////////////////////////////////////////////////////
///Función que devuelve la contraseña pasada encriptada con password_hash
function EncriptaPass($pass){
	return password_hash($pass, PASSWORD_DEFAULT);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
///COMPRUEBA SI EXISTE UN FICHERO PASANDO RUTA/NOMBRE DE ESTE EN LA WEB
function existefichero($fichero) {
	$resultado = file_exists ($fichero);
	return $resultado;
}


//Genera una cadena aleatoria de caracteres y números, $num es el número de caracteres
function generacadenaaleatoria($num) {

    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';    
    
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for($i = 0; $i < $num; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
 
    return $random_string;
}

function RegistrarLog($usuario, $accion) {
    try {
        $db = getConnection();
        $stmt = $db->prepare(
            "INSERT INTO log_accesos (fecha, hora, usuario, accion) VALUES (CURDATE(), CURTIME(), :usuario, :accion)"
        );
        $stmt->execute([':usuario' => (string)$usuario, ':accion' => (string)$accion]);
        $db = null;
    } catch(Exception $e) { /* silencioso */ }
}

?>