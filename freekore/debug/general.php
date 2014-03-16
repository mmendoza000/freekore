<?php
/**
 * FreeKore Php Framework
 * Version: 0.3.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2011 Freekore PHP Team
 * @license    GNU GPL License
 */


// pa() = PRINT ARRAY
/**
 * 
 * @desc       formated print_r() function, by default prints $_POST array   
 * @since      0.3.1 Beta
 */
function pa($ARR = NULL){
	if($ARR!=NULL){
		$printArray = $ARR;
	}else{$printArray = $_POST;}
	$uniqId=mt_rand(100,1000000);
	if(count($printArray)>0){
		echo '<div id="DEBUG_POST_INFO'.$uniqId.'" class="message" >
  <a href="javaScript:oculta(\'DEBUG_POST_INFO'.$uniqId.'\');">[Show|Hide]</a>
  <pre id="data_DEBUG_POST_INFO'.$uniqId.'">';
		print_r($printArray);
		echo '</pre>
  
  </div>';  
	}
}