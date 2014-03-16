<?php
/**
 * FreeKore Php Framework
 * Version: 0.2
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 */
session_start();

class start{
	public static function get_filepath(){
		return self::create_file_path($_SERVER["SCRIPT_NAME"]);
	}
	private static function create_file_path($file_name){

		$pth = explode('public/',$file_name);
		$pth[1] = trim(@$pth[1],'/');
			
		$tot = 0;
		if($pth[1]!=null){
			$pth_syspath = explode('/',$pth[1]);
			$tot = count($pth_syspath);
		}

		$SYS_PATH = './';


		for($i=0;$i<$tot;$i++){ $SYS_PATH .= '../'; }
		return $SYS_PATH;
	}
}

/*fkore app start*/
if(!defined('SYSTEM_PATH')){
	define('SYSTEM_PATH',start::get_filepath());


	/*Carga todos los archivos requeridos*/
	//Main Class FKORE
	require(SYSTEM_PATH.'freekore/fkore/fkore.class.php');

	//load the app configuration
	fkore::load_configuration();
	
	
	//Create url_relative HTTP constant
	fkore::createUrlRelative();

	//load class
	require(SYSTEM_PATH.'freekore/fkore/load.class.php');


	//agregar clase FkException
	require(SYSTEM_PATH.'freekore/fkore/fkexception.class.php');


	//agregar clase AppController
	require(SYSTEM_PATH.'freekore/fkore/appcontroller.class.php');


	// General libs
	require(SYSTEM_PATH.'freekore/fkore/fk.class.php');
    require(SYSTEM_PATH.'freekore/fkore/fk.php');
	

	//DEFINED VARS
	require(SYSTEM_PATH.'app/config/defined/default.php');


	//load the app configuration
	//fkore::load_configuration();

	
	//AutoLoad
	fkore :: _use('app/config/autoload.php');
	fkore::fk_autoload($GLOBALS['autoload']);
	isset($GLOBALS['autoload']);

}