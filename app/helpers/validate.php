<?php
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    New BSD License
 */

/**
 * @package validate helper
 * @since   0.1 Beta
 */

if(!function_exists('is_email')){

	function is_email($pMail) {
		if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/", $pMail ) ) {
			return true;
		} else {
			return false;
		}
	}

}

if(!function_exists('is_empty')){

	function is_empty($str) {
		$str = trim($str);
		if ($str!=null && $str!='') {
			return FALSE;
		} else {
			return TRUE;
		}
	}

}

if(!function_exists('is_http_link')){
	function is_http_link($url) {
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		if (eregi($urlregex, $url)) {return TRUE;} else {return FALSE;}
		
	}
}
