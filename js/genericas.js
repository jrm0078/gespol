	const cdiNewLine="\n";

	// ============================================================
	// FUNCIONES COMUNES REUTILIZABLES EN TODAS LAS PANTALLAS
	// ============================================================

	/**
	 * Diálogo de confirmación de eliminación estándar.
	 * Uso: confirmarEliminar('Nombre del registro', function() { ...tu lógica AJAX... });
	 */
	function confirmarEliminar(nombreRegistro, fnConfirmar) {
		Swal.fire({
			title: '¿Eliminar registro?',
			html: '<b>' + nombreRegistro + '</b>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			returnFocus: false
		}).then(function(result) {
			if (result.isConfirmed) fnConfirmar();
		});
	}

	/**
	 * Toast de éxito/error/warning estándar.
	 * Uso: toastMsg('Guardado correctamente', 'success')
	 *      toastMsg('Error al guardar', 'error')
	 */
	function toastMsg(mensaje, tipo) {
		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: tipo || 'info',
			title: mensaje,
			showConfirmButton: false,
			timer: 2500,
			timerProgressBar: true
		});
	}

	/**
	 * Cargador AJAX genérico con manejo de respuesta estándar {validacion, mensaje, error}.
	 * Uso: ajaxPost(url, datos, function(result) { ... });
	 */
	function ajaxPost(url, datos, fnExito, fnError) {
		$.ajax({
			type: 'POST',
			url: url,
			data: datos,
			dataType: 'json',
			success: function(result) {
				if (result.validacion === 'ok') {
					if (fnExito) fnExito(result);
				} else if (result.validacion === 'warning') {
					Swal.fire({ icon: 'warning', title: 'Atención', html: result.mensaje });
				} else {
					Swal.fire({ icon: 'error', title: 'Error', html: result.error || 'Error inesperado' });
				}
			},
			error: function(xhr) {
				if (fnError) fnError(xhr);
				else Swal.fire({ icon: 'error', title: 'Error de conexión', text: xhr.statusText });
			}
		});
	}

	//OBTIENE VARIABLE GET DE LA URL
	function getQueryVariable(variable)
	{
		   var query = window.location.search.substring(1);
		   var vars = query.split("&");
		   for (var i=0;i<vars.length;i++) {
				   var pair = vars[i].split("=");
				   if(pair[0] == variable){return pair[1];}
		   }
		   return(false);
	}	


	// Función que devuelve la cadena pasada como argumento, ajustada a la
	// derecha/izquierda según el parámetro y rellena con 0 o caracter indicado
	function zero(pCadena, nLon, cLeftRight, cCaracter){
		
		var LeftRight = "";
		var Caracter = "";
		var Cadena = CStr(pCadena);
		
		if (cLeftRight!==undefined){
			LeftRight = cLeftRight;
		}
		
		if (cCaracter!==undefined){
			Caracter = cCaracter;
		}
		
		LeftRight = (LeftRight=="" ? "L" :  LeftRight);
		Caracter = (Caracter=="" ?  "0" : Caracter);
		Lon = nLon;
		if (Cadena.length > Lon){
			aux = Cadena;
		}
		else{
			if (LeftRight == "L"){
				aux = Caracter.repeat(Lon - Cadena.length) + Cadena;
			}
			else{
				aux = Cadena + Caracter.repeat(Lon - Cadena.length);
			}
		}
	return aux;
	}


	//Función que devuelve la fechahora en formato UTC
	//Esta se pasa en formato texto y devuelve texto
	function FechaHoraUtc(FechaHora){
		return Replace(Replace(new Date(FechaHora).toISOString(),":",""),"-","");
	}

	// Función que devuelve la fecha DEL CLIENTE actual en formato 2020-12-01
	function FechaActual(){

		var date = new Date;
		var year = date.getFullYear().toString();
		var month = (date.getMonth()+1).toString(); // beware: January = 0; February = 1, etc.
		var day = date.getDate().toString();
		
		fecha= year + "-" + zero(month,2,"","") + "-" + zero(day,2,"","");
	return fecha;
	}



	// Función que devuelve la hora actual en formato 00:00:00
	function HoraActual(){
		var date = new Date;
		var seconds = date.getSeconds().toString();
		var minutes = date.getMinutes().toString();
		var hour = date.getHours().toString();
		
		hora= zero(hour,2,"","") + ":" + zero(minutes,2,"","") + ":" + zero(seconds,2,"","");
	return hora;
	}



	
	
	//Sumar días a una fecha pasada como texto o date
	function FechaSumaDias(fecha,d){
		var Fecha = new Date();
		var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
		var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
		var aFecha = sFecha.split(sep);
		var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
		fecha= new Date(fecha);
		fecha.setDate(fecha.getDate()+parseInt(d));
		var anno=fecha.getFullYear();
		var mes= fecha.getMonth()+1;
		var dia= fecha.getDate();
		mes = (mes < 10) ? ("0" + mes) : mes;
		dia = (dia < 10) ? ("0" + dia) : dia;
		var fechaFinal = dia+sep+mes+sep+anno;
		return (fechaFinal);
	 }	
	
	//Restar dos fechas para calcular los días, se devuelve el número de días
	function RestaFechas(fechamax,fechamin){
    	var datemax = new Date(fechamax);
        var datemin = new Date(fechamin);
		var diferencia = datemax.getTime() - datemin.getTime();
		return (diferencia / 1000 / 60 / 60 / 24);		
		
	}
	
	
	//Cambia formato de fecha de dd-mm-aaaa a aaaa-mm-dd y viceversa
	function convertDateFormat(string) {
	  var info = string.split('-');
	  return info[2] + '-' + info[1] + '-' + info[0];
	}
	
	
	

	
	//Función que valida email, devuelve true si es correcto y false si no lo es
	function ValidarEmail(valor){

		if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(valor)){
			return true;
		} else {
			return false;
		}

	}



	

		
	//Función que devuelve url amigable de la pasada
	function urlamigable(url) {
	var a = 'àáäâèéëêìíïîòóöôùúüûñçßÿœæŕśńṕẃǵǹḿǘẍźḧ·/_,:;';
	var b = 'aaaaeeeeiiiioooouuuuncsyoarsnpwgnmuxzh------';
	var p = new RegExp(a.split('').join('|'), 'g');

	return url.toString().toLowerCase().replace(/\s+/g, '-')
		.replace(p, function (c) {
			return b.charAt(a.indexOf(c));
		})
		.replace(/&/g, '-y-')
		.replace(/[^\w\-]+/g, '')
		.replace(/\-\-+/g, '-')
		.replace(/^-+/, '')
		.replace(/-+$/, '');
    }
	
	//Función que devuelve true si es un número el valor pasado y false si no
	function isNumeric(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}	
	
	//Función que devuelve el valor del parámetro Numero redondeado
	//según el factor de redondeo Ej. 0,1 redondea a un decimal, 10 a la decena,..
	//AoB = Alza o Baja -> "A" o "B"
	function Redondea(Numero, dec, AoB){
		var EsNegativo = false;
		var CadNum = "";
		var CadTmp = "";
		var sufijo = "";
		var PosPunto = 0;
		var Redondea=Numero;
		var num=Numero;
		var dec2=1;
		
		
		try {
			if (Left("" + Numero, 1) === "-"){
				num = Numero * -1;
				EsNegativo = true;
			} else {
				EsNegativo = false;
			}
			
			dec2 = Iif(dec === undefined, 1, dec);
			CadNum = CStr(num / dec2);
							
			if (InStr(1, CadNum, ".")) {		//SI NO ES DIVISIBLE POR EL FACTOR (RESULTADO NO ES MULTIPLO EXACTO)
				if (InStr(1, CadNum, "E") !== 0) {     //para numeros con coma flotante
					sufijo = Mid(CadNum, InStr(1, CadNum, "E"));
					CadNum = Left(CadNum, InStr(1, CadNum, "E") - 1);
					CadTmp = "";
					if (Left(CadNum, 1) === "+" || Left(CadNum, 1) === "-"){
						CadTmp = Left(CadNum, 1);
						CadNum = Mid(CadNum, 2);
					}
					if (Mid(sufijo, 2, 1) === "+") {
						CadNum = zero(CadNum, CInt(Mid(sufijo, 3)) + Len(CadNum), "R");
					} else {
						CadNum = zero(CadNum, CInt(Mid(sufijo, 3)) + Len(CadNum));
					}
					PosPunto = InStr(1, CadNum, ".");
					CadNum = Left(CadNum, PosPunto - CInt(Mid(sufijo, 3)) - 1) + "." + 
						Mid(CadNum, PosPunto - CInt(Mid(sufijo, 3)), PosPunto - 2) + 
						Mid(CadNum, PosPunto + 1);
					CadNum = CadTmp + CadNum;
				}
				CadNum = Left(CadNum, InStr(1, CadNum, ".") + 1);
				if (AoB !== undefined) {   //Si se ha indicado a la alza o baja
					if (AoB === "A") {
						CadNum = CStr(CDbl(Left(CadNum, Len(CadNum) - 2)) + 1);
					} else {
						CadNum = Left(CadNum, Len(CadNum) - 2);
					}
				} else {   //si NO se ha indicado alza o baja
					if (Right(CadNum, 1) >= "5") {
						CadNum = CStr(CDbl(Left(CadNum, Len(CadNum) - 2)) + 1);
					} else {
						CadNum = Left(CadNum, Len(CadNum) - 2);
					}
				}
				
				Redondea = CDbl(CadNum) * dec2 * (Iif(EsNegativo, -1, 1));
				Redondea = CDbl(DecFixed(Redondea,Len(CStr(dec2))-InStr(1,CStr(dec2),".")));
			
			} else {
				//SI ES DIVISIBLE POR EL FACTOR (RESULTADO ES MULTIPLO EXACTO)
				Redondea = CDbl(CadNum) * dec2 * (Iif(EsNegativo, -1, 1));
				Redondea = CDbl(DecFixed(Redondea,Len(CStr(dec2))-InStr(1,CStr(dec2),".")));
			}

		} catch (error) {
		  Redondea = Numero;
		}
		return Redondea;
	}
		
	//Función que devuelve la parte izquierda de la cadena en el número de caracteres indicados
	function Left(cadena,longitud){
		var resultado = IsVacio(cadena) ? "" : cadena;
		resultado = resultado.substring(0, longitud);
		return resultado;
	}

	//Función que devuelve la parte derecha de la cadena en el número de caracteres indicados
	function Right(cadena,longitud){		
		var resultado = IsVacio(cadena) ? "" : cadena;		;
		resultado = resultado.substring(resultado.length - longitud, resultado.length); 
		return resultado;
	}


	//Función que devuelve desde el caracter comienzo de una cadena hasta la longitud indicada
	//si no se indica la longitud se devuelve cadena hasta el final.
	function Mid(cadena,comienzo,longitud){
		var resultado = IsVacio(cadena) ? "" : cadena;
		if (longitud===undefined){
			resultado = resultado.substring(comienzo - 1, resultado.length); 
		} else {
			resultado = resultado.substring(comienzo - 1, comienzo + longitud - 1); 
		}
		return resultado;
		
	}

	//Función que retorna retornasitrue si se cumple condicion, caso contrario retorna el valor de retornasifalse
	function Iif(condicion,retornasitrue,retornasifalse){
		return condicion? retornasitrue : retornasifalse;
	}
		
	//Funcion que retorna el nº de carácter de la posición de la subcadena en la cadena
	//Si no lo encuentra devuelve 0
	function InStr(posicionini,cadena,subcadena){
		return cadena.indexOf(subcadena,posicionini-1) + 1;
	}	

	//pasando una variable número la devuelve en string
	function CStr(numero){
		return numero.toString();
	}

	//pasando un número en variable string devuelve una variable número doble (separador decimal .) con el contenido
	function CDbl(cadena){
		return parseFloat(cadena);
	}

	//pasando un número en variable string devuelve una variable número entero con el contenido
	function CInt(cadena){
		return parseInt(cadena);
	}

	//Devualve la longitud de cadena pasada
	function Len(cadena){
		
		if (cadena===null || cadena===undefined){
			return 0;
		}else {
			return cadena.length;
		}
		
	}
	
	//Función que se le pasa un número y lo devuelve con los decimales fijados (ojo, no redondea)
	function DecFixed(numero,dec){
		return CDbl(numero).toFixed(dec);
	}
	
	//Comprobar si el iban pasado es correcto
	function fn_ValidateIBAN(IBAN) {

		//Se pasa a Mayusculas
		IBAN = IBAN.toUpperCase();
		//Se quita los blancos de principio y final.
		IBAN = IBAN.trim();
		IBAN = IBAN.replace(/\s/g, ""); //Y se quita los espacios en blanco dentro de la cadena

		var letra1,letra2,num1,num2;
		var isbanaux;
		var numeroSustitucion;
		//La longitud debe ser siempre de 24 caracteres
		if (IBAN.length != 24) {
			return false;
		}

		// Se coge las primeras dos letras y se pasan a números
		letra1 = IBAN.substring(0, 1);
		letra2 = IBAN.substring(1, 2);
		num1 = getnumIBAN(letra1);
		num2 = getnumIBAN(letra2);
		//Se sustituye las letras por números.
		isbanaux = String(num1) + String(num2) + IBAN.substring(2);
		// Se mueve los 6 primeros caracteres al final de la cadena.
		isbanaux = isbanaux.substring(6) + isbanaux.substring(0,6);

		//Se calcula el resto, llamando a la función modulo97, definida más abajo
		resto = modulo97(isbanaux);
		if (resto == 1){
			return true;
		}else{
			return false;
		}
	}
	
	//calculo de modulo 97, se usa por ejemplo para la funcion de validar IBAN fn_ValidateIBAN
	function modulo97(iban) {
		var parts = Math.ceil(iban.length/7);
		var remainer = "";

		for (var i = 1; i <= parts; i++) {
			remainer = String(parseFloat(remainer+iban.substr((i-1)*7, 7))%97);
		}

		return remainer;
	}

	//para calcular el número de las letras usado en función fn_ValidateIBAN
	function getnumIBAN(letra) {
		ls_letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return ls_letras.search(letra) + 10;
	}
		
	//Función Replace que reemplaza en una cadena de texto un valor por otro (todas las coincidencias)
	//Variable caseinsensitive si está a true (por defecto) diferencia entre mayúsculas y minúsculas, en true sí.
	function Replace(Cadena,ValorBuscar,ValorReemplazar,caseinsensitive=true){
		var Result="";

		if (caseinsensitive){
			//diferencia entre may y min
			const regex = new RegExp(ValorBuscar, 'g');
			Result = Cadena.replace(regex, ValorReemplazar) ;		
		} else {
			//no diferencia entre may y min
			const regex = new RegExp(ValorBuscar, 'gi');
			Result = Cadena.replace(regex, ValorReemplazar) ;
		}
	
		return Result;
	
	}
	
	//retorna True si la variable es nula, "" o undefined
	function IsVacio(variable){
		return (variable===null || variable===undefined || variable==="" );		
	}	
	
	
	//retorna True si la variable es un Array
	function IsArray(variable){
		return Array.isArray(variable);
	}
	
	//Pasa cadena de texto a array separando los elementos según caracterseparador
	function Split(variable,caracterseparador){
		return variable.split(caracterseparador);
	}
	
	
	//Genera un número aleatorio entre el rango que se indique
	function Random(min, max) {
		return Math.floor((Math.random() * (max - min + 1)) + min);
	}
	
	
//FUNCION QUE RETORNA SI CAMPO ES texto, numero, etc... EN FUNCIÓN DEL TIPO EN BASE DE DATOS
function TipoCampoBDaDatatable(TipoCampo){
	resultado="";
	
	switch (TipoCampo){
		case "BIGINT":
			resultado='numero';
			break;
		case "CHAR":
			resultado='texto';
			break;
		case "DATE":
			resultado='fecha';
			break;
		case "DATETIME":
			resultado='fechahora';
			break;
		case "DOUBLE":
			resultado='decimal';
			break;
		case "FLOAT":
			resultado='decimal';
			break;
		case "INT":
			resultado='numero';
			break;
		case "LONGTEXT":
			resultado='texto';
			break;
		case "MEDIUMINT":
			resultado='numero';
			break;
		case "MEDIUMTEXT":
			resultado='texto';
			break;
		case "SMALLINT":
			resultado='numero';
			break;
		case "TEXT":
			resultado='texto';
			break;
		case "TIME":
			resultado='texto';
			break;	
		case "TIMESTAMP":
			resultado='texto';
			break;	
		case "TINYINT":
			resultado='numero';
			break;	
		case "TINYTEXT":
			resultado='texto';
			break;	
		case "VARCHAR":
			resultado='texto';
			break;		
		case "AUTONUMERICO":
			resultado='numero';
			break;	
		case "DECIMAL":
			resultado='decimal';
			break;			
		default:
			resultado="texto";
			break;	
	}
	
	return resultado;
}	