<section class="panel">
    <header class="panel-heading">
        Pagination
    <span class="tools pull-right">
        <a href="javascript:;" class="fa fa-chevron-down"></a>
        <a href="javascript:;" class="fa fa-cog"></a>
        <a href="javascript:;" class="fa fa-times"></a>
    </span>
    </header>
    <div class="panel-body">
<div id="cont" class="cont" style="min-height:500px">
<h1 id="x">Paso 1 de 3</h1> 
<form id="frm1" name="frm1" onsubmit="return false" >
<fieldset><legend>Archivo</legend>

<?php
echo fk_message('warning', '<b>IMPORTANTE:</b> Seleccione el archivo con formato <b>.xls Excel 97-2003</b> ',false); 
echo fk_file_field('archivo', 'archivo', '',null,'required');
?>
<br>
</fieldset>
<input type="submit" class="btn" value="Continuar &raquo;">
</form>
    </div>

    <div id="cont2" class="cont" style="display:none;">
Cargando...
</div>
<div id="cont3" class="cont" style="display:none;">
Cargando...
</div>

</div>
        
        
</section>

<?php 
$xSubmit = new ajax_submit();
echo $xSubmit->Render('frm1', 'cont2', 'admin/import-2/');

?>


<script>
$(function(){
	
	
	

	

	$("#frm1").validate({
	 submitHandler: function(form) {
	 	alert(123);
		      var pArgs = {pDiv:'cont2', 
						  pUrl:'<?php echo $GLOBALS['currentUrl']?>',
						  pForm:'frm1',
						  pArgs:'oper=step2',
						  pUrlAfter:'', 
						  insertMode:''
						  };
			  fk_ajax_submit(pArgs);
			  //$('#cont').hide();
			  alert(1);
			  
		 		return false;
		 },invalidHandler: function(form, validator){
	      var errors = validator.numberOfInvalids();
	      if (errors) { $('#cont').hide(); alert("Por favor, llena los campos obligatorios"); }
		 }
	});

	$("#table").change(function(){
		<?php 
		$xFields = new ajax('submit', 'dvFields', 'form:frm1;url:admin/import-select-fields/');
		//echo $xFields->render();
		?>
	});
});
</script>