<?php
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 */


/**
 * @package    fk_ajax
 * @desc       Ajax Functions
 * @since      0.3.1 Beta
 */

class ajax{

	public $type = '';
	public $place = '';
	public $params = array();

	/**
	 * @package    fk_ajax
	 * @since      0.1 Beta
	 * @desc       Create an Ajax object
	 * @method __construct
	 * @example 1) $fx = new fk_ajax('form','url:MyController/MyAction;form:myformId');
	 *          2) $fx = new fk_ajax('url','url:MyController/MyAction');
	 *          3) $fx = new fk_ajax('url','url:MyController/MyAction;args:x1=val1&x2=val2;url_after:Other/Url;insert_mode:top');
	 *          4) $fx = new fk_ajax('url','url:MyController/MyAction;url_after:Other/Url;');
	 *          5) $fx = new fk_ajax('url','url:MyController/MyAction;insert_mode:bottom'); insert mode [bottom|top]
	 *
	 *
	 *
	 *
	 */
	function __construct($type, $place ,$params ){

		$this->type = $type;
		$this->place = $place;
		$p = explode(';',$params);

		foreach($p as $k=>$v){
			if(trim($v)!=''){

				$var_val = explode(':',$v);
				$var = $var_val[0];
				$val = $var_val[1];
				$this->params[$var]=$val;
			}

		}


	} // end __construct()
	/**
	* @package    fk_ajax
	* @since      v0.1 Beta
	* @desc       Create an Ajax object
	* @method render()
	* @example 1) OpenJavaScriptTag $fx -> render(); EndJavaScriptTag
	*          2) htmlObject onclic|onmouseout| other javascript event :$fx -> render()
	* */
	public function render(){
		$render_rs = '';
		if($this->type == 'url'){

		 $render_rs = "
		      var pArgs = {pDiv:'".$this->place."', 
						  pUrl:'".$this->params['url']."',
						  pArgs:'".@$this->params['args']."', 
						  pUrlAfter:'".@$this->params['url_after']."', 
						  insertMode:'".@$this->params['insert_mode']."'
						  };
			  fk_ajax_exec(pArgs);
			  ";
		}
		if($this->type == 'submit'){
			$render_rs = "
		      var pArgs = {pDiv:'".$this->place."', 
						  pUrl:'".$this->params['url']."',
						  pForm:'".$this->params['form']."',
						  pArgs:'".@$this->params['args']."',
						  pUrlAfter:'".@$this->params['url_after']."', 
						  insertMode:'".@$this->params['insert_mode']."'
						  };
			  fk_ajax_submit(pArgs);
			  ";

		}
		if($this->type == 'json'){
			$render_rs = "
		      var pArgs = {pUrl:'".$this->params['url']."',
						  pArgs:'".@$this->params['args']."'};
			  fk_ajax_json(pArgs,false);
			  ";

		}
		if($this->type == 'json-submit'){
			$render_rs = "var pArgs = {pUrl:'".$this->params['url']."',
		                   pForm:'".$this->params['form']."',
						   pArgs:'".@$this->params['args']."'};
			  fk_ajax_json(pArgs,true);";

		}

		return  $render_rs;

	} // end render()

} // End Class