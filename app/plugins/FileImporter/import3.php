<h1>Paso 3 de 3</h1>

<?php



$Imp = new FileImporter();
$Imp->tableName = $GLOBALS['tableName'];
$Imp->fixedC = $GLOBALS['fixedC'];
$Imp->rules = $GLOBALS['fixedC'];
$rules = fk_post('rules');

//---------------------
//IMPORTAR
//---------------------

if($Imp->importToDB()){
	echo fk_message('ok', ' '.$Imp->registros_importados.' registros fueron importados',false);
}else{
	echo fk_message('alert', $Imp->result_message,false);
}

?>
<input type="button" class="btn"
	onclick="$('#cont2').show(800); $('#cont3').hide(500);"
	value=" &laquo; Regresar ">
	
	<input type="button" class="btn" onclick="window.close()" value=" Cerrar ">