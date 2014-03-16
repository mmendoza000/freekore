<?php
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 */
/*************************************************************
 **  Programa:    FkExceptions.class.php
 **  Descripcion: Clase exceptions de freekore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    26/04/10   mmendoza    Creado.
 **************************************************************/
/**
 *@package FkException extends Exception
 *@since v0.1 beta
 *@desc  FreeKore Exeptions Manager
 * */
class FkException extends Exception {

	public $description = 'Exception no definida';
	public $solution = 'No hay solucion definida';
	public $solution_code = '';
	public $error_code = '';
	/**
	 *@package FkException extends Exception
	 *@since v0.1 beta
	 *@method show()
	 *@param $tpl_exception
	 *@desc  Prints the FkExeption error.
	 *       To use the parameter $tpl_exception layouts see the  defined
	 *       templates on freekore/sys_messages/exceptions
	 * */
	public function show($tpl_exception = 'common'){
		
		$tpl_exception = SYSTEM_PATH.'/freekore/sys_messages/exceptions/'.$tpl_exception.'.php';

		
		// Mensaje amigable en caso que no este activo el interactive
		if(@$GLOBALS['FKORE']['RUNNING']['app']['interactive']!=TRUE){
			if(file_exists(SYSTEM_PATH.'/app/errors/error_general.php')){
				$tpl_exception = SYSTEM_PATH.'/app/errors/error_general.php';
			}else{
				$tpl_exception = SYSTEM_PATH.'/freekore/sys_messages/page/default_error_general.php';
			}
			 
		}
			
		

		$inc_files_arr = get_included_files();
		$tot_inc_files = count($inc_files_arr);
		
			
		$details = '<h3>Included Files</h3>'.implode('<br />',$inc_files_arr).' <br /> Total:'.$tot_inc_files.
	              '<h3>Memoria Usada</h3><p>'.fk_memory_usage().'</p>';

		

		$exc_cont = file_get_contents($tpl_exception);
		$find = array('{message}','{trace}','{description}','{solution}','{solution_code}','{details}','{error_code}');
		$repl = array($this->getMessage(),$this,$this->description,$this->solution,$this->solution_code,$details,$this->error_code);
		$exc_cont = str_replace($find,$repl,$exc_cont);

		if(fk_post('ajax')==1){
			echo $exc_cont;	
		}else{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FreeKore PHP</title>
		<?php echo fk_css();?>
		<?php echo fk_js();?>
</head>

<body>
		<?php echo $exc_cont?>
</body>
</html>
		<?php
		}
		die();
	} // FkException -> show()

}  // End Class
?>
