<form id="frm3" id="frm3" onsubmit="return false;">
<h1>Paso 2 de 3</h1>
<div>
<?php 
echo fk_message('warning', '<b>Elija informaci&oacute;n:</b> Seleccione las columnas y los renglones que desea importar',false);
?>
<?php
$Imp = new FileImporter();

$File = new ActiveRecord('uploads');
$File->find(fk_post('archivo'));

$f = uploads_directory().'/'.$File->fields['archivo'];
$tablefields = $GLOBALS['tableFields'];

?>
<div style="width:100%; height:500px; overflow:auto;">
<?php 
// Imprime tabla de resultado
$Imp->importExcelData($f,$tablefields);
?>
</div>
<input type="button" class="btn" onclick="$('#cont').show(800); $('#cont2').hide(800);" value=" &laquo; Regresar ">
<input type="submit" class="btn" value="Importar &raquo;">

</div>
<div class="clear"></div>
</form>
 <script type="text/javascript">
<!--

$("#frm3").validate({
	 submitHandler: function(form) {
		 
		      var pArgs = {pDiv:'cont3', 
						  pUrl:'<?php echo $GLOBALS['currentUrl']?>',
						  pForm:'frm3',
						  pArgs:'oper=step3',
						  pUrlAfter:'', 
						  insertMode:''
						  };
			  fk_ajax_submit(pArgs);
			  $("#cont2").hide();
		 return false;
	 },invalidHandler: function(form, validator){
      var errors = validator.numberOfInvalids();
      if (errors) { alert("Por favor, llena los campos obligatorios");}
	 }
});
//-->
</script>