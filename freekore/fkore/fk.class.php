<?php
class fk{

	static function blank_header(){
		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo fk_document_title();?></title>
		<?php echo fk_css();?>
		<?php echo fk_js();?>
</head>

<body>
		<?php

	} // blank_header()
	static function blank_footer(){
		?>
</body>
</html>
		<?php

	} // blank_header()
	static function ui_dialog_1($id,$title,$btn_name,$content=''){
		$onclic_btn = '';
		
		$html = '<a href="javascript:void(0)" class="b" id="opener-'.$id.'">'.$btn_name.'</button>
<div id="dialog-'.$id.'" title="'.$title.'" style="width:900px;height:800px;"> 
	<p>'.$content.'</p> 
</div> 
<script> 
	$(function() {
		$( "#dialog-'.$id.'" ).dialog({
			autoOpen: false,
			width: 800,
			height: 430,
			modal: true
			
		});
		$( "#opener-'.$id.'" ).click(function() {
			$( "#dialog-'.$id.'" ).dialog( "open" );
			return false;
		});
	});
</script>';
		
		
		return $html;
	
	}

}