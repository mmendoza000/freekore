<?php
function http_uploads(){

	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];

	if($app=='production_app'){
		return HTTP_UPLOADS_PROD;
	}elseif($app=='internet_test_app'){
		return HTTP_UPLOADS_TEST;
	}elseif($app=='interet_dev_app'){
		return HTTP_UPLOADS_DEV;
	}else{
		return HTTP_UPLOADS_LOCAL;
	}

}
function app_config_is($var,$value){
	$res = false;
	if(isset($GLOBALS['FKORE']['config']['APP'][$var])){
		if($GLOBALS['FKORE']['config']['APP'][$var] == $value){
			$res=true;
		}
	}

	return $res;
	
}
function app_running_is($var,$value){

	$res = false;
	if(isset($GLOBALS['FKORE']['RUNNING']['app'][$var])){
		if($GLOBALS['FKORE']['RUNNING']['app'][$var] == $value){
			$res=true;
		}
	}
	
	return $res;

}

function app_is_on_production(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='production_app'){
		return true;
	}else{
		return false;
	}
}

function app_is_on_development(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='development_app'){
		return true;
	}else{
		return false;
	}
}
function app_is_on_dbinternet_dev(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='dbinternet_dev_app'){
		return true;
	}else{
		return false;
	}
}
function app_is_on_interet_dev(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='interet_dev_app'){
		return true;
	}else{
		return false;
	}
}
function app_is_on_testing(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='internet_test_app'){
		return true;
	}else{
		return false;
	}
}

function app_url_is($url){
	return app_running_is('www_server', $url);
}

function app_is_hosted_on_internet(){
	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];
	if($app=='production_app' || $app=='internet_test_app' || $app=='interet_dev_app' ){
		return true;
	}else{
		return false;
	}
}
function uploads_directory(){

	$app = $GLOBALS['FKORE']['config']['APP']['app_activated'];

	if($app=='production_app'){
		return UPLOADS_DIRECTORY_PROD;
	}elseif($app=='internet_test_app'){
		return UPLOADS_DIRECTORY_TEST;
	}elseif($app=='interet_dev_app'){
		return UPLOADS_DIRECTORY_DEV;
	}else{
		return UPLOADS_DIRECTORY_LOCAL;
	}

}



/**
 * @desc devuelve enlace back similar a history.go(-1) en javascript
 * */
function go_back($default_link = NULL){

	$default_link = ($default_link == NULL) ? fk_link() : fk_link().$default_link ;
	$serv_refer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
	$url_refer = get_domain_url($serv_refer);
	$url_this_site = get_domain_url(fk_link());
	$link_refer = ($url_this_site==$url_refer) ? $_SERVER['HTTP_REFERER'] : $default_link;
	return $link_refer;
}

/**
 * @desc genera un transaction key
 * */
function setTransactionKey(){

	$t_key = uniqid();
	$_SESSION['TRANSACTION_KEY'] = $t_key;
	return $t_key;
}
/**
 * @desc comprueba transaction key generado en la ultima operacion
 * */
function confirmTransactionKey($t_key){

	$TransactionKey = isset($_SESSION['TRANSACTION_KEY'])?$_SESSION['TRANSACTION_KEY']:NULL;

	if($t_key==$TransactionKey && $TransactionKey!=NULL ){
		return true;
	}else{
		return false;
	}
}

function get_domain_url($url){
	$arrSrc = array('http://www.','https://www.','http://','https://');
	$url = str_replace($arrSrc, '', $url);
	$url_1 = explode('/', $url);
	$url = $url_1[0];
	return $url;
}

/**
 * @desc verifica la http url y el tipo de navegador y envia a mobil o al descktop
 * */
function verify_http_path(){

	$domain_url = get_domain_url(fk_link());
	$req_host = $_SERVER['HTTP_HOST'];

	if($req_host!=$domain_url){
		header('Location:'.fk_link());
	}

}
function zerofill($entero, $largo){

	$relleno = '';

	if (strlen($entero) < $largo) {$relleno = str_repeat('0', $largo-strlen($entero));}
	return $relleno . $entero;
}