<?php
class TestController extends AppController{

	public function index(){
			
	} // End index

	public function ws() {

		Load::library('WebService');

		$server = new SoapServer(null, array('uri' => 'http://localhost/freekore/test/ws'));

		// Asignamos la Clase
		$server->setClass('WebService');

		// Atendemos las peticiones
		$server->handle();
		$server->getFunctions();

	} // End ws

	/**
	 * @desc 	Programa que devuelva en un array,
	 *  		el cuadrado de los 200 primeros nœmeros enteros.
	 * @author  Miguel Mendoza
	 *
	 * */
	function cuadrado(){

		$arr = array();
		for ($i = 1;$i < 201;$i ++){
			$arr[$i] = $i*$i;
		}

		return $arr;

	} // end cuadrado

	public function content() {


		$this->putContent('{U}','CONTENIDO DE U');
		$arr['A'] = 'AAAA';
		$arr['B'] = 'ESTO ES B';
		$this->putArrayContent($arr);



		//fk_header();
		$this->load->view('test/content.php');
		//fk_footer();

	} // End content


	public function ws_activar() {

		fk_header();

		?>
<form action="http://beta.wasanga.com/activacion.php" method="post">

	<input type="text" value="activar" name="tipo"> <input type="text"
		value="mmendoza005@hotmail.com" name="email"> <input type="text"
		value="mmendoza004" name="password"> <input type="text"
		value="mmendoza005" name="user_login"> <input type="text"
		value="mmendoza003" name="titulo"> <input type="text" value="10917"
		name="registro_id"> <input type="submit">


</form>
		<?php

		fk_footer();

	} // End ws_activar

	public function usuarios() {
		
		Load::database();
		Load::freekoreLib('appform');
		Load::freekoreLib('applist');
		
		$F = new AppForm('usuarios');
		
		
		fk_header();
		echo $F->render($this->getCurrentUrl());
		fk_footer();

	} // End usuarios



} // End TestController