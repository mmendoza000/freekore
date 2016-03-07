<?php
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 * @since      0.1 Beta
 *
 */


/**
 * @package    load.class.php
 * @desc       Programa para cargar views,libs, helpers, plugins
 * @since      0.1 Beta
 */


class Load{

	// Contenido dinamico
	private $ViewContent= array(); // Contenido
	public $ReturnResult= false;  // False: Imprime resultado (Default), true devuelve resultado

	/**
	 * @package load.class.php
	 * @method view()
	 * @desc Loads a view
	 * @example 1) $this->Load->View('Inicio/view.php');
	 *          2) $data['mi_var']; $this->Load->View('folder/view.php',$data);
	 *          3) $this->putContent('{list}',$listValues);$this->Load->View('folder/view.php');
	 * @since 0.1 Beta
	 */
	public function view($view,$data = array(),$ReturnResult=false){

		// False: Imprime resultado, true devuelve resultado
		$this -> ReturnResult = $ReturnResult;

		$view_file = 'app/views/'.$view;
		$view_file_path = SYSTEM_PATH.$view_file;
		$view_exist = false;

		if( fk_file_exists($view_file) ){ $view_exist = true; }

		if($view_exist==FALSE){
			//view no existe
			if($GLOBALS['FKORE']['RUNNING']['app']['interactive']==true){

				try{
					throw new FkException("Archivo: ".$view_file." no existe ");
				}catch(FkException $e){
					$e->description='Es requerido el archivo view <b>'.$view_file.'</b>,
					                    sin embargo no fue encontrado';
					$e->solution='Cree el archivo <b>'.$view_file.'</b> y agregue el codigo requerido.
					                 Ejemplo:';
					$e->solution_code= fk_str_format('<?php fk_header();?>
Este es el archivo '.$view_file.'
<?php fk_footer();?> ','html');
					$e->show('code_help');

				}
					
			}else{ die("<fieldset><h1>Error 404 -1: La p&aacute;gina no existe </h1></fieldset>"); }

		}else{
			// Preprocesar la vista
			return $this->PreProcessFile($view_file_path,$data);
		}

	} // End View
	/**
	 * @package Load
	 * @method Model()
	 * @desc Loads a model
	 * @example 1) $this->Load->Model('MyModel'); calls app/models/MyModel.php
	 *          2) $this->Load->Model(array('MyModel1','MyModel2')); calls the array of models
	 * @since 0.1 Beta
	 */
	public static function model($Model){

		if(is_array($Model)){
			// Cargar los modelos
			if(count($Model)>0){
				foreach ($Model as $k => $v) {
					// Cargar el modelo
					fkore::_use('app/models/'.$v.'.php');
				}
			} // End count $Model >0

		}else{
			// Cargar el modelo
			fkore::_use('app/models/'.$Model.'.php');

		}
	} // End Model
	/**
	 * @package load
	 * @method Helper()
	 * @desc Loads a Helper
	 * @example 1) $this->Load->Helper('MyHelper'); calls app/helpers/MyHelper.php
	 *          2) $this->Load->Helper(array('MyHelper1','MyHelper2')); calls the array of Helpers
	 * @since 0.1 Beta
	 */
	public static function helper($Helper){

		if(is_array($Helper)){
			// Cargar los helpers
			if(count($Helper)>0){
				foreach ($Helper as $k => $v) {
					// Cargar helper
					fkore::_use('app/helpers/'.$v.'.php');
				}
			} // End count $Helper >0

		}else{
			// Cargar el Helper
			fkore::_use('app/helpers/'.$Helper.'.php');

		}
	}

	/**
	 * @package Load
	 * @method Library()
	 * @desc Loads a Library | loads array of libraries
	 * @example 1) $this->Load->Library('MyLibrary'); calls app/libraries/MyHelper.php
	 *          2) $this->Load->Library(array('MyLibrary1','MyLibrary2')); calls the array of Libraries
	 * @since 0.1 Beta
	 */
	public static function library($Lib){

		if(is_array($Lib)){
			// Cargar librerias
			if(count($Lib)>0){
				foreach ($Lib as $k => $v) {
					// Cargar el modelo
					fkore::_use('app/libraries/'.$v.'.php');
				}
			} // End count $Lib >0

		}else{
			// Cargar libreria
			fkore::_use('app/libraries/'.$Lib.'.php');
		}
	} // Library
	/**
	 * @package load
	 * @method plugin()
	 * @desc Loads a Plugin | loads array of Plugins
	 * @example 1) $this->Load->Plugin('MyPlugin'); calls app/plugins/MyPlugin/MyPlugin.class.php
	 *             & app/plugins/MyPlugin/MyPlugin.utils.php
	 *          2) $this->Load->plugin(array('MyPlugin1','MyPlugin2')); calls the array of Plugins
	 * @since 0.1 Beta
	 */
	public static function plugin($Plugin){

		if(is_array($Plugin)){
			// Cargar Plugin
			if(count($Plugin)>0){
				foreach ($Plugin as $k => $v) {
					// Cargar el plugin

					fkore::_use('app/plugins/'.$v."/".$v.".class.php");
					// get other variables (css code, css links, js code, js links )
					fkore::_use("app/plugins/".$v."/".$v.".utils.php");
				}
			} // End count $Plugin >0

		}else{
			// Cargar $Plugin
			fkore::_use("app/plugins/".$Plugin."/".$Plugin.".class.php");
			// get other variables (css code, css links, js code, js links )
			fkore::_use("app/plugins/".$Plugin."/".$Plugin.".utils.php");

		}
	} // $Plugin

	/**
	 * @package load
	 * @method PreProcessFile()
	 * @desc Pre process file replacing the content sent by $this->PutContent() method
	 * @since 0.1 Beta
	 */
	private function PreProcessFile($File,$Arr = array()){

		// Declarar Arrays
		$ArrSearch = array();
		$ArrRepl   = array();

		// Array de variables enviado por funcion
		if(count($Arr)>0){
			foreach ($Arr as $k => $v){

				$$k = $v;
			}

		}

		// Array generado por PutContent
		if(count($this->ViewContent)>0){
			foreach ($this->ViewContent as $k => $v){
				$ArrSearch[] = $k;
				$ArrRepl[] = $v;
			}

		}

		$f_view = file_get_contents($File);

		$view_content = str_replace($ArrSearch, $ArrRepl, $f_view);

		$ViewResult = '';
		if($this->ReturnResult==TRUE){

			// Returns result
			ob_start();
			eval('?>' . $view_content . '<?php ');
			echo $ViewResult = ob_get_contents();
			ob_end_clean();

		}else{

			// Prints result
			if(app_config_is('utf8_encode', true)){
				// if is utf8 returns value in utf8
				echo  utf8_encode(eval('?>' . $view_content . '<?php '));
			}else{
				echo  eval('?>' . $view_content . '<?php ');
			}

		}
		
		if(app_config_is('utf8_encode', true)){
			// if is utf8 returns value in utf8
			$ViewResult = utf8_encode($ViewResult);	
		}

		return $ViewResult;


	} // End Pre Process

	/**
	 *@package load
	 *@method put_content()
	 *@desc replaces Content into a view
	 *@example  put_content('{list}' , $MyListContent );
	 *          will replace the tag {list} with the $MyListContent in the
	 *          $this->Load->view('MyView.tpl') execution
	 *@since v0.1 beta
	 * */

	public function PutContent($Index , $Content ){

		$this->ViewContent[$Index] = $Content;
			
	}

	public static function database(){

		//agregar interface de base de datos
		fkore :: _use('freekore/fkore/db/db_interface.php');

		//agregar adaptadores de base de datos
		fkore :: _use("freekore/fkore/db/adapters/db_".$GLOBALS['FKORE']['RUNNING']['db']['db_type'].".php",".php");

		//agregar Active Record de base de datos
		fkore :: _use("freekore/fkore/db/db_record.class.php");
		fkore :: _use("freekore/fkore/db/activerecord.class.php");

		//agregar Db Base
		fkore :: _use("freekore/fkore/db/db.php");

		$db = new db();
		$db->connect();
			
	}

	public static function freekoreLib($Library){
		fkore::_use('freekore/libs/'.$Library.'.php','',true);
	}

	/**
	 * @desc Loads an internal url
	 * @param $url;
	 * @example when you need to load an internal url inside a view <b>Load::url('myaccount/general/');</b> will load myaccount.controller.php and executes general() method
	 *
	 * */
	public static function url($url,$return=false){

		$url_rs=fkore::url_processor($url);

		if(isset($url_rs['file_controller'])&&isset($url_rs['controller'])&&isset($url_rs['action'])){

			if($return==true){ ob_start(); }

			require_once (SYSTEM_PATH.'app/controllers/'.$url_rs['file_controller']);
			$page = new $url_rs['controller']($url_rs);

			if($return==true){
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			}


		}
	} // end url

	/**
	 * @package Load
	 * @method formModel()
	 * @desc Loads a form model (app/models/forms/)
	 * @example
	 * @since 0.3.4
	 */
	public static function formModel($formModel){
		self::model('forms/'.$formModel);
	} // End	formModel
	
	/**
	* @package Load
	* @method recordModel()
	* @desc Loads a db record model (app/models/records/)
	* @example
	* @since 0.3.4
	*/
	public static function recordModel($recordModel){
		self::model('records/'.$recordModel);
	} // End recordModel
	

} // end Load


