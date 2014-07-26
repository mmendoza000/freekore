<form method="POST">
<?php 

pa($_POST);

$json_priv =  '{"PUEDE_ATENDENDER_CHAT":"1","PUEDE_ADMINISTRAR":"0"}';

$user_privs = json_decode($json_priv,true);


foreach ($GLOBALS['PRIVILEGIOS'] as $k=>$v){
	
	$checked = '';
	if(isset($user_privs[$k])){ 
		if($user_privs[$k]==1){ $checked = 'checked="checked"'; }
	}
	
	echo '<div><label><input type="checkbox" '.$checked.' value="1" name="'.$k.'" >'.$v.'</label></div>';
}

?>
<input type="submit"  value="Guardar privilegios">
</form>
<br>
hola

{U}


{A}
{B}