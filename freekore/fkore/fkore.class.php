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
 **  Programa:  fkore.class.php
 **  Descripcion: Funciones principales del sistema FlexKore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    26/04/10   mmendoza    Creado.
 **************************************************************/
/**
 *@package fkore
 *@desc  Freekore Main Package
 * */
class fkore {

	/**
	 *@package fkore
	 *@var string
	 *@desc  Freekore Version
	 * */
	static $version = '0.4.0';

	/**
	 *@package fkore
	 *@method _use()
	 *@since v0.1 beta
	 *@desc  Includes file or files inside Freekore Application. Using it You Don't have
	 *		 to worry about relative path. Automatically detects the path.
	 *@example 1) fkore::_use('app/models/*'); Includes all files on app/models/
	 *         2) fkore::_use('app/models/*','.php'); Includes all .php files on app/models/
	 *         3) fkore::_use('app/models/file.php'); Includes app/models/file.php
	 *         4) fkore::_use('app/models/file.php', FALSE); includes  app/models/file.php but
	 *            if file does not exist the application can continue.
	 *            By default Required param is set to TRUE.
	 *
	 * */
	public static function _use($p_rute,$end = '',$required = true){

		$x_r=explode('/',$p_rute);
		$num_dirs=count($x_r);
		$dir = trim(SYSTEM_PATH.$p_rute,'*');

		if($x_r[$num_dirs-1]!="*"){
			// un solo archivos
			try{
			 if(file_exists($dir)){
			 	include_once($dir);
			 }else{
			 	// Mandar error si es requerido
			 	if($required == true){
			 		throw new FkException('El archivo '.$dir.' no existe');
			 	}
			 }
			}catch(FkException $ex){
			 $ex->show();
			}

		}else{

			// multiples archivos
			try{
				if(!@$reader = opendir($dir)){


					throw new FkException('Directorio '.$dir.' no existe');
				}
			}catch(FkException $ex){
				$ex->show();
			}
			while ($file = readdir($reader)){

				if(!is_dir(SYSTEM_PATH.$file)){

					if($end != ''){
						// con terminacion
				  $filex =	explode($end, $file);
				  if(count($filex)==2 && $filex[1] == '' ){
				  	if(file_exists($dir.$file)){ include_once($dir.$file);}
				  }
					}else{
						// archivo completo
						if(file_exists($dir.$file)){
							include_once($dir.$file);
					 }
					}
				} // end if !is_dir
			} // end while
			closedir ($reader);
			// multiples archivos
		}

	} // end _use()
	/**
	 *@package fkore
	 *@method _use()
	 *@since v0.1 beta
	 *@desc  Includes Freekore Plugin
	 *       1) loads app/plugins/{plugin}/{plugin}.class.php file &
	 *        app/plugins/{plugin}/{plugin}.utils.php file
	 * */
	public static function usePlugin($plugin){

		// Include Class
		self :: _use("app/plugins/".$plugin."/".$plugin.".class.php");

		// get other variables (css code, css links, js code, js links )
		//Js
		self :: _use("app/plugins/".$plugin."/".$plugin.".utils.php");
			
			
			
	} // _useControl('FkGrid')
	/**
	 *@package fkore
	 *@method url()
	 *@since v0.1 beta
	 *@desc  returns the http url
	 * */
	//function para imprimir una url,
	//TODO:considerara permalinks en la proxima version
	public static function url($p_path){
		return RUTA_HTTP.$p_path;
	} // url

	/**
	 *@package fkore
	 *@method get_theme_dir()
	 *@since v0.1 beta
	 *@desc  Returns the complete theme url
	 * */
	//function para obtener la ruta http de la ruta raiz del tema configurado
	public static function get_theme_dir($tema = NULL){

		if(!defined('SELECTED_THEME_DIR')){
			// definir ruta del tema seleccionado
			if($tema==NULL || trim($tema)==''){
				$the_theme=THEME;
			}else{
				$the_theme=$tema;
			}

			$url_dir = RUTA_HTTP.THEMES_DIR.'/'.$the_theme;
			define('SELECTED_THEME_DIR',$url_dir);

		}

		$ruta_http_tema = constant('SELECTED_THEME_DIR');

		return $ruta_http_tema;
	} // get_theme_dir
	/**
	 *@package fkore
	 *@method FkResource($Resource,$Type)
	 *@since v0.1 beta
	 *@desc  Gets the .js or .css resource
	 * */
	public static function FkResource($Resource,$Type){
		$Resource = decode($Resource);

		$fileName = SYSTEM_PATH.$Resource;
		// Reject if is not  .js o .css file
		$ext=self::file_ext($fileName);
		if($ext=='js' || $ext == 'css'){
			if(file_exists($fileName)){

				if($Type=='js'){
					header("content-type: text/javascript");
					echo '/* File:'.$fileName.' */
';
					echo file_get_contents($fileName);
				}
				if($Type=='css'){
					header("Content-type: text/css");
					echo '/* File:'.$fileName.' */
';
					echo file_get_contents($fileName);
				}

			}else{echo '/* File not found:'.$fileName.' */';}

		}
	}
	/**
	 *@package fkore
	 *@method file_ext($file)
	 *@since v0.1 beta
	 *@desc  returns the file exencion
	 * */
	public static function file_ext($file){
		$fext = explode('.',$file);
		$ext_pos = count($fext) - 1;
		return $fext[$ext_pos];
	}
	/**
	 *@package fkore
	 *@method Initialize()
	 *@since v0.1 beta
	 *@desc  runs freekore
	 **/
	public static function InitializeFK($P_GET){


		//load view

		if(isset($P_GET['url'])){
			$url_rs=self::url_processor($P_GET['url']);
		}





		if(!isset($url_rs)){
			$url_rs['controller'] = 'IndexController';
			$url_rs['module'] = 'index';
			$url_rs['action'] = 'index';

			$url_rs['file_controller'] = 'index.controller.php';
			$url_rs['file_view'] = 'index/index.view.php';
		}


		$controller_exist = false;
		if( file_exists(SYSTEM_PATH.'app/controllers/'.$url_rs['file_controller']) ){ $controller_exist = true; }


		if($controller_exist==true){
			// controler existe

			// view existe
			//EJECUTAR CONTROLLER

			require(SYSTEM_PATH.'app/controllers/'.$url_rs['file_controller']);

			//EJECUTAR CONTROLLER
			$page = new $url_rs['controller']($url_rs);

		}else{
			// controler no existe
			if($GLOBALS['FKORE']['RUNNING']['app']['interactive']==true){

				if(self::dynamic_page($url_rs['file_controller'],$url_rs)==false){
					// MOSTRAR ERROR Y AYUDA
					$cont_control = str_replace('__ControlerName__',$url_rs['controller'],file_get_contents(SYSTEM_PATH.'freekore/build/templates/controller-layout.tpl'));

					try{
						throw new FkException('El Controlador "'.$url_rs['file_controller'].'" no existe');
					}catch(FkException $e){
						$e->description='Es requerido el archivo Controllador <b>'.$url_rs['file_controller'].'</b>
						                    , sin embargo no fue encontrado.';
						$e->solution='1. Crea la clase <b>'.$url_rs['controller'].'</b> en el archivo
						                    <b>'.$url_rs['file_controller'].'</b> ';						                           $e->solution_code = fk_str_format($cont_control,'html');
						$e->show('code_help');
					}
				}



			}else{

				if(self::dynamic_page($url_rs['file_controller'],$url_rs)){
					if(file_exists(SYSTEM_PATH.'app/errors/error_404.php')){
						require(SYSTEM_PATH.'app/errors/error_404.php');
					}else{
						require(SYSTEM_PATH.'freekore/sys_messages/page/default_error_404.php');
					}
				}



			}
		}

	} // End InitializeFK
	public static function url_processor($url){



		$file_lst = array();

		if($GLOBALS['FKORE']['config']['APP']['mod_rewrite']){
			//----------------
			//MOD REWRITE TRUE
			//----------------
			$url_div = explode('/',$url);
			$tot= count($url_div);

			$cnt = 0;

			for($i=0;$i<$tot;$i++){

				if(trim($url_div[$i])!=''){
					$cnt++;

					$file_lst['url'][$cnt]['value'] = $url_div[$i];
					$file_lst['url'][$cnt]['is_file_or_dir'] = 'dir';
				}

			}
			// last is file

			$file_lst['url'][$cnt]['is_file_or_dir'] = 'file';

		}else{
			//----------------
			//MOD REWRITE FALSE
			//----------------
			$_slash = '/';
			$_q_mark = '?';


			$url_and_vars = explode($_q_mark,$url);

			$the_url = $url_and_vars[0];
			$the_vars = @$url_and_vars[1];

			$url_div = explode($_slash,$the_url);
			$tot= count($url_div);

			$cnt = 0;

			for($i=0;$i<$tot;$i++){

				if(trim($url_div[$i])!=''){
					$cnt++;
					$file_lst['url'][$cnt]['value'] = $url_div[$i];
					$file_lst['url'][$cnt]['is_file_or_dir'] = 'dir';
				}
			}

			// last is file

			$file_lst['url'][$cnt]['is_file_or_dir'] = 'file';

			//get prams
			$the_vars = trim($the_vars,'{');
			$the_vars = trim($the_vars,'}');
			$the_vars_arr = explode(';',$the_vars);

			$file_lst['get_vars']=array();

			if(count($the_vars_arr)>0){
				foreach($the_vars_arr as $k => $v){
					$new_v = explode('=',$v);

					if(isset($new_v[0]) && isset($new_v[1])){
						$file_lst['get_vars'][$new_v[0]]=$new_v[1];
					}

				}
			}



		}


		// return controller
		$file_controller = 'index.controller.php';
		$controller = 'IndexController';
		$module = '';
		$action = 'index';



		$i=0;

		// Controller
		if(isset($file_lst['url'][1])){
			$v = $file_lst['url'][1];
			$file_controller = $v['value'].'.controller.php';
			$controller = self::camelcase(self::var_format($v['value']));
			$controller = $controller.'Controller';
		}

		//Method
		if(isset($file_lst['url'][2])){
			$action = $file_lst['url'][2]['value'];
		}






		$file_rs = array();
		$file_rs['url_processed']= $url;
		$file_rs['module']=self::var_format($module);
		$file_rs['action']=self::var_format($action);

		$file_rs['file_controller']=$file_controller;
		$file_rs['controller']=$controller;
		//$file_rs['directory_track']=$file_lst['url'];

		$file_rs['get_vars']=@$file_lst['get_vars'];

		return $file_rs;

	} // url_processor($url)


	public static function fk_autoload($autoload){
		//Section freekore libs
		self::autoloader('freekore/libs',$autoload['freekore-libs']);    // Libs
		//Section Database
		if($autoload['database']==true){
			Load::database();
		}
		//Section freekore debug
		if($GLOBALS['FKORE']['RUNNING']['app']['debug']==true){
			self::autoloader('freekore/debug',$autoload['freekore-debug']);    // debug
		}

		//Section app
		self::autoloader('app/models',$autoload['models']);    // Models
		self::autoloader('app/plugins',$autoload['plugins']);   // Plugins
		self::autoloader('app/libraries',$autoload['libraries']); // Libs
		self::autoloader('app/helpers',$autoload['helpers']);   // Helpers

	}
	private static function autoloader($Dir,$Arr){

		if(count($Arr)>0){
			foreach ($Arr as $k=>$v){
				fkore::_use($Dir.'/'.$v.'.php');
			}
		}
	}

	public static function load_configuration(){
		//--------------------
		// LOAD CONFIG FILES
		//--------------------
		// config.ini
		self::read_config_file('config');
		// database.ini
		self::read_config_file('database');


		//--------------------
		// Set view,controler & model files variable
		//--------------------


		//--------------------
		// Set database conection
		//--------------------
		// get app activated
		$app_on=$GLOBALS['FKORE']['config']['APP']['app_activated'];
		$app_on = strtoupper($app_on);

		$arr_app_act = $GLOBALS['FKORE']['config'][$app_on];
		// get environment activated
		$env_on = strtoupper($arr_app_act['database_mode']);

		//--------------------
		// Set HTTP PATH
		//--------------------
		//Set HTTP variable = www_server
		//(moved to from AppController)
		//define('HTTP',$arr_app_act['www_server']);

		// get environment activated variables
		$arr_env = $GLOBALS['FKORE']['database'][$env_on];

		$GLOBALS['FKORE']['RUNNING']['app'] = $arr_app_act;
		$GLOBALS['FKORE']['RUNNING']['db'] = $arr_env;

		// define  database vars
		define('HOST',$arr_env['db_host']);
		define('USER',$arr_env['db_username']);
		define('PASSWORD',$arr_env['db_password']);
		define('SYSTEM_DB',$arr_env['db_name']);
		define('DB_TYPE',$arr_env['db_type']);
		// Inicializar JS links, Css links
		$GLOBALS['FKORE']['js_links'] = '';
		$GLOBALS['FKORE']['css_links'] = '';

		//SET LANGUAGE
		$DEFAULT_LANGUAGE = $GLOBALS['FKORE']['config']['APP']['default_language'];
		$GLOBALS['APP_LANGUAGE']  = (@$_SESSION['language']!=null) ? $_SESSION['language'] : $DEFAULT_LANGUAGE ;



		//pa($GLOBALS['FKORE']);



	} // read_config

	private static function read_config_file($FILE){

		//
		$file_cnf = file(SYSTEM_PATH.'app/config/'.$FILE.'.php');
		$subsection = false;

		foreach($file_cnf as $k=>$v){

			$v=trim($v);
			$char0=substr($v,0,1);

			if($char0!=';' && $char0!='#' && $v!=''){
				//LINEAS NO COMENTADAS
				$var_value = explode('=',$v);
				$var   = trim($var_value[0]);
				$value = trim(@$var_value[1]);
				$value = trim($value,'"');

				if(strtoupper($value)==="ON"){$value = TRUE;}
				if(strtoupper($value)==="OFF"){$value = FALSE;}




				if($char0=='['){
					// SUB SECTION
					$subsection = true;
					$section_name = trim($var,'[');
					$section_name = trim($section_name,']');
					$section_name = strtoupper($section_name);

				}else{
					// VARS
					if(!$subsection){
						// NO SECCIONES
						$GLOBALS['FKORE'][$FILE][$var]=$value;
					}else{
						// SI HAY SECCIONES
						$GLOBALS['FKORE'][$FILE][$section_name][$var]=$value;

					}


				}
				//LINEAS NO COMENTADAS
			}

		}

			

	} // read_config_file

	/**
	 *@package fkore
	 *@method createUrlRelative()
	 *@desc creates the Url Relative to the Current Url
	 *@example  Current url = "http://example/Controller/Model/var1/"
	 *          relative url = "../../../" , removing Controller/Model/var1/
	 *@since v0.1
	 * */
	public static function createUrlRelative(){

		if(!defined('HTTP')){
			if($GLOBALS['FKORE']['config']['APP']['mod_rewrite']){
				$rel_path = $GLOBALS['FKORE']['RUNNING']['app']['www_server'];
			}else{
				$rel_path = $GLOBALS['FKORE']['RUNNING']['app']['www_server'].'public/';
			}


			//--------------------
			// Set HTTP PATH
			//--------------------
			//Set HTTP variable = www_server
			define('HTTP',$rel_path);
		}

	} // createUrlRelative


	private static function encodedArray($StrEncoded){
		/*
		 * Generar array de caracteres especiales
		 * ej: base64_encode("á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ")
		 * */
		$StrDecoded = base64_decode($StrEncoded);
		eval("\$strByComas = \"$StrDecoded\";");
		$Arr=explode(',', $strByComas);

		return $Arr;
	}

	public static function var_format($txt){

		// Para:  'a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N'
		$find = self::encodedArray('4SzpLO0s8yz6LPEswSzJLM0s0yzaLNE=');

		//Rememplazamos caracteres especiales latinos
		$repl = array('a', 'e', 'i', 'o', 'u', 'n','A', 'E', 'I', 'O', 'U', 'N');
		$txt = str_replace ($find, $repl, trim($txt));
		// Anadimos los guiones
		$find2 = array(' ', '&', '\r\n', '\n', '+');
		$txt = str_replace ($find2, '_', $txt);
		// Eliminamos y Reemplazamos demas caracteres especiales
		$find3 = array('/[^A-Za-z0-9\-<>.$]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('_', '_', '');
		$txt = preg_replace ($find3, $repl, $txt);

		return $txt;
	}

	public static function camelcase($s){

		$s = ucwords(strtolower(strtr($s, '_', ' ')));
		$s = str_replace(' ', '', $s);
		return $s;
	}

	public static function dynamic_page($page,$url_rs){

		$page = substr($page, 0,-15);
		// Load dinamic user page

		if(file_exists(SYSTEM_PATH.'app/controllers/DynamicPage.controller.php')){
			require(SYSTEM_PATH.'app/controllers/DynamicPage.controller.php');

			//EJECUTAR CONTROLLER
			$dypage = new DynamicPageController($page);
			return true;
		}else{
			return false;
		}


	} // end dynamic_page()

} // End class fkore