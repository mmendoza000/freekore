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
 **  Programa:    AppController.class.php
 **  Descripcion: Clase controlador de freekore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    26/04/10   mmendoza    Creado.
 **************************************************************/
/**
 *@package AppController
 *@since v0.1 beta
 *@desc  Controller Object
 * */
class AppController {


	public $controller = 'index';
	public $action = 'index';
	public $only_ajax = false;


	public $Load; // load Object
	public $load; // load Object
	public $PermaLinkVars=array();
	public $PermaLinkVarsText = '';

	private $url_processed = ''; // real url used

	/**
	 *@package AppController
	 *@method construct()
	 *@desc Creates the controller properties
	 *@since v0.1 beta
	 * */
	public function __construct($url_params=NULL){



		$this->url_processed = isset($url_params['url_processed'])?$url_params['url_processed']:'';

		// Define Objeto load
		$this->Load = new Load();
		$this->load = new Load();

		// Variables modulo y accion
		$this->action = ($url_params['action']!=NULL) ? $url_params['action'] : $this->action;
		$this->controller = substr($url_params['controller'],0,-10); // Set controller name

		// get PermalinkVars
		$this->getPermaLinkVars();


		// ejecuta el method
		$_action = $this->action;

		if(method_exists($this,$_action) ){
			$this->$_action();
		}else{
			if($GLOBALS['FKORE']['RUNNING']['app']['interactive']==true){

				try{
					if(!method_exists($this,$_action) ){
						throw new FkException('La accion "'.$_action.'" no existe',1);
					}else{
						$this->$_action();
					}
				}catch(FkException $e){

					$e->description = 'El metodo <b>'.$_action.'</b> de la clase <b>'.get_class($this).'</b>
			                    no fue encontrado';				
					$e->solution    = 'Crea el metodo <b>'.$_action.'</b> en la clase <b>'.get_class($this).'</b>
			                    en el archivo <b>'.$url_params['file_controller'].'</b> ';							
					$e->solution_code = '&lt?php<br />class '.get_class($this).' extends AppController {<br />    public function '.$_action.'() {
    
    } // End '.$_action.'
	
}
?&gt;';							 
					$e->show('code_help');

				}
			}else{
				if(file_exists(SYSTEM_PATH.'app/errors/error_404.php')){
					require(SYSTEM_PATH.'app/errors/error_404.php');
				}else{
					require(SYSTEM_PATH.'freekore/sys_messages/page/default_error_404.php');
				}

			}
		}


		//ejecuta la vista

		$display_view = true;

		//Validacion de ajax
		if($this->only_ajax == true){
			if(@$_POST['ajax']!=1){
				// No entrar si no es via ajax
				$display_view = false;
			}
		}


	} // End Construct

	/**
	 *@package AppController
	 *@method index()
	 *@desc Default action
	 *@since v0.1 beta
	 * */
	public function index(){
		// default index
	}
	/**
	 *@package AppController
	 *@method getCurrentPage()
	 *@desc Returns the current page location
	 *@example $this->getCurrentPage retuns  MyController/MyAcction
	 *@return string
	 *@since v0.1 beta
	 * */
	private function getCurrentPage(){
		return $this->controller.'/'.$this->action;
	}

	/**
	 *@package AppController
	 *@method getCurrentUrl()
	 *@desc Returns the Current Url , including permalink vars on it
	 *@example $this->getCurrentUrl retuns  MyController/MyAcction/a/b/c
	 *@return string
	 *@since v0.1
	 * */
	public function getCurrentUrl(){
		/*$get = isset($_GET['url'])?$_GET['url'] : '';
		 return $get;*/
		return $this->url_processed;
	}
	/**
	 *@package AppController
	 *@method getPermaLinkVars()
	 *@desc Returns the $_GET variables from a permanent link
	 *@example $this->getPermaLinkVars() from MyController/MyAcction/var1/var2/var3
	 *         retuns  array('var1','var2','var3');
	 *@return array
	 *@since v0.1
	 * */
	public function getPermaLinkVars(){

		$cur_page=$this->getCurrentPage();
		$tot_str = strlen($cur_page);

		$url = $this->getCurrentUrl();


		$vars = substr($url, $tot_str+1);
		$rs_vars = explode('/', $vars);
		$perma_vars = array();
		foreach ($rs_vars as $k => $v) {
			if(trim($v)!=''){
				$perma_vars[]=$v;
			}
		}

		$this->PermaLinkVars = $perma_vars;
		$this->PermaLinkVarsText = $vars;

		return $perma_vars;
	}

	/**
	 *@package AppController
	 *@method page_title()
	 *@desc sets the Html page title
	 *@deprecated use pageTitle() instead
	 *@since v0.1 beta
	 * */
	public function page_title($p){
		$this->pageTitle($p);
	}
	/**
	 *@package AppController
	 *@method pageTitle()
	 *@desc sets the Html page title
	 *@since v0.3.1 beta
	 * */
	public function pageTitle($p){
		# Tittle pagina
		$p = fk_str_format($p,'html');
		fk_page('TITLE',$p);
	} // page_tittle

	/**
	 *@package AppController
	 *@method page_description()
	 *@desc sets the Html meta description
	 *@deprecated use pageDescription() instead
	 *@since v0.1 beta
	 * */
	public function page_description($p){
		$this->pageDescription($p);
	}
	/**
	 *@package AppController
	 *@method pageDescription()
	 *@desc sets the Html meta description
	 *@since v0.3.1 beta
	 * */
	public function pageDescription($p){
		# Descripcion de la pagina
		$p = fk_str_format($p,'html');
		fk_page('DESCRIPTION',$p);
	}	// page_description
	/**
	 *@package AppController
	 *@method page_keywords()
	 *@desc sets the Html meta keywords
	 *@deprecated use pageKeywords() instead
	 *@since v0.1 beta
	 * */
	public function page_keywords($p){
		$this->pageKeywords($p);
	}
	/**
	 *@package AppController
	 *@method pageKeywords()
	 *@desc sets the Html meta keywords
	 *@since v0.3.1 beta
	 * */
	public function pageKeywords($p){
		# Keywords de la pagina
		$p = fk_str_format($p,'html');
		fk_page('KEYWORDS',$p);
	}	// page_KEYWORDSv
	/**
	 *@package AppController
	 *@method menu()
	 *@desc sets the menu selected option
	 *@example Menu('id_menu','selected_option');
	 *@since v0.1 beta
	 * */
	public function menu($m_id,$curr){
		# Keywords de la pagina
		fk_menu($m_id,$curr);
	}	// page_KEYWORDS

	/**
	 *@package AppController
	 *@method PutContent()
	 *@desc replaces Content into a view
	 *@example  PutContent('{list}' , $MyListContent );
	 *          will replace the tag {list} with the $MyListContent in the
	 *          $this->Load->View('MyView.tpl') execution
	 *@since v0.1 beta
	 * */
	public function putContent($Index , $Content ){

		$this->load->PutContent($Index , $Content );
	}

	public function putArrayContent($array,$prefix=''){

		if($prefix!=''){ $prefix = $prefix.'.';}

		foreach ($array as $k=>$v){
			$this->load->PutContent('{'.$prefix.$k.'}' , $v );
		}
	}
	
	/**
	 *@package AppController
	 *@method urlSegment($position);
	 *@desc returns the url parameter of position:   
	 *@example  example: whith http://my-app.com/controller/method/pos1/pos2/pos3/
	 *          urlSegment(0) returns pos1
	 *@since v4.0 
	 * */
	public function urlSegment($position){
		
		return (isset($this->PermaLinkVars[$position])? $this->PermaLinkVars[$position]:'');
	}

}