<div >

<hr>
<H2>BOOSTRAP</H2>
<div class="progress progress-striped active">
  <div class="bar" style="width: 40%;"></div>
</div>
<div class="btn-group">
  <a class="btn btn-primary" href="#"><i class="icon-user icon-white"></i> User</a>
  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="#"><i class="icon-pencil"></i> Edit</a></li>
    <li><a href="#"><i class="icon-trash"></i> Delete</a></li>
    <li><a href="#"><i class="icon-ban-circle"></i> Ban</a></li>
    <li class="divider"></li>
    <li><a href="#"><i class="i"></i> Make admin</a></li>
  </ul>
</div>

<div class="btn-toolbar">
  <div class="btn-group">
    <a class="btn" href="#"><i class="icon-align-left"></i></a>
    <a class="btn" href="#"><i class="icon-align-center"></i></a>
    <a class="btn" href="#"><i class="icon-align-right"></i></a>
    <a class="btn" href="#"><i class="icon-align-justify"></i></a>
  </div>
</div>

<form class="form-search">
  <input type="text" class="input-medium search-query">
  <button type="submit" class="btn">Search</button>
</form>
<div class="input-prepend">
  <div class="btn-group">
    <button class="btn dropdown-toggle" data-toggle="dropdown">
      Action
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
      <li><a tabindex="-1" href="#">Action</a></li>
  <li><a tabindex="-1" href="#">Another action</a></li>
  <li><a tabindex="-1" href="#">Something else here</a></li>
  <li class="divider"></li>
  <li><a tabindex="-1" href="#">Separated link</a></li>
    </ul>
  </div>
  <input class="span2" id="prependedDropdownButton" type="text">
</div>

<button type="button" class="btn  "><span class="icon-envelope"></span> Enviar por correo</button>
<button type="button" class="btn ">Primary</button>
<button type="button" class="btn btn-primary btn-large">Primary</button>
<button type="button" class="btn btn-info btn-small">Primary</button>
<button type="button" class="btn btn-success btn-mini">Primary</button>
<button type="button" class="btn btn-warning">Primary</button>
<button type="button" class="btn btn-danger">Primary</button>
<button type="button" class="btn btn-inverse">Primary</button>
<button type="button" class="btn btn-link">Primary</button>


<hr>




<h1><?php echo 'Freekore Versi&oacute;n '.fkore::$version; ?></h1>

<a href="<?php echo fk_link('usuarios/?crear=true&otra=2')?>">Crear usuarios</a>
<a href="<?php echo fk_link('usuarios/modificar/1/?var1=2&var2=2')?>">Modificar 1 usuarios</a>
<a href="<?php echo fk_link('usuarios/eliminar/1/')?>">Eliminar usuario</a>
<a href="<?php echo fk_link('test/usuarios/1/')?>">Pruebas</a>
<div class="fk-ok-message">
<h3>
 <?php
  echo __('Felicidades! est&aacute;s usando freekore');
  ?>
  <br>
</h3>
</div>
<?php 

echo fk_message('ok', 'Ok <b>negritas</b>');
echo fk_message('error', 'Error <b>negritas</b>');

echo fk_message('warning', 'Warning <b>negritas</b>');
echo fk_message('info', 'Info <b>negritas</b>');
echo fk_message('alert', 'Alert <b>negritas</b>');
?>
<div class="message ok">
<?php echo 'Este archivo se encuentra en: <b>app/views/index.php</b>';?>
</div>

<div class="message">
<b>Documentaci&oacute;n:</b> Para comenzar con freekore puedes leer el tutorial <a href="http://freekore.com/hola/mundo" target="blank">Hola Mundo</a> 
</div>

<div class="message warning">
Este es un mensaje <b>Tipo warning</b> del template por defecto 
</div>

<div class="message alert">
Este es un mensaje <b>Tipo alerta</b> del template por defecto 
</div>



</div>

