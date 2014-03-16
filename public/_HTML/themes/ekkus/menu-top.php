<?php
$cnt = 0;

$cnt++;
$ArrMnu[$cnt]['label'] = 'Inicio';
$ArrMnu[$cnt]['sel_mnu'] = 'inicio';
$ArrMnu[$cnt]['link'] = fk_link();
$ArrMnu[$cnt]['submenu']=array();

/* Example other menu option*/
/*$cnt++;
$ArrMnu[$cnt]['label'] = 'Other';
$ArrMnu[$cnt]['sel_mnu'] = 'other';
$ArrMnu[$cnt]['link'] = fk_link('other');
$ArrMnu[$cnt]['submenu']=array();*/

function printSubMenu($arr){

	if(count($arr)>0){
		?>
<ul class="submenu">
<?php
foreach ($arr as $li){
	?>
	<li><a href="<?php echo $li['link'] ?>"><?php echo $li['label'] ?></a></li>
	<?php
}
?>
</ul>
<?php
	}

}

?>
<div id="dv-mnu">
<ul id="menu">
<?php

foreach ($ArrMnu as $li){
	$class = '';

	if (@MENU_1==$li['sel_mnu']){
		$class = 'curr';
	}
	$ico = '';
	if(isset($li['ico'])){
		$ico = $li['ico'];
	}

	$onClick = isset($li['onclick']) ? $li['onclick']: '';

	?>
	<li class="menupop <?php echo $class ?>"><a class="subm_link"
		href="<?php echo $li['link'] ?>" onclick="<?php echo $onClick ?>"> <?php echo $ico;?>
		<?php echo $li['label'] ?></a> <?php
		printSubMenu($li['submenu']);
		?></li>
		<?php
}
?>
</ul>
</div>
<script type="text/javascript"> 
jQuery(document).ready(function(){
	jQuery( 'a.subm_link' ).hover( function( i ) {
		jQuery( 'a.subm_link' ).parent().removeClass( 'on' );
		jQuery( this ).parent().addClass( 'on' );
	});
	jQuery( '.submenu' ).hover( function(){}, function(){
		jQuery( '.menupop' ).removeClass( 'on' );
	});
	jQuery( '#menu2' ).hover( function(){}, function(){
		jQuery( '.menupop' ).removeClass( 'on' );
	});
});
</script>
