<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo fk_document_title();?></title>
<meta name="keywords" content="<?php echo fk_document_keywords();?>"/>

<?php
fk_css();
fk_js();
?>
<link href="<?php echo fk_theme_url()?>/style.css" rel="stylesheet"	type="text/css" />
<link href="<?php echo fk_theme_url()?>/menu.css" rel="stylesheet"	type="text/css" />


<link href="<?php echo fk_theme_url()?>/ui-core/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="<?php echo fk_theme_url()?>/ui-core/js/bootstrap.min.js"></script>
    <?php /**/?> 

<!--menu-->
<script type="text/javascript" src="<?php echo fk_theme_url()?>/menu.js"></script>
</head>
<body>
<div id="header">
<div id="header-content">
<div id="logo"><a href="<?php echo fk_link();?>">
<img border="0"	src="<?php echo fk_theme_url()?>/img/logo.png" title="Ekkus: Totem Software TM" alt="Ekkus: Totem Software TMk" /></a></div>
</div>

<?php
// obtener menu
fkore :: _use('public/_HTML/themes/'.fk_theme().'/menu-top.php');
?>

</div>

<div id="content-main">
<div id="content">