<?php
class DynamicPageController{

	public function __construct($page){
		$this->index($page);
	}

	public function index($page){

		echo "hola pagina dinamica:";
		echo $page;
		
		
		/*
		  $db = new db();
		$rs = $db->result('select count(*)  as tot from a_usuarios where usuario="'.$page.'" ');

		if($rs[0]['tot']==1){

			$rs2 = $db->result('select idusuario from a_usuarios where usuario="'.$page.'" ');
			$id_usuario =$rs2[0]['idusuario'];
			include SYSTEM_PATH.'app/controllers/directorio.controller.php';
			$newpage = new DirectorioController();
			$newpage->detalleMedico($id_usuario);
			//echo 'hola';
		}else{
			if($page=='wiki'){
				$termino = fk_get('url');
				header("Location:http://es.wikipedia.org/".$termino);
					
			}else{
				// Pagina no existe
				include SYSTEM_PATH.'app/errors/error_404.php';
			}
				

		}
		 */
		




			
	} // End index

}